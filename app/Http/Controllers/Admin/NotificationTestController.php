<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\FcmToken;
use App\Models\NotifikasiPendaftar;
use App\Services\FcmSender;
use App\Services\FonnteSender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class NotificationTestController extends Controller
{
    public function __construct(
        private FonnteSender $fonnte,
        private FcmSender $fcm
    ) {}

    public function index(): View
    {
        return view('admin.notifications.test', [
            'fonnte_configured' => $this->fonnte->isConfigured(),
            'fcm_configured' => $this->fcm->isConfigured(),
        ]);
    }

    public function testWhatsapp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'message' => 'required|string|max:1000',
        ]);

        $phone = $this->normalizePhone($request->phone);

        try {
            $result = $this->fonnte->send($phone, $request->message);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan WhatsApp berhasil dikirim ke ' . $phone,
                    'data' => $result,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim WhatsApp: ' . ($result['error'] ?? 'Unknown error'),
                'data' => $result,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function testFcm(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string|min:50',
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:500',
        ]);

        try {
            $result = $this->fcm->sendToToken(
                $request->token,
                $request->title,
                $request->body,
                $request->input('data', [])
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Push notification berhasil dikirim!',
                    'data' => $result,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim FCM: ' . ($result['error'] ?? 'Unknown error'),
                'data' => $result,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function testFcmBroadcast(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:500',
        ]);

        try {
            $result = $this->fcm->sendToTopic(
                'agenda-updates',
                $request->title,
                $request->body
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Broadcast ke topic berhasil dikirim!',
                    'data' => $result,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal broadcast: ' . ($result['error'] ?? 'Unknown error'),
                'data' => $result,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Debug reminder status (admin only)
     */
    public function debugReminders(): JsonResponse
    {
        $now = now();
        
        // Upcoming agendas
        $upcomingAgendas = Agenda::query()
            ->where('status', '!=', 'dibatalkan')
            ->where('waktu_mulai', '>', $now)
            ->orderBy('waktu_mulai')
            ->limit(10)
            ->get();
        
        // All subscribers (recent)
        $subscribers = NotifikasiPendaftar::with('agenda')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
        
        // FCM tokens
        $fcmTokens = FcmToken::orderByDesc('created_at')
            ->limit(10)
            ->get();
        
        // Calculate reminder times for subscribers
        $subscriberData = $subscribers->map(function ($s) use ($now) {
            $agenda = $s->agenda;
            $reminderMinutes = $s->reminder_minutes ?? 60;
            $reminderTime = $agenda?->waktu_mulai?->copy()->subMinutes($reminderMinutes);
            $minutesUntil = $reminderTime ? $now->diffInMinutes($reminderTime, false) : null;
            
            return [
                'id' => $s->id,
                'agenda_id' => $s->agenda_id,
                'agenda_perihal' => $agenda?->perihal_kegiatan ? substr($agenda->perihal_kegiatan, 0, 50) : null,
                'agenda_waktu' => $agenda?->waktu_mulai?->format('Y-m-d H:i'),
                'phone' => $s->phone_number ? substr($s->phone_number, 0, 8) . '***' : null,
                'channel' => $s->channel_preference,
                'reminder_minutes' => $reminderMinutes,
                'reminder_time' => $reminderTime?->format('Y-m-d H:i'),
                'minutes_until_reminder' => $minutesUntil,
                'wa_sent' => $s->whatsapp_sent,
                'fcm_sent' => $s->fcm_sent,
                'created' => $s->created_at?->format('Y-m-d H:i:s'),
            ];
        });
        
        return response()->json([
            'server_time' => $now->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
            'services' => [
                'fonnte_configured' => $this->fonnte->isConfigured(),
                'fcm_configured' => $this->fcm->isConfigured(),
            ],
            'upcoming_agendas' => $upcomingAgendas->map(fn($a) => [
                'id' => $a->id,
                'perihal' => substr($a->perihal_kegiatan, 0, 50),
                'waktu_mulai' => $a->waktu_mulai?->format('Y-m-d H:i'),
                'minutes_until' => $a->waktu_mulai ? $now->diffInMinutes($a->waktu_mulai, false) : null,
            ]),
            'subscribers_count' => $subscribers->count(),
            'subscribers' => $subscriberData,
            'fcm_tokens_count' => $fcmTokens->count(),
            'fcm_tokens' => $fcmTokens->map(fn($t) => [
                'id' => $t->id,
                'device' => $t->device_name,
                'subscribed_agendas' => $t->subscribed_agendas,
                'active' => $t->is_active,
                'created' => $t->created_at?->format('Y-m-d H:i:s'),
            ]),
        ]);
    }
    
    /**
     * Run scheduler manually (admin only)
     */
    public function runScheduler(): JsonResponse
    {
        try {
            Artisan::call('agenda:send-reminders');
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'output' => $output,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
