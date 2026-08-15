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
     * Global one-time subscription endpoint.
     *
     * Accepts:
     * - fcm_token (string, required if browser notifications enabled)
     * - device_name (string, optional)
     * - whatsapp_contacts (array of {name, phone})
     */
    public function registerFcmToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fcm_token'         => ['nullable', 'string', 'max:500'],
            'device_name'       => ['nullable', 'string', 'max:255'],
            'whatsapp_contacts' => ['nullable', 'array', 'max:20'],
            'whatsapp_contacts.*.name'  => ['nullable', 'string', 'max:100'],
            'whatsapp_contacts.*.phone' => ['required_with:whatsapp_contacts', 'string', 'min:10', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $fcmToken = $data['fcm_token'] ?? null;
        $hasFcm = !empty($fcmToken);
        $hasWa = !empty($data['whatsapp_contacts']);

        if (!$hasFcm && !$hasWa) {
            return response()->json(['success' => false, 'message' => 'Pilih minimal satu metode notifikasi.'], 422);
        }

        try {
            $savedFcmToken = null;
            $waAdded = 0;
            $waSkipped = 0;
            $waDuplicates = 0;

            if ($hasFcm) {
                $savedFcmToken = $this->reminderService->registerFcmToken(
                    $fcmToken,
                    $data['device_name'] ?? null
                );
            }

            if ($hasWa) {
                foreach ($data['whatsapp_contacts'] as $contact) {
                    $phone = $this->normalizePhone($contact['phone'] ?? '');
                    if (strlen($phone) < 10) {
                        $waSkipped++;
                        continue;
                    }

                    // Check duplicate
                    $existing = FcmToken::where('whatsapp_phone', $phone)->first();
                    if ($existing) {
                        $waDuplicates++;
                        continue;
                    }

                    FcmToken::create([
                        'token'           => 'whatsapp-user-' . $phone,
                        'device_name'     => $data['device_name'] ?? 'WhatsApp Subscriber',
                        'is_active'       => false,
                        'whatsapp_opt_in' => true,
                        'whatsapp_name'   => $contact['name'] ?? null,
                        'whatsapp_phone'  => $phone,
                    ]);

                    $waAdded++;
                }
            }

            $messages = [];
            if ($savedFcmToken) {
                $messages[] = 'langganan notifikasi browser';
            }
            if ($waAdded > 0) {
                $messages[] = "{$waAdded} nomor WhatsApp";
            }

            $detailMsg = '';
            if ($waSkipped > 0) {
                $detailMsg .= " {$waSkipped} nomor tidak valid.";
            }
            if ($waDuplicates > 0) {
                $detailMsg .= " {$waDuplicates} nomor sudah terdaftar.";
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menyimpan ' . ($messages ? implode(' dan ', $messages) : 'langganan') . '.' . $detailMsg,
                'token_id' => $savedFcmToken?->id,
                'wa_added' => $waAdded,
                'wa_skipped' => $waSkipped,
                'wa_duplicates' => $waDuplicates,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Gagal menyimpan langganan.',
            ], 500);
        }
    }

    /**
     * List upcoming agendas for the modal preview.
     */
    public function incoming(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 20), 50);

        $agendas = Agenda::query()
            ->select(['id', 'slug', 'perihal_kegiatan', 'waktu_mulai', 'tempat', 'status'])
            ->where('status', '!=', 'dibatalkan')
            ->where('waktu_mulai', '>', now())
            ->orderBy('waktu_mulai', 'asc')
            ->limit($limit)
            ->get()
            ->map(fn ($a) => [
                'id'               => $a->id,
                'slug'             => $a->slug,
                'perihal_kegiatan' => $a->perihal_kegiatan,
                'tanggal_mulai'    => $a->waktu_mulai?->translatedFormat('d M Y'),
                'waktu_mulai'      => $a->waktu_mulai?->translatedFormat('H:i'),
                'tempat'           => $a->tempat,
                'status'           => $a->status,
            ]);

        return response()->json($agendas);
    }

    /**
     * Search agenda for legacy/admin usage.
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
     * Get notification service status.
     */
    public function status(): JsonResponse
    {
        return response()->json([
            'success'  => true,
            'services' => $this->reminderService->getServiceStatus(),
        ]);
    }

    private function normalizePhone(?string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone ?? '');
        $phone = ltrim($phone, '0');
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }
}
