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
Route::get('/api/debug/test-insert/{agendaId}', function ($agendaId) {
    try {
        // Test insert langsung ke database
        $subscriber = \App\Models\NotifikasiPendaftar::create([
            'agenda_id' => $agendaId,
            'phone_number' => '628123456789',
            'channel_preference' => 'both',
            'nama' => 'Test Debug',
        ]);
        
        return response()->json([
            'success' => true,
            'subscriber_id' => $subscriber->id,
            'subscriber' => $subscriber->toArray(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

Route::get('/api/debug/reminders', function () {
    try {
        $now = now();
        
        // Upcoming agendas
        $upcomingAgendas = \App\Models\Agenda::query()
            ->where('status', '!=', 'dibatalkan')
            ->where('waktu_mulai', '>', $now)
            ->orderBy('waktu_mulai')
            ->limit(5)
            ->get();
        
        // Subscribers
        $subscribers = \App\Models\NotifikasiPendaftar::orderByDesc('created_at')
            ->limit(10)
            ->get();
        
        // FCM tokens
        $fcmTokens = \App\Models\FcmToken::orderByDesc('created_at')
            ->limit(10)
            ->get();
        
        return response()->json([
            'server_time' => $now->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
            'upcoming_agendas_count' => $upcomingAgendas->count(),
            'upcoming_agendas' => $upcomingAgendas->map(function($a) use ($now) {
                return [
                    'id' => $a->id,
                    'perihal' => $a->perihal_kegiatan,
                    'waktu_mulai' => optional($a->waktu_mulai)->format('Y-m-d H:i:s'),
                    'minutes_until' => $a->waktu_mulai ? $now->diffInMinutes($a->waktu_mulai, false) : null,
                    'reminder_sent' => optional($a->reminder_sent_at)->format('Y-m-d H:i:s'),
                ];
            }),
            'subscribers_count' => $subscribers->count(),
            'subscribers' => $subscribers->map(function($s) {
                return [
                    'id' => $s->id,
                    'agenda_id' => $s->agenda_id,
                    'phone' => $s->phone_number ? substr($s->phone_number, 0, 6) . '***' : null,
                    'channel' => $s->channel_preference,
                    'wa_sent' => $s->whatsapp_sent,
                    'fcm_sent' => $s->fcm_sent,
                    'created' => optional($s->created_at)->format('Y-m-d H:i:s'),
                ];
            }),
            'fcm_tokens_count' => $fcmTokens->count(),
            'fcm_tokens' => $fcmTokens->map(function($t) {
                return [
                    'id' => $t->id,
                    'device' => $t->device_name,
                    'subscribed_agendas' => $t->subscribed_agendas,
                    'active' => $t->is_active,
                    'created' => optional($t->created_at)->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
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
