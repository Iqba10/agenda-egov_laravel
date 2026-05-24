<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\AgendaReminder;
use App\Models\NotifikasiPendaftar;
use App\Services\AgendaReminderService;
use App\Services\FcmSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAgendaReminders extends Command
{
    protected $signature   = 'agenda:send-reminders';
    protected $description = 'Kirim pengingat WhatsApp dan FCM 1 jam sebelum agenda dimulai.';

    public function handle(AgendaReminderService $service, FcmSender $fcm): int
    {
        $now  = now();
        $sent = 0;

        $this->info('===========================================');
        $this->info('  AGENDA REMINDER SCHEDULER');
        $this->info('===========================================');
        $this->info("Waktu server: {$now->format('Y-m-d H:i:s')} WIB");
        $this->newLine();

        // Kirim pengingat untuk agenda yang dimulai dalam 50-70 menit
        // Ini berarti notifikasi masuk ~1 jam sebelum (±10 menit toleransi)
        // Contoh: agenda 21:00 → notifikasi masuk sekitar 19:50 - 20:10
        $windowStart = $now->copy()->addMinutes(50);
        $windowEnd = $now->copy()->addMinutes(70);
        
        $this->info("Window pengiriman: {$windowStart->format('H:i')} - {$windowEnd->format('H:i')}");
        $this->info("(Notifikasi ~1 jam sebelum agenda, toleransi ±10 menit)");
        $this->newLine();

        // Cari agenda yang belum dikirim reminder
        $agendas = Agenda::query()
            ->where('status', '!=', 'dibatalkan')
            ->whereBetween('waktu_mulai', [$windowStart, $windowEnd])
            ->whereNull('reminder_sent_at') // Belum pernah kirim reminder
            ->get();

        $this->info("Agenda dalam window: {$agendas->count()}");

        if ($agendas->isEmpty()) {
            $this->info("Tidak ada agenda yang perlu dikirim reminder.");
            
            // Log untuk debugging
            $allUpcoming = Agenda::query()
                ->where('status', '!=', 'dibatalkan')
                ->where('waktu_mulai', '>', $now)
                ->whereNull('reminder_sent_at')
                ->orderBy('waktu_mulai')
                ->limit(5)
                ->get(['id', 'perihal_kegiatan', 'waktu_mulai']);
            
            if ($allUpcoming->isNotEmpty()) {
                $this->newLine();
                $this->info("Agenda mendatang yang belum dikirim reminder:");
                foreach ($allUpcoming as $a) {
                    $diff = $now->diffInMinutes($a->waktu_mulai);
                    $this->line("  - {$a->perihal_kegiatan} ({$a->waktu_mulai->format('H:i')}) - dalam {$diff} menit");
                }
            }
        }

        foreach ($agendas as $agenda) {
            $this->newLine();
            $this->line("→ Proses: {$agenda->perihal_kegiatan}");
            $this->line("  Waktu mulai: {$agenda->waktu_mulai->format('d/m/Y H:i')} WIB");
            
            $count = $this->sendRemindersForAgenda($agenda, '1h', $service, $fcm);
            $sent += $count;

            // Tandai agenda sudah dikirim reminder
            $agenda->update(['reminder_sent_at' => now()]);

            if ($count > 0) {
                $this->info("  ✓ Terkirim: {$count} notifikasi");
                Log::info("Reminder sent for agenda", [
                    'agenda_id' => $agenda->id,
                    'agenda' => $agenda->perihal_kegiatan,
                    'count' => $count,
                ]);
            } else {
                $this->warn("  ⚠ Tidak ada subscriber untuk agenda ini");
            }
        }

        $this->newLine();
        $this->info("===========================================");
        $this->info("Total pengingat terkirim: {$sent}");
        $this->info("===========================================");

        return self::SUCCESS;
    }

    private function sendRemindersForAgenda(
        Agenda $agenda,
        string $type,
        AgendaReminderService $service,
        FcmSender $fcm
    ): int {
        $count = 0;

        // Kirim ke subscriber WhatsApp yang belum dikirim
        $subscribers = NotifikasiPendaftar::where('agenda_id', $agenda->id)
            ->where('status', '!=', 'failed')
            ->where(function ($q) {
                $q->where('whatsapp_sent', false)
                    ->orWhere('fcm_sent', false);
            })
            ->get();

        $this->line("  Subscribers ditemukan: {$subscribers->count()}");

        foreach ($subscribers as $subscriber) {
            $this->line("    - {$subscriber->phone_number} ({$subscriber->channel_preference})");
            if ($service->sendToSubscriber($subscriber, $type)) {
                $count++;
            }
        }

        // Kirim FCM ke semua token yang subscribe agenda ini
        $fcmCount = $fcm->sendToAgendaSubscribers($agenda, $type);
        if ($fcmCount > 0) {
            $this->line("  FCM tokens terkirim: {$fcmCount}");
        }
        $count += $fcmCount;

        return $count;
    }
}
