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
    
    // Admin-only: Debug reminder status
    Route::get('/notifications/debug', [NotificationTestController::class, 'debugReminders'])->name('notifications.debug');
    Route::post('/notifications/run-scheduler', [NotificationTestController::class, 'runScheduler'])->name('notifications.run-scheduler');
});

require __DIR__.'/auth.php';
