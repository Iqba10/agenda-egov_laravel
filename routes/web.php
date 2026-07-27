<?php

use App\Http\Controllers\Admin\AgendaController as AdminAgendaController;
use App\Http\Controllers\Admin\NotificationTestController;
use App\Http\Controllers\Admin\OpdGroupController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\BulkRegistrationController;
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
Route::get('/api/agenda/incoming', [NotificationController::class, 'incoming'])->name('api.agenda.incoming');
Route::get('/api/notify/status', [NotificationController::class, 'status'])->name('notify.status');
Route::post('/api/fcm/register', [NotificationController::class, 'registerFcmToken'])->name('api.fcm.register')->middleware('throttle:30,1');

// Bulk Registration (WhatsApp)
Route::get('/notify/bulk', [BulkRegistrationController::class, 'index'])->name('notify.bulk');
Route::post('/notify/bulk', [BulkRegistrationController::class, 'store'])->name('notify.bulk.store')->middleware('throttle:5,1');

Route::get('/api/opd-groups', [App\Http\Controllers\Api\OpdGroupController::class, 'index'])->name('api.opd-groups.index');

// Temporary diagnostic route (remove after debugging)
Route::get('/api/debug-db', function () {
    $results = [];
    try {
        $results['db_connection'] = 'OK';
        \DB::connection()->getPdo();
    } catch (\Throwable $e) {
        $results['db_connection'] = 'FAIL: ' . $e->getMessage();
    }
    try {
        $results['opd_groups_table'] = \Schema::hasTable('opd_groups') ? 'EXISTS' : 'MISSING';
    } catch (\Throwable $e) {
        $results['opd_groups_table'] = 'ERROR: ' . $e->getMessage();
    }
    try {
        $results['opd_groups_count'] = \DB::table('opd_groups')->count();
    } catch (\Throwable $e) {
        $results['opd_groups_count'] = 'ERROR: ' . $e->getMessage();
    }
    try {
        $results['migrations'] = \DB::table('migrations')->where('migration', 'like', '%opd%')->pluck('migration')->toArray();
    } catch (\Throwable $e) {
        $results['migrations'] = 'ERROR: ' . $e->getMessage();
    }
    return response()->json($results);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Temporary route for creating users (remove in production)
Route::get('/setup-users', function () {
    $admin = \App\Models\User::firstOrCreate(
        ['email' => 'admin@agenda-egov.local'],
        [
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'admin',
        ]
    );

    $user = \App\Models\User::firstOrCreate(
        ['email' => 'user@agenda-egov.local'],
        [
            'name' => 'User Biasa',
            'username' => 'user',
            'password' => \Illuminate\Support\Facades\Hash::make('user123'),
            'role' => 'user',
        ]
    );

    return response()->json([
        'admin' => ['email' => $admin->email, 'username' => $admin->username, 'password' => 'admin123'],
        'user' => ['email' => $user->email, 'username' => $user->username, 'password' => 'user123'],
    ]);
})->name('setup.users');

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

    // Subscribers Management
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
    Route::post('/subscribers/{subscriber}/resend', [SubscriberController::class, 'resend'])->name('subscribers.resend');
    Route::delete('/subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');
    Route::post('/subscribers/bulk-resend', [SubscriberController::class, 'bulkResend'])->name('subscribers.bulk-resend');
    Route::delete('/fcm-tokens/{fcmToken}', [SubscriberController::class, 'destroyFcmToken'])->name('fcm-tokens.destroy');

    // OPD Groups Management
    Route::get('/opd-groups', [OpdGroupController::class, 'index'])->name('opd-groups.index');
    Route::get('/opd-groups/create', [OpdGroupController::class, 'create'])->name('opd-groups.create');
    Route::post('/opd-groups', [OpdGroupController::class, 'store'])->name('opd-groups.store');
    Route::get('/opd-groups/{opdGroup}/edit', [OpdGroupController::class, 'edit'])->name('opd-groups.edit');
    Route::put('/opd-groups/{opdGroup}', [OpdGroupController::class, 'update'])->name('opd-groups.update');
    Route::delete('/opd-groups/{opdGroup}', [OpdGroupController::class, 'destroy'])->name('opd-groups.destroy');
    Route::post('/opd-groups/fetch', [OpdGroupController::class, 'fetchGroups'])->name('opd-groups.fetch');

    // OPD Group Members
    Route::get('/opd-groups/{opdGroup}/members', [OpdGroupController::class, 'members'])->name('opd-groups.members');
    Route::post('/opd-groups/{opdGroup}/members', [OpdGroupController::class, 'addMembers'])->name('opd-groups.members.add');
    Route::delete('/opd-groups/{opdGroup}/members/{member}', [OpdGroupController::class, 'removeMember'])->name('opd-groups.members.remove');
});

require __DIR__.'/auth.php';
