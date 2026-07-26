<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'Total subscribers: ' . App\Models\NotifikasiPendaftar::count() . PHP_EOL;

$subs = App\Models\NotifikasiPendaftar::latest()->take(5)->get(['id', 'reminder_minutes', 'phone_number', 'created_at']);

foreach ($subs as $s) {
    echo 'ID: ' . $s->id . ', reminder_minutes: ' . $s->reminder_minutes . ', phone: ' . substr($s->phone_number, 0, 10) . '..., created: ' . $s->created_at . PHP_EOL;
}
