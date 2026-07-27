<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\AgendaReminder;
use App\Models\FcmToken;
use App\Models\NotifikasiPendaftar;
use App\Models\OpdGroup;
use App\Models\OpdGroupMember;
use Illuminate\Support\Collection;

class AgendaReminderService
{
    public function __construct(
        private FonnteSender $fonnte,
        private FcmSender $fcm
    ) {}

    /**
     * Kirim notifikasi ke subscriber berdasarkan channel preference
     */
    public function sendToSubscriber(NotifikasiPendaftar $subscriber, string $type = 'immediate'): bool
    {
        $agenda = $subscriber->agenda;
        $success = false;
        
        \Log::info('sendToSubscriber() START', [
            'subscriber_id' => $subscriber->id,
            'agenda_id' => $agenda?->id,
            'channel' => $subscriber->channel_preference,
            'phone' => $subscriber->phone_number,
            'type' => $type,
        ]);

        // Kirim via WhatsApp jika diperlukan
        if (in_array($subscriber->channel_preference, ['whatsapp', 'both'])) {
            \Log::info('Attempting WhatsApp send', ['phone' => $subscriber->phone_number]);
            
            if ($subscriber->phone_number) {
                $waResult = $this->fonnte->sendAgendaReminder($subscriber->phone_number, $agenda, $type);
                \Log::info('WhatsApp result', ['success' => $waResult]);
                
                if ($waResult) {
                    $subscriber->markWhatsappSent();
                    $success = true;
                }
            } else {
                \Log::warning('No phone number for WhatsApp');
            }
        }

        // Kirim ke semua anggota grup OPD
        if ($subscriber->channel_preference === 'group') {
            $groupId = str_replace('group:', '', $subscriber->phone_number);
            $group = OpdGroup::find($groupId);

            if ($group && $group->is_active) {
                $members = $group->members()->get();
                \Log::info('Sending to OPD group members', [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'members_count' => $members->count(),
                ]);

                // Kirim ke WhatsApp group (jika group_id tersedia)
                if ($group->group_id) {
                    $groupResult = $this->fonnte->sendAgendaReminderToGroup($group->group_id, $agenda, $type);
                    \Log::info('WhatsApp group send result', ['success' => $groupResult]);
                    if ($groupResult) {
                        $success = true;
                    }
                }

                // Kirim juga ke masing-masing anggota secara individual
                foreach ($members as $member) {
                    $waResult = $this->fonnte->sendAgendaReminder($member->phone_number, $agenda, $type);
                    if ($waResult) {
                        $success = true;
                    }
                }

                if ($success) {
                    $subscriber->markWhatsappSent();
                }
            } else {
                \Log::warning('OPD group not found or inactive', ['group_id' => $groupId]);
            }
        }

        // Kirim via FCM jika diperlukan
        if (in_array($subscriber->channel_preference, ['fcm', 'both'])) {
            \Log::info('Attempting FCM send', ['agenda_id' => $agenda->id]);
            
            // Cari FCM token yang subscribe ke agenda ini (coba kedua format int dan string)
            $fcmTokens = FcmToken::active()
                ->where(function ($q) use ($agenda) {
                    $q->whereJsonContains('subscribed_agendas', $agenda->id)
                      ->orWhereJsonContains('subscribed_agendas', (string) $agenda->id);
                })
                ->pluck('token')
                ->toArray();
            
            \Log::info('FCM tokens found', ['count' => count($fcmTokens)]);

            $fcmSuccess = false;
            foreach ($fcmTokens as $token) {
                \Log::info('Sending to FCM token', ['token_prefix' => substr($token, 0, 20)]);
                if ($this->fcm->sendAgendaReminder($token, $agenda, $type)) {
                    $fcmSuccess = true;
                }
            }

            if ($fcmSuccess) {
                $subscriber->markFcmSent();
                $success = true;
            }
        }
        
        \Log::info('sendToSubscriber() END', ['success' => $success]);

        return $success;
    }

