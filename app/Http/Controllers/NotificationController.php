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
            'channel'      => ['required', 'in:whatsapp,fcm,both'],
            'agenda_ids'   => ['required', 'array', 'min:1', 'max:10'],
            'agenda_ids.*' => ['integer', 'exists:agenda,id'],
            'phone_number' => ['required_if:channel,whatsapp,both', 'nullable', 'string', 'max:20'],
            'fcm_token'    => ['required_if:channel,fcm,both', 'nullable', 'string', 'max:500'],
            'nama'         => ['nullable', 'string', 'max:100'],
        ], [
            'agenda_ids.required'       => 'Pilih minimal satu agenda.',
            'agenda_ids.min'            => 'Pilih minimal satu agenda.',
            'phone_number.required_if'  => 'Nomor WhatsApp diperlukan untuk channel ini.',
            'fcm_token.required_if'     => 'Izinkan notifikasi browser terlebih dahulu.',
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
            \Log::info('NotificationController@subscribe - START', [
                'validated_data' => $data,
                'channel' => $channel,
            ]);

            // Subscribe ke semua agenda yang dipilih
            $results = $this->reminderService->subscribeToMultipleAgendas($data);

            \Log::info('NotificationController@subscribe - RESULTS', [
                'results_count' => count($results),
                'results' => $results,
            ]);

            $channelLabel = match ($channel) {
                'whatsapp' => 'WhatsApp',
                'fcm'      => 'notifikasi browser',
                'both'     => 'WhatsApp dan notifikasi browser',
            };

            return response()->json([
                'success' => true,
                'message' => "Terdaftar! Anda akan diingatkan via {$channelLabel} 1 jam sebelum agenda dimulai.",
                'results' => $results,
                'debug' => [
                    'results_count' => count($results),
                    'channel' => $channel,
                    'agenda_ids' => $data['agenda_ids'] ?? [],
                ],
            ]);

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
}
