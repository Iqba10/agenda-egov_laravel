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
        $this->newLine();

        // Kirim pengingat 1 jam sebelumnya
        $this->info('⏱️ Cek pengingat 1 jam...');

        $agendas1h = Agenda::query()
            ->where('status', '!=', 'dibatalkan')
            ->whereBetween('waktu_mulai', [
                $now->copy()->addHour()->subMinute(),
                $now->copy()->addHour()->addMinute(),
            ])
            ->get();

        foreach ($agendas1h as $agenda) {
            $count = $this->sendRemindersForAgenda($agenda, '1h', $service, $fcm);
            $sent += $count;

            if ($count > 0) {
                $this->line("   ✓ {$agenda->perihal_kegiatan}: {$count} notifikasi");
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

        // Tentukan kolom flag berdasarkan type
        $waFlag = $type === '24h' ? 'whatsapp_sent' : 'whatsapp_sent';
        $fcmFlag = $type === '24h' ? 'fcm_sent' : 'fcm_sent';

        // Kirim ke subscriber yang belum dikirim untuk type ini
        // Untuk simplicity, kita track berdasarkan status dan flag
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
