<?php

use App\Http\Controllers\Admin\AgendaController as AdminAgendaController;
use App\Http\Controllers\Admin\NotificationTestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAgendaController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicAgendaController::class, 'index'])->name('agenda.index');
Route::get('/agenda/{agenda}', [PublicAgendaController::class, 'show'])->name('agenda.show');
Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
Route::get('/api/weather', WeatherController::class)->name('api.weather');
Route::get('/api/agenda-search', [NotificationController::class, 'search'])->name('agenda.notify.search');
Route::get('/api/notify/status', [NotificationController::class, 'status'])->name('notify.status');

// Temporary debug endpoint - hapus setelah debugging selesai
Route::get('/api/debug/reminders', function () {
    $now = now();
    
    // Service status
    $fonnte = app(\App\Services\FonnteSender::class);
    $fcm = app(\App\Services\FcmSender::class);
    
    // Upcoming agendas
    $upcomingAgendas = \App\Models\Agenda::query()
        ->where('status', '!=', 'dibatalkan')
        ->where('waktu_mulai', '>', $now)
        ->orderBy('waktu_mulai')
        ->limit(5)
        ->get(['id', 'perihal_kegiatan', 'waktu_mulai', 'reminder_sent_at']);
    
    // Subscribers
    $subscribers = \App\Models\NotifikasiPendaftar::with('agenda:id,perihal_kegiatan,waktu_mulai')
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();
    
    // FCM tokens
    $fcmTokens = \App\Models\FcmToken::active()
        ->orderByDesc('created_at')
        ->limit(10)
        ->get(['id', 'device_name', 'subscribed_agendas', 'is_active', 'created_at']);
    
    return response()->json([
        'server_time' => $now->format('Y-m-d H:i:s'),
        'timezone' => config('app.timezone'),
        'services' => [
            'whatsapp_configured' => $fonnte->isConfigured(),
            'fcm_configured' => $fcm->isConfigured(),
        ],
        'upcoming_agendas' => $upcomingAgendas->map(fn($a) => [
            'id' => $a->id,
            'perihal' => $a->perihal_kegiatan,
            'waktu_mulai' => $a->waktu_mulai?->format('Y-m-d H:i:s'),
            'minutes_until' => $now->diffInMinutes($a->waktu_mulai, false),
            'reminder_sent' => $a->reminder_sent_at?->format('Y-m-d H:i:s'),
        ]),
        'subscribers' => $subscribers->map(fn($s) => [
            'id' => $s->id,
            'agenda_id' => $s->agenda_id,
            'agenda' => $s->agenda?->perihal_kegiatan,
            'phone' => $s->phone_number ? substr($s->phone_number, 0, 6) . '***' : null,
            'channel' => $s->channel_preference,
            'wa_sent' => $s->whatsapp_sent,
            'fcm_sent' => $s->fcm_sent,
            'created' => $s->created_at?->format('Y-m-d H:i:s'),
        ]),
        'fcm_tokens' => $fcmTokens->map(fn($t) => [
            'id' => $t->id,
            'device' => $t->device_name,
            'subscribed_agendas' => $t->subscribed_agendas,
            'active' => $t->is_active,
            'created' => $t->created_at?->format('Y-m-d H:i:s'),
        ]),
    ]);
});
Route::post('/api/fcm/register', [NotificationController::class, 'registerFcmToken'])->name('fcm.register')->middleware('throttle:30,1');
Route::post('/notify/subscribe', [NotificationController::class, 'subscribe'])->name('notify.subscribe')->middleware('throttle:10,5');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminAgendaController::class, 'index'])->name('dashboard');
    Route::get('/agendas/print', [AdminAgendaController::class, 'print'])->name('agendas.print');
    Route::get('/agendas/create', [AdminAgendaController::class, 'create'])->name('agendas.create');
    Route::post('/agendas', [AdminAgendaController::class, 'store'])->name('agendas.store');
    Route::get('/agendas/{agenda}', [AdminAgendaController::class, 'show'])->name('agendas.show');
    Route::get('/agendas/{agenda}/edit', [AdminAgendaController::class, 'edit'])->name('agendas.edit');
    Route::put('/agendas/{agenda}', [AdminAgendaController::class, 'update'])->name('agendas.update');
    Route::delete('/agendas/{agenda}', [AdminAgendaController::class, 'destroy'])->name('agendas.destroy');
    Route::delete('/agendas/{agenda}/documents/{document}', [AdminAgendaController::class, 'destroyDocument'])->name('agendas.documents.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Notification Test Routes
    Route::get('/notifications/test', [NotificationTestController::class, 'index'])->name('notifications.test');
    Route::post('/notifications/test/whatsapp', [NotificationTestController::class, 'testWhatsapp'])->name('notifications.test.whatsapp');
    Route::post('/notifications/test/fcm', [NotificationTestController::class, 'testFcm'])->name('notifications.test.fcm');
    Route::post('/notifications/test/broadcast', [NotificationTestController::class, 'testFcmBroadcast'])->name('notifications.test.broadcast');
});

require __DIR__.'/auth.php';
