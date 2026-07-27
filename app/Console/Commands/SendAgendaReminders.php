<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\FcmToken;
use App\Services\FcmSender;
use App\Services\FonnteSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAgendaReminders extends Command
{
    protected $signature   = 'agenda:send-reminders';
    protected $description = 'Kirim pengingat global WhatsApp dan FCM 6 jam & 1 jam sebelum agenda.';

    public function handle(FcmSender $fcm, FonnteSender $fonnte): int
    {
        $now  = now();
        $sent = 0;

        $this->info('===========================================');
        $this->info('  AGENDA GLOBAL REMINDER SCHEDULER');
        $this->info('===========================================');
        $this->info("Waktu server: {$now->format('Y-m-d H:i:s')} WIB");
        $this->newLine();

        $agendas = Agenda::query()
            ->where('status', '!=', 'dibatalkan')
            ->where('waktu_mulai', '>', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('reminder_6h_sent_at')
                  ->orWhereNull('reminder_1h_sent_at');
            })
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $this->info("Agenda aktif yang memerlukan pengingat: {$agendas->count()}");
        $this->newLine();

        foreach ($agendas as $agenda) {
            $diffMinutes = $now->diffInMinutes($agenda->waktu_mulai, false);

            // 1 hour reminder window: between 1h00 and 0h45 before start
            if ($diffMinutes <= 60 && $diffMinutes >= 45 && is_null($agenda->reminder_1h_sent_at)) {
                $this->line("> {$agenda->perihal_kegiatan} — pengingat 1 jam");
                $sent += $this->sendReminder($fcm, $fonnte, $agenda, '1h');
                $agenda->update(['reminder_1h_sent_at' => $now]);
                $this->newLine();
                continue;
            }

            // 6 hours reminder window: between 6h00 and 5h45 before start
            if ($diffMinutes <= 360 && $diffMinutes >= 345 && is_null($agenda->reminder_6h_sent_at)) {
                $this->line("> {$agenda->perihal_kegiatan} — pengingat 6 jam");
                $sent += $this->sendReminder($fcm, $fonnte, $agenda, '6h');
                $agenda->update(['reminder_6h_sent_at' => $now]);
                $this->newLine();
            }
        }

        $this->info("===========================================");
        $this->info("Total notifikasi terkirim: {$sent}");
        $this->info("===========================================");

        return self::SUCCESS;
    }

    private function sendReminder(FcmSender $fcm, FonnteSender $fonnte, Agenda $agenda, string $type): int
    {
        $sent = 0;

        // FCM: send to all active browser tokens (global subscription)
        $activeTokens = FcmToken::active()->where('is_active', true)->pluck('token');
        $this->line("  FCM subscribers: {$activeTokens->count()}");

        foreach ($activeTokens as $token) {
            try {
                if ($fcm->sendAgendaReminder($token, $agenda, $type)) {
                    $sent++;
                    Log::info('Global FCM reminder sent', [
                        'agenda_id' => $agenda->id,
                        'type' => $type,
                        'token_prefix' => substr($token, 0, 20),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Global FCM reminder failed', [
                    'agenda_id' => $agenda->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // WhatsApp: send to personal + OPD bulk subscribers
        $waSubscribers = FcmToken::whatsappSubscribers()
            ->whereNotNull('whatsapp_phone')
            ->get();
        $this->line("  WhatsApp subscribers: {$waSubscribers->count()}");

        foreach ($waSubscribers as $subscriber) {
            try {
                if ($fonnte->sendAgendaReminder($subscriber->whatsapp_phone, $agenda, $type)) {
                    $sent++;
                    Log::info('Global WhatsApp reminder sent', [
                        'agenda_id' => $agenda->id,
                        'type' => $type,
                        'phone_prefix' => substr($subscriber->whatsapp_phone, 0, 8) . '***',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Global WhatsApp reminder failed', [
                    'agenda_id' => $agenda->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $sent;
    }
}
