<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\FcmToken;
use App\Services\AgendaReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function __construct(
        private AgendaReminderService $reminderService
    ) {}

    /**
     * Search agenda untuk modal notifikasi
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->string('q')->toString();

        $agendas = Agenda::query()
            ->select(['id', 'slug', 'perihal_kegiatan', 'waktu_mulai', 'waktu_selesai', 'tempat', 'status'])
            ->when($q, fn ($query) => $query->where('perihal_kegiatan', 'like', "%{$q}%")
                ->orWhere('tempat', 'like', "%{$q}%"))
            ->where('status', '!=', 'dibatalkan')
            ->where('waktu_mulai', '>', now())
            ->orderBy('waktu_mulai', 'asc')
            ->limit(20)
            ->get()
            ->map(fn ($a) => [
                'id'               => $a->id,
                'slug'             => $a->slug,
                'perihal_kegiatan' => $a->perihal_kegiatan,
                'waktu_mulai'      => $a->waktu_mulai?->translatedFormat('d M Y, H:i'),
                'tempat'           => $a->tempat,
                'status'           => $a->status,
            ]);

        return response()->json($agendas);
    }

    /**
     * Subscribe ke agenda dengan pilihan channel (whatsapp/fcm/both)
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel'         => ['required', 'in:whatsapp,fcm,both'],
            'agenda_ids'      => ['required', 'array', 'min:1', 'max:10'],
            'agenda_ids.*'    => ['integer', 'exists:agenda,id'],
            'phone_number'    => ['required_if:channel,whatsapp,both', 'nullable', 'string', 'max:20'],
            'fcm_token'       => ['required_if:channel,fcm,both', 'nullable', 'string', 'max:500'],
            'nama'            => ['nullable', 'string', 'max:100'],
            'reminder_minutes'=> ['nullable', 'integer', 'min:1', 'max:10080'], // 1 min - 7 days
        ], [
            'agenda_ids.required'       => 'Pilih minimal satu agenda.',
            'agenda_ids.min'            => 'Pilih minimal satu agenda.',
            'phone_number.required_if'  => 'Nomor WhatsApp diperlukan untuk channel ini.',
            'fcm_token.required_if'     => 'Izinkan notifikasi browser terlebih dahulu.',
            'reminder_minutes.min'      => 'Waktu pengingat minimal 5 menit.',
            'reminder_minutes.max'      => 'Waktu pengingat maksimal 7 hari.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $channel = $data['channel'];

        // Check service availability
        $serviceStatus = $this->reminderService->getServiceStatus();

        if ($channel === 'whatsapp' && !$serviceStatus['whatsapp']) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan WhatsApp belum dikonfigurasi.',
            ], 503);
        }

        if ($channel === 'fcm' && !$serviceStatus['fcm']) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan notifikasi browser belum dikonfigurasi.',
            ], 503);
        }

        try {
            \Log::info('NotificationController@subscribe - RAW REQUEST', [
                'all_input' => $request->all(),
                'validated_data' => $data,
                'channel' => $channel,
            ]);

            // Debug: cek data yang diterima
            $debugInfo = [
                'received_channel' => $channel,
                'received_agenda_ids' => $data['agenda_ids'] ?? [],
                'received_phone' => isset($data['phone_number']) ? substr($data['phone_number'], 0, 6) . '***' : null,
                'received_fcm_token' => isset($data['fcm_token']) ? substr($data['fcm_token'], 0, 20) . '...' : null,
            ];

            // Subscribe ke semua agenda yang dipilih
            $results = $this->reminderService->subscribeToMultipleAgendas($data);

            \Log::info('NotificationController@subscribe - RESULTS', [
                'results_count' => count($results),
                'results' => $results,
            ]);

            // Cek apakah benar-benar ada yang tersimpan
            if (empty($results) && in_array($channel, ['whatsapp', 'both'])) {
                \Log::warning('NotificationController@subscribe - NO RESULTS but expected WhatsApp subscription');
            }

            $channelLabel = match ($channel) {
                'whatsapp' => 'WhatsApp',
                'fcm'      => 'notifikasi browser',
                'both'     => 'WhatsApp dan notifikasi browser',
            };

            // Format waktu pengingat untuk pesan
            $reminderMinutes = $data['reminder_minutes'] ?? 60;
            $reminderLabel = $this->formatReminderTime($reminderMinutes);

            return response()->json([
                'success' => true,
                'message' => "Terdaftar! Anda akan diingatkan via {$channelLabel} {$reminderLabel} sebelum agenda dimulai.",
                'results_count' => count($results),
                'debug' => $debugInfo,
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('NotificationController@subscribe - ERROR', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            report($e);
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan. Silakan coba lagi.',
                'debug_error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Register FCM token dari browser
     */
    public function registerFcmToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token'       => ['required', 'string', 'max:500'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $fcmToken = $this->reminderService->registerFcmToken(
                $request->input('token'),
                $request->input('device_name')
            );

            return response()->json([
                'success' => true,
                'message' => 'Token terdaftar.',
                'token_id' => $fcmToken->id,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Gagal mendaftarkan token.'], 500);
        }
    }

    /**
     * Get notification service status
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'success'  => true,
            'services' => $this->reminderService->getServiceStatus(),
        ]);
    }

    /**
     * Format reminder time for display
     */
    private function formatReminderTime(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} menit";
        }
        
        $hours = $minutes / 60;
        
        if ($minutes % 60 === 0) {
            if ($hours >= 24) {
                $days = $hours / 24;
                return $days == 1 ? "1 hari" : "{$days} hari";
            }
            return $hours == 1 ? "1 jam" : "{$hours} jam";
        }
        
        // Mixed hours and minutes
        $wholeHours = floor($hours);
        $remainingMinutes = $minutes % 60;
        return "{$wholeHours} jam {$remainingMinutes} menit";
    }
}
