<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\NotifikasiPendaftar;
use App\Services\AgendaReminderService;
use App\Services\FcmSender;
use Illuminate\Console\Command;

class SendAgendaReminders extends Command
{
    protected $signature   = 'agenda:send-reminders';
    protected $description = 'Kirim pengingat WhatsApp dan FCM 1 jam sebelum agenda dimulai.';

    public function handle(AgendaReminderService $service, FcmSender $fcm): int
    {
        $now  = now();
        $sent = 0;

        $this->info('Memulai pengiriman pengingat agenda...');
        $this->info("Waktu server: {$now->format('Y-m-d H:i:s')} WIB");
        $this->newLine();

        // Kirim pengingat 1 jam sebelumnya (window 50-70 menit untuk toleransi)
        $this->info('⏱️ Cek pengingat 1 jam...');

        // Window diperlebar: 50-70 menit dari sekarang
        // Ini memberikan toleransi 20 menit jika scheduler delay
        $windowStart = $now->copy()->addMinutes(50);
        $windowEnd = $now->copy()->addMinutes(70);
        
        $this->info("   Window: {$windowStart->format('H:i')} - {$windowEnd->format('H:i')}");

        $agendas1h = Agenda::query()
            ->where('status', '!=', 'dibatalkan')
            ->whereBetween('waktu_mulai', [$windowStart, $windowEnd])
            ->get();

        $this->info("   Agenda ditemukan: {$agendas1h->count()}");

        foreach ($agendas1h as $agenda) {
            $this->line("   → Proses: {$agenda->perihal_kegiatan} ({$agenda->waktu_mulai->format('H:i')})");
            $count = $this->sendRemindersForAgenda($agenda, '1h', $service, $fcm);
            $sent += $count;

            if ($count > 0) {
                $this->line("     ✓ Terkirim: {$count} notifikasi");
            } else {
                $this->line("     ⚠ Tidak ada subscriber atau sudah dikirim");
            }
        }

        $this->newLine();
        $this->info("✅ Selesai. Total pengingat terkirim: {$sent}");

        return self::SUCCESS;
    }

    private function sendRemindersForAgenda(
        Agenda $agenda,
        string $type,
        AgendaReminderService $service,
        FcmSender $fcm
    ): int {
        $count = 0;

        // Kirim ke subscriber yang belum dikirim
        $subscribers = NotifikasiPendaftar::where('agenda_id', $agenda->id)
            ->where('status', '!=', 'failed')
            ->where(function ($q) {
                $q->where('whatsapp_sent', false)
                    ->orWhere('fcm_sent', false);
            })
            ->get();

        foreach ($subscribers as $subscriber) {
            if ($service->sendToSubscriber($subscriber, $type)) {
                $count++;
            }
        }

        // Kirim FCM ke semua token yang subscribe agenda ini
        $fcmCount = $fcm->sendToAgendaSubscribers($agenda, $type);
        $count += $fcmCount;

        return $count;
    }
}
