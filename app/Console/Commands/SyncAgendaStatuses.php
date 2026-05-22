<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use Illuminate\Console\Command;

class SyncAgendaStatuses extends Command
{
    protected $signature = 'agenda:sync-statuses';
    protected $description = 'Sinkronkan status agenda berdasarkan waktu mulai dan waktu selesai.';

    public function handle(): int
    {
        $now = now();
        $updated = 0;

        Agenda::query()
            ->where('status', '!=', 'dibatalkan')
            ->chunkById(100, function ($agendas) use (&$updated, $now) {
                foreach ($agendas as $agenda) {
                    $computed = $agenda->computedStatus();

                    if ($agenda->status !== $computed) {
                        $agenda->forceFill(['status' => $computed])->saveQuietly();
                        $updated++;
                    }
                }
            });

        $this->info("Status agenda tersinkronisasi: {$updated}");

        return self::SUCCESS;
    }
}
