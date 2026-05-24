<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\AgendaReminder;
use App\Models\FcmToken;
use App\Models\NotifikasiPendaftar;
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

        // Kirim via WhatsApp jika diperlukan
        if (in_array($subscriber->channel_preference, ['whatsapp', 'both']) && !$subscriber->whatsapp_sent) {
            if ($subscriber->phone_number && $this->fonnte->sendAgendaReminder($subscriber->phone_number, $agenda, $type)) {
                $subscriber->markWhatsappSent();
                $success = true;
            }
        }

        // Kirim via FCM jika diperlukan
        if (in_array($subscriber->channel_preference, ['fcm', 'both']) && !$subscriber->fcm_sent) {
            // Cari FCM token yang subscribe ke agenda ini
            $fcmTokens = FcmToken::active()
                ->whereJsonContains('subscribed_agendas', $agenda->id)
                ->pluck('token')
                ->toArray();

            foreach ($fcmTokens as $token) {
                if ($this->fcm->sendAgendaReminder($token, $agenda, $type)) {
                    $success = true;
                }
            }

            if ($success) {
                $subscriber->markFcmSent();
            }
        }

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
            ]
        );
    }

    /**
     * Register/update FCM token and subscribe to broadcast topic
     */
    public function registerFcmToken(string $token, ?string $deviceName = null): FcmToken
    {
        $fcmToken = FcmToken::updateOrCreate(
            ['token' => $token],
            [
                'device_name' => $deviceName ?? 'Web Browser',
                'is_active'   => true,
            ]
        );

        // Auto-subscribe to broadcast topic for general announcements
        $this->fcm->subscribeToTopic($token, 'agenda-updates');

        return $fcmToken;
    }

    /**
     * Subscribe FCM token ke agenda tertentu
     */
    public function subscribeFcmToAgenda(string $token, int $agendaId): bool
    {
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
     */
    public function subscribeToMultipleAgendas(array $data): array
    {
        $results = [];
        $agendaIds = $data['agenda_ids'] ?? [];
        $channel = $data['channel'] ?? 'whatsapp';
        $phoneNumber = $data['phone_number'] ?? null;
        $fcmToken = $data['fcm_token'] ?? null;

        \Log::info('subscribeToMultipleAgendas() - START', [
            'channel' => $channel,
            'agenda_ids' => $agendaIds,
            'agenda_ids_count' => count($agendaIds),
            'phone_number' => $phoneNumber ? substr($phoneNumber, 0, 6) . '***' : null,
            'fcm_token' => $fcmToken ? substr($fcmToken, 0, 20) . '...' : null,
            'will_save_whatsapp' => in_array($channel, ['whatsapp', 'both']) && !empty($phoneNumber),
            'will_save_fcm' => in_array($channel, ['fcm', 'both']) && !empty($fcmToken),
        ]);

        foreach ($agendaIds as $agendaId) {
            \Log::info('Processing agenda', ['agenda_id' => $agendaId]);
            
            try {
                // Untuk WhatsApp/both, simpan ke notifikasi_pendaftar
                if (in_array($channel, ['whatsapp', 'both']) && !empty($phoneNumber)) {
                    \Log::info('Saving to notifikasi_pendaftar', [
                        'agenda_id' => $agendaId,
                        'phone' => substr($phoneNumber, 0, 6) . '***',
                    ]);
                    
                    $subscriber = $this->subscribe([
                        'agenda_id'    => $agendaId,
                        'phone_number' => $phoneNumber,
                        'nama'         => $data['nama'] ?? null,
                        'channel'      => $channel,
                        'fcm_token'    => $fcmToken, // Pass FCM token too
                    ]);
                    
                    \Log::info('Subscriber saved', [
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
                // Don't re-throw, continue with other agendas
            }
        }

        \Log::info('subscribeToMultipleAgendas() - END', [
            'results_count' => count($results),
        ]);

        return $results;
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
}
