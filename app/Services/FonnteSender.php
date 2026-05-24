<?php

namespace App\Services;

use App\Models\Agenda;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteSender
{
    private string $apiUrl = 'https://api.fonnte.com/send';
    private ?string $token;
    private ?string $device;

    public function __construct()
    {
        $this->token  = config('services.fonnte.token');
        $this->device = config('services.fonnte.device');
    }

    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    public function send(string $phone, string $message): array
    {
        if (!$this->isConfigured()) {
            Log::warning('Fonnte not configured - FONNTE_TOKEN is empty');
            return ['success' => false, 'error' => 'Fonnte token tidak dikonfigurasi'];
        }

        // Normalize phone number (pastikan format 628xxx)
        $phone = $this->normalizePhone($phone);

        try {
            $payload = [
                'target'  => $phone,
                'message' => $message,
            ];

            if ($this->device) {
                $payload['device'] = $this->device;
            }

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Fonnte API response', ['phone' => $phone, 'response' => $data]);

                // Fonnte returns status in response (could be boolean true or string "true")
                $status = $data['status'] ?? false;
                if ($status === true || $status === 'true' || $status == 1) {
                    Log::info('WhatsApp sent successfully', ['phone' => $phone]);
                    return ['success' => true, 'data' => $data];
                }

                // Check for detail/process field (alternative success indicator)
                if (isset($data['detail']) || isset($data['process'])) {
                    Log::info('WhatsApp sent (via detail/process)', ['phone' => $phone]);
                    return ['success' => true, 'data' => $data];
                }

                Log::warning('Fonnte API returned error', [
                    'phone'    => $phone,
                    'response' => $data,
                ]);
                return ['success' => false, 'error' => $data['reason'] ?? $data['detail'] ?? 'Unknown error', 'data' => $data];
            }

            Log::error('Fonnte API request failed', [
                'phone'  => $phone,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return ['success' => false, 'error' => 'HTTP ' . $response->status()];

        } catch (\Throwable $e) {
            Log::error('Fonnte API exception', [
                'phone'   => $phone,
                'message' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendAgendaReminder(string $phone, Agenda $agenda, string $type = 'immediate'): bool
    {
        $message = $this->buildAgendaMessage($agenda, $type);
        $result = $this->send($phone, $message);
        return $result['success'];
    }

    public function sendBulkAgendaReminder(string $phone, array $agendas, string $type = 'immediate'): bool
    {
        $message = $this->buildBulkAgendaMessage($agendas, $type);
        $result = $this->send($phone, $message);
        return $result['success'];
    }

    private function buildAgendaMessage(Agenda $agenda, string $type): string
    {
        $typeLabel = match ($type) {
            '24h'       => '⏰ *Pengingat H-1*',
            '6h'        => '🔔 *Pengingat 6 Jam Lagi*',
            '2h'        => '🔔 *Pengingat 2 Jam Lagi*',
            '1h'        => '⏱️ *Pengingat 1 Jam Lagi*',
            '30m'       => '⏱️ *Pengingat 30 Menit Lagi*',
            'custom'    => '🔔 *Pengingat Agenda*',
            default     => '📋 *Konfirmasi Pendaftaran*',
        };

        $waktuMulai = $agenda->waktu_mulai?->translatedFormat('l, d F Y — H:i') ?? '-';
        $url = url('/agenda/' . $agenda->slug);

        return <<<MSG
{$typeLabel}

*{$agenda->perihal_kegiatan}*

📅 {$waktuMulai} WIB
📍 {$agenda->tempat}
🏢 {$agenda->asal_surat}

🔗 Detail: {$url}

---
_Agenda eGov — Diskominfo Kabupaten Sambas_
MSG;
    }

    private function buildBulkAgendaMessage(array $agendas, string $type): string
    {
        $typeLabel = match ($type) {
            '24h'    => '⏰ *Pengingat Agenda H-1*',
            '6h'     => '🔔 *Pengingat Agenda 6 Jam Lagi*',
            '2h'     => '🔔 *Pengingat Agenda 2 Jam Lagi*',
            '1h'     => '⏱️ *Pengingat Agenda 1 Jam Lagi*',
            '30m'    => '⏱️ *Pengingat Agenda 30 Menit Lagi*',
            'custom' => '🔔 *Pengingat Agenda*',
            default  => '📋 *Konfirmasi Pendaftaran Pengingat*',
        };

        $lines = [
            $typeLabel,
            '',
            'Berikut agenda yang Anda tandai untuk diingat:',
            '',
        ];

        foreach ($agendas as $agenda) {
            $waktuMulai = $agenda->waktu_mulai?->translatedFormat('d M Y, H:i') ?? '-';
            $lines[] = "📌 *{$agenda->perihal_kegiatan}*";
            $lines[] = "   📅 {$waktuMulai} WIB";
            $lines[] = "   📍 {$agenda->tempat}";
            $lines[] = "   🔗 " . url('/agenda/' . $agenda->slug);
            $lines[] = '';
        }

        $lines[] = '---';
        $lines[] = '_Agenda eGov — Diskominfo Kabupaten Sambas_';

        return implode("\n", $lines);
    }

    private function normalizePhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert 08xxx to 628xxx
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Add 62 if doesn't start with it
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
