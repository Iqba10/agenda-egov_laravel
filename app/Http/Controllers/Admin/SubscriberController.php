<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\NotifikasiPendaftar;
use App\Services\AgendaReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriberController extends Controller
{
    public function __construct(
        private AgendaReminderService $reminderService
    ) {}

    /**
     * Display list of all subscribers
     */
    public function index(Request $request): View
    {
        $query = NotifikasiPendaftar::with('agenda')
            ->orderByDesc('created_at');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'sent') {
                $query->where(function ($q) {
                    $q->where('whatsapp_sent', true)
                      ->orWhere('fcm_sent', true);
                });
            } elseif ($request->status === 'pending') {
                $query->where('whatsapp_sent', false)
                      ->where('fcm_sent', false);
            }
        }

        // Filter by channel
        if ($request->filled('channel')) {
            $query->where('channel_preference', $request->channel);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhereHas('agenda', function ($q2) use ($search) {
                      $q2->where('perihal_kegiatan', 'like', "%{$search}%");
                  });
            });
        }

        $subscribers = $query->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => NotifikasiPendaftar::count(),
            'wa_sent' => NotifikasiPendaftar::where('whatsapp_sent', true)->count(),
            'wa_pending' => NotifikasiPendaftar::where('whatsapp_sent', false)
                ->whereIn('channel_preference', ['whatsapp', 'both'])->count(),
            'fcm_sent' => NotifikasiPendaftar::where('fcm_sent', true)->count(),
            'fcm_pending' => NotifikasiPendaftar::where('fcm_sent', false)
                ->whereIn('channel_preference', ['fcm', 'both'])->count(),
        ];

        // FCM Tokens
        $fcmTokens = FcmToken::orderByDesc('created_at')->limit(20)->get();

        return view('admin.subscribers.index', compact('subscribers', 'stats', 'fcmTokens'));
    }

    /**
     * Resend notification to a subscriber
     */
    public function resend(NotifikasiPendaftar $subscriber): JsonResponse
    {
        try {
            // Reset sent flags
            $subscriber->update([
                'whatsapp_sent' => false,
                'fcm_sent' => false,
            ]);

            // Get reminder type
            $reminderMinutes = $subscriber->reminder_minutes ?? 60;
            $type = $this->getReminderType($reminderMinutes);

            // Send immediately
            $success = $this->reminderService->sendToSubscriber($subscriber, $type);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi berhasil dikirim ulang!',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim notifikasi. Cek konfigurasi service.',
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a subscriber
     */
    public function destroy(NotifikasiPendaftar $subscriber): JsonResponse
    {
        try {
            $subscriber->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subscriber berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an FCM token
     */
    public function destroyFcmToken(FcmToken $fcmToken): JsonResponse
    {
        try {
            $fcmToken->delete();

            return response()->json([
                'success' => true,
                'message' => 'FCM Token berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk resend pending notifications
     */
    public function bulkResend(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada subscriber yang dipilih.',
                ], 422);
            }

            $sent = 0;
            $failed = 0;

            $subscribers = NotifikasiPendaftar::whereIn('id', $ids)->get();

            foreach ($subscribers as $subscriber) {
                $subscriber->update([
                    'whatsapp_sent' => false,
                    'fcm_sent' => false,
                ]);

                $reminderMinutes = $subscriber->reminder_minutes ?? 60;
                $type = $this->getReminderType($reminderMinutes);

                if ($this->reminderService->sendToSubscriber($subscriber, $type)) {
                    $sent++;
                } else {
                    $failed++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil kirim: {$sent}, Gagal: {$failed}",
                'sent' => $sent,
                'failed' => $failed,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getReminderType(int $minutes): string
    {
        if ($minutes <= 30) return '30m';
        if ($minutes <= 60) return '1h';
        if ($minutes <= 120) return '2h';
        if ($minutes <= 360) return '6h';
        if ($minutes <= 1440) return '24h';
        return 'custom';
    }
}
