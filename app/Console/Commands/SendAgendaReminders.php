<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\NotifikasiPendaftar;
use App\Services\AgendaReminderService;
use App\Services\FcmSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAgendaReminders extends Command
{
    protected $signature   = 'agenda:send-reminders';
    protected $description = 'Kirim pengingat WhatsApp dan FCM berdasarkan waktu yang dipilih subscriber.';

    public function handle(AgendaReminderService $service, FcmSender $fcm): int
    {
        $now  = now();
        $sent = 0;

        $this->info('===========================================');
        $this->info('  AGENDA REMINDER SCHEDULER');
        $this->info('===========================================');
        $this->info("Waktu server: {$now->format('Y-m-d H:i:s')} WIB");
        $this->newLine();

        // Cari subscribers yang perlu dikirim notifikasi
        $subscribers = NotifikasiPendaftar::with('agenda')
            ->whereHas('agenda', function ($q) use ($now) {
                $q->where('status', '!=', 'dibatalkan')
                  ->where('waktu_mulai', '>', $now); // Hanya agenda yang belum mulai
            })
            ->where(function ($q) {
                $q->where('whatsapp_sent', false)
                  ->orWhere('fcm_sent', false);
            })
            ->get();

        $this->info("Total subscribers pending: {$subscribers->count()}");
        $this->newLine();

        $readyToSend = [];
        $upcoming = [];

        foreach ($subscribers as $subscriber) {
            $agenda = $subscriber->agenda;
            if (!$agenda || !$agenda->waktu_mulai) continue;

            $reminderMinutes = $subscriber->reminder_minutes ?? 60;
            $reminderTime = $agenda->waktu_mulai->copy()->subMinutes($reminderMinutes);
            $agendaStarted = $now->gte($agenda->waktu_mulai);
            
            // Toleransi window untuk reminder
            $windowStart = $reminderTime->copy()->subMinutes(10);
            $windowEnd = $reminderTime->copy()->addMinutes(10);
            
            $minutesUntilReminder = $now->diffInMinutes($reminderTime, false);
            $minutesSinceAgendaStart = $agendaStarted ? $now->diffInMinutes($agenda->waktu_mulai) : 0;

            if ($now->between($windowStart, $windowEnd)) {
                // Tepat waktu - dalam window
                $readyToSend[] = $subscriber;
            } elseif ($now->lt($windowStart)) {
                // Belum waktunya
                $upcoming[] = [
                    'subscriber' => $subscriber,
                    'agenda' => $agenda,
                    'reminder_minutes' => $reminderMinutes,
                    'minutes_until_reminder' => $minutesUntilReminder,
                ];
            }
            // Jika lewat window atau agenda sudah mulai, skip (terlalu terlambat)
        }

        $this->info("Siap kirim: " . count($readyToSend) . " subscriber");
        $this->newLine();

        // Kirim notifikasi
        foreach ($readyToSend as $subscriber) {
            $agenda = $subscriber->agenda;
            $reminderMinutes = $subscriber->reminder_minutes ?? 60;
            $type = $this->getReminderType($reminderMinutes);

            $this->line("-> {$agenda->perihal_kegiatan}");
            $this->line("  Waktu: {$agenda->waktu_mulai->format('d/m/Y H:i')} WIB");
            $this->line("  Subscriber: {$subscriber->phone_number} ({$subscriber->channel_preference})");
            $this->line("  Reminder: {$reminderMinutes} menit sebelum");

            if ($service->sendToSubscriber($subscriber, $type)) {
                $sent++;
                $this->info("  [OK] Terkirim!");
                Log::info("Reminder sent", [
                    'subscriber_id' => $subscriber->id,
                    'agenda_id' => $agenda->id,
                    'agenda' => $agenda->perihal_kegiatan,
                    'reminder_minutes' => $reminderMinutes,
                ]);
            } else {
                $this->warn("  [FAIL] Gagal mengirim");
            }
            $this->newLine();
        }

        // Tampilkan upcoming reminders
        if (!empty($upcoming)) {
            $this->info("Pengingat mendatang:");
            usort($upcoming, fn($a, $b) => $a['minutes_until_reminder'] <=> $b['minutes_until_reminder']);
            
            foreach (array_slice($upcoming, 0, 5) as $item) {
                $this->line("  - {$item['agenda']->perihal_kegiatan}");
                $this->line("    Kirim dalam: {$item['minutes_until_reminder']} menit ({$item['reminder_minutes']} menit sebelum agenda)");
            }
        }

        $this->newLine();
        $this->info("===========================================");
        $this->info("Total pengingat terkirim: {$sent}");
        $this->info("===========================================");

        return self::SUCCESS;
    }

    /**
     * Get reminder type label based on minutes
     */
    private function getReminderType(int $minutes): string
    {
        if ($minutes <= 15) return '15m';
        if ($minutes <= 30) return '30m';
        if ($minutes <= 60) return '1h';
        if ($minutes <= 120) return '2h';
        if ($minutes <= 360) return '6h';
        if ($minutes <= 1440) return '24h';
        return 'custom';
    }
}
