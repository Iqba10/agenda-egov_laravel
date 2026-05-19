<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FcmSender;
use App\Services\FonnteSender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