    /**
     * Kirim notifikasi ke AgendaReminder (admin/internal)
     */
    public function sendToReminder(AgendaReminder $reminder): bool
    {
        if ($reminder->is_sent) {
            return true;
        }

        $agenda = $reminder->agenda;
        $success = false;

        if ($reminder->channel === 'whatsapp') {
            $success = $this->fonnte->sendAgendaReminder($reminder->phone_number, $agenda);
        } elseif ($reminder->channel === 'fcm') {
            // Untuk FCM channel, cari token yang terkait
            $tokens = FcmToken::active()
                ->whereJsonContains('subscribed_agendas', $agenda->id)
                ->pluck('token')
                ->toArray();

            foreach ($tokens as $token) {
                if ($this->fcm->sendAgendaReminder($token, $agenda)) {
                    $success = true;
                    break;
                }
            }
        }

        if ($success) {
            $reminder->markAsSent();
        }

        return $success;
    }

    /**
     * Kirim notifikasi bulk ke multiple agenda untuk satu nomor/subscriber
     */
    public function sendBulkToPhone(string $phone, Collection $agendas, string $channel = 'whatsapp', string $type = 'immediate'): bool
    {
        if ($channel === 'whatsapp' || $channel === 'both') {
            return $this->fonnte->sendBulkAgendaReminder($phone, $agendas->all(), $type);
        }

        return false;
    }

    /**
     * Subscribe user ke agenda dengan channel preference
     */
    public function subscribe(array $data): NotifikasiPendaftar
    {
        \Log::info('subscribe() payload', $data);
        $fcmTokenId = null;
        if (!empty($data['fcm_token'])) {
            $fcmToken = $this->registerFcmToken($data['fcm_token']);
            $fcmTokenId = $fcmToken->id;
        }

        return NotifikasiPendaftar::updateOrCreate(
            [
                'agenda_id'    => $data['agenda_id'],
                'phone_number' => $data['phone_number'] ?? null,
            ],
            [
                'nama'               => $data['nama'] ?? null,
                'fcm_token_id'       => $fcmTokenId,
                'channel_preference' => $data['channel'] ?? 'whatsapp',
                'reminder_minutes'   => $data['reminder_minutes'] ?? 60,
                'is_immediate'       => false, // Always false - no immediate mode
            ]
        );
    }

    /**
     * Register/update FCM token for global subscription.
     * Optional WhatsApp opt-in details.
     */
    public function registerFcmToken(string $token, ?string $deviceName = null, array $whatsapp = []): FcmToken
    {
        if ($this->isPlaceholderFcmToken($token)) {
            throw new \InvalidArgumentException('Token browser tidak valid. Aktifkan ulang notifikasi sampai token Firebase berhasil dibuat.');
        }

        $fcmToken = FcmToken::updateOrCreate(
            ['token' => $token],
            [
                'device_name' => $deviceName ?? 'Web Browser',
                'is_active'   => true,
            ]
        );

        if (!empty($whatsapp['opt_in']) && !empty($whatsapp['phone'])) {
            $fcmToken->update([
                'whatsapp_opt_in' => true,
                'whatsapp_name'   => $whatsapp['name'] ?? null,
                'whatsapp_phone'  => $this->normalizePhone($whatsapp['phone']),
            ]);
        } else {
            $fcmToken->update([
                'whatsapp_opt_in' => false,
                'whatsapp_name'   => null,
                'whatsapp_phone'  => null,
            ]);
        }

        return $fcmToken;
    }

    private function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        $cleaned = ltrim($cleaned, '0');
        if (!str_starts_with($cleaned, '62')) {
            $cleaned = '62' . $cleaned;
        }
        return $cleaned;
    }

    /**
     * Subscribe FCM token ke agenda tertentu
     */
    public function subscribeFcmToAgenda(string $token, int $agendaId): bool
    {
        if ($this->isPlaceholderFcmToken($token)) {
            throw new \InvalidArgumentException('Token browser tidak valid untuk agenda ini.');
        }

        \Log::info('subscribeFcmToAgenda()', ['agenda_id' => $agendaId, 'token_prefix' => substr($token, 0, 12)]);
        $fcmToken = FcmToken::findByToken($token);

        if (!$fcmToken) {
            $fcmToken = $this->registerFcmToken($token);
        }

        $fcmToken->subscribeToAgenda($agendaId);
        return true;
    }

    /**
     * Subscribe ke multiple agenda sekaligus
     * Semua reminder akan dikirim oleh scheduler sesuai waktu yang dipilih
     */
    public function subscribeToMultipleAgendas(array $data): array
    {
        $results = [];
        $agendaIds = $data['agenda_ids'] ?? [];
        $channel = $data['channel'] ?? 'whatsapp';
        $phoneNumber = $data['phone_number'] ?? null;
        $fcmToken = $data['fcm_token'] ?? null;
        $opdGroupId = $data['opd_group_id'] ?? null;
        $reminderMinutes = $data['reminder_minutes'] ?? 60;

        \Log::info('subscribeToMultipleAgendas() - START', [
            'channel' => $channel,
            'agenda_ids' => $agendaIds,
            'agenda_ids_count' => count($agendaIds),
            'phone_number' => $phoneNumber ? substr($phoneNumber, 0, 6) . '***' : null,
            'fcm_token' => $fcmToken ? substr($fcmToken, 0, 20) . '...' : null,
            'opd_group_id' => $opdGroupId,
            'reminder_minutes' => $reminderMinutes,
            'will_save_whatsapp' => in_array($channel, ['whatsapp', 'both']) && !empty($phoneNumber),
            'will_save_fcm' => in_array($channel, ['fcm', 'both']) && !empty($fcmToken),
            'will_save_group' => $channel === 'group' && !empty($opdGroupId),
        ]);

        foreach ($agendaIds as $agendaId) {
            \Log::info('Processing agenda', ['agenda_id' => $agendaId]);

            try {
                // Untuk WhatsApp/both, simpan ke notifikasi_pendaftar
                if (in_array($channel, ['whatsapp', 'both']) && !empty($phoneNumber)) {
                    \Log::info('Saving to notifikasi_pendaftar', [
                        'agenda_id' => $agendaId,
                        'phone' => substr($phoneNumber, 0, 6) . '***',
                        'reminder_minutes' => $reminderMinutes,
                    ]);

                    $subscriber = $this->subscribe([
                        'agenda_id'       => $agendaId,
                        'phone_number'    => $phoneNumber,
                        'nama'            => $data['nama'] ?? null,
                        'channel'         => $channel,
                        'fcm_token'       => $fcmToken,
                        'reminder_minutes'=> $reminderMinutes,
                    ]);

                    \Log::info('Subscriber saved', [
                        'subscriber_id' => $subscriber->id,
                        'agenda_id' => $subscriber->agenda_id,
                    ]);

                    $results[] = $subscriber;
                }

                // Untuk group channel, simpan ke notifikasi_pendaftar dengan opd_group_id
                if ($channel === 'group' && !empty($opdGroupId)) {
                    \Log::info('Saving to notifikasi_pendaftar (group)', [
                        'agenda_id' => $agendaId,
                        'opd_group_id' => $opdGroupId,
                        'reminder_minutes' => $reminderMinutes,
                    ]);

                    $subscriber = NotifikasiPendaftar::updateOrCreate(
                        [
                            'agenda_id'    => $agendaId,
                            'phone_number' => 'group:' . $opdGroupId, // Prefix to identify group subscribers
                        ],
                        [
                            'nama'               => 'Grup OPD',
                            'fcm_token_id'       => null,
                            'channel_preference' => 'group',
                            'reminder_minutes'   => $reminderMinutes,
                            'is_immediate'       => false,
                        ]
                    );

                    \Log::info('Group subscriber saved', [
                        'subscriber_id' => $subscriber->id,
                        'agenda_id' => $subscriber->agenda_id,
                    ]);

                    $results[] = $subscriber;
                }

                // Untuk FCM/both, subscribe token ke agenda
                if (in_array($channel, ['fcm', 'both']) && !empty($fcmToken)) {
                    \Log::info('Subscribing FCM to agenda', ['agenda_id' => $agendaId]);
                    $this->subscribeFcmToAgenda($fcmToken, $agendaId);
                }
            } catch (\Throwable $e) {
                \Log::error('subscribeToMultipleAgendas item FAILED', [
                    'agenda_id' => $agendaId,
                    'channel' => $channel,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        \Log::info('subscribeToMultipleAgendas() - END', [
            'results_count' => count($results),
        ]);

        return $results;
    }

    /**
     * Kirim notifikasi IMMEDIATE — untuk reminder <= 15 menit
     * Langsung kirim tanpa nunggu scheduler
     */
    public function sendImmediate(NotifikasiPendaftar $subscriber): bool
    {
        if ($subscriber->immediate_sent) {
            \Log::info('Immediate already sent, skipping', ['subscriber_id' => $subscriber->id]);
            return true;
        }

        $agenda = $subscriber->agenda;
        if (!$agenda || !$agenda->waktu_mulai) {
            \Log::warning('Cannot send immediate - no agenda', ['subscriber_id' => $subscriber->id]);
            return false;
        }

        $success = false;

        // Kirim WhatsApp
        if (in_array($subscriber->channel_preference, ['whatsapp', 'both']) && $subscriber->phone_number) {
            $waResult = $this->fonnte->sendAgendaReminder($subscriber->phone_number, $agenda, 'immediate');
            if ($waResult) {
                $subscriber->markWhatsappSent();
                $success = true;
            }
        }

        // Kirim FCM
        if (in_array($subscriber->channel_preference, ['fcm', 'both']) && $subscriber->fcm_token_id) {
            $fcmToken = \App\Models\FcmToken::find($subscriber->fcm_token_id);
            if ($fcmToken && $fcmToken->token) {
                $fcmResult = $this->fcm->sendAgendaReminder($fcmToken->token, $agenda, 'immediate');
                if ($fcmResult) {
                    $subscriber->markFcmSent();
                    $success = true;
                }
            }
        }

        // Tandai immediate sudah dikirim
        $subscriber->update([
            'immediate_sent' => true,
            'immediate_sent_at' => now(),
        ]);

        \Log::info('Immediate sent', [
            'subscriber_id' => $subscriber->id,
            'success' => $success,
        ]);

        return $success;
    }

    /**
     * Kirim immediate confirmation setelah subscribe
     */
    public function sendImmediateConfirmation(array $data): bool
    {
        $agendaIds = $data['agenda_ids'] ?? [];
        $channel = $data['channel'] ?? 'whatsapp';
        $agendas = Agenda::whereIn('id', $agendaIds)->get();

        if ($agendas->isEmpty()) {
            return false;
        }

        $success = false;

        // Kirim WhatsApp jika ada nomor
        if (in_array($channel, ['whatsapp', 'both']) && !empty($data['phone_number'])) {
            $success = $this->fonnte->sendBulkAgendaReminder($data['phone_number'], $agendas->all(), 'immediate');
        }

        // Kirim FCM jika ada token
        if (in_array($channel, ['fcm', 'both']) && !empty($data['fcm_token'])) {
            foreach ($agendas as $agenda) {
                if ($this->fcm->sendAgendaReminder($data['fcm_token'], $agenda, 'immediate')) {
                    $success = true;
                }
            }
        }

        return $success;
    }

    /**
     * Check if services are properly configured
     */
    public function getServiceStatus(): array
    {
        return [
            'whatsapp' => $this->fonnte->isConfigured(),
            'fcm'      => $this->fcm->isConfigured(),
        ];
    }

    private function isPlaceholderFcmToken(string $token): bool
    {
        return str_starts_with($token, 'browser-notification-') || strlen(trim($token)) < 40;
    }
}
