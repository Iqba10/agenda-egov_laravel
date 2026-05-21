<?php

namespace App\Services;

use App\Models\Agenda;
use App\Models\FcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmSender
{
    private ?string $projectId;
    private ?string $credentialsPath;
    private ?string $credentialsJson;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->projectId       = config('services.firebase.project_id');
        $this->credentialsPath = config('services.firebase.credentials_path');
        $this->credentialsJson = config('services.firebase.credentials_json');
    }

    public function isConfigured(): bool
    {
        if (empty($this->projectId)) {
            return false;
        }
        
        // Check if credentials available (either file or JSON env)
        return $this->hasCredentials();
    }
    
    private function hasCredentials(): bool
    {
        // Option 1: JSON from env variable
        if (!empty($this->credentialsJson)) {
            return true;
        }
        
        // Option 2: File path
        if (!empty($this->credentialsPath) && file_exists($this->credentialsPath)) {
            return true;
        }
        
        return false;
    }
    
    private function getCredentials(): ?array
    {
        // Option 1: JSON from env variable
        if (!empty($this->credentialsJson)) {
            return json_decode($this->credentialsJson, true);
        }
        
        // Option 2: File path
        if (!empty($this->credentialsPath) && file_exists($this->credentialsPath)) {
            return json_decode(file_get_contents($this->credentialsPath), true);
        }
        
        return null;
    }

    public function send(string $token, string $title, string $body, array $data = []): bool
    {
        $result = $this->sendToToken($token, $title, $body, $data);
        return $result['success'];
    }

    public function sendToToken(string $token, string $title, string $body, array $data = []): array
    {
        if (!$this->isConfigured()) {
            Log::warning('FCM not configured - check FIREBASE_PROJECT_ID and credentials file');
            return ['success' => false, 'error' => 'FCM tidak dikonfigurasi'];
        }

        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return ['success' => false, 'error' => 'Gagal mendapatkan access token'];
            }

            $message = [
                'message' => [
                    'token'        => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'webpush' => [
                        'fcm_options' => [
                            'link' => $data['url'] ?? url('/'),
                        ],
                        'notification' => [
                            'icon'  => $data['icon'] ?? '/favicon.ico',
                            'badge' => $data['badge'] ?? '/favicon.ico',
                        ],
                    ],
                    'data' => array_map('strval', $data ?: ['type' => 'notification']),
                ],
            ];

            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type'  => 'application/json',
            ])->post($url, $message);

            if ($response->successful()) {
                Log::info('FCM notification sent', ['token' => substr($token, 0, 20) . '...']);
                return ['success' => true, 'data' => $response->json()];
            }

            // Handle invalid token - deactivate it
            if ($response->status() === 404 || $response->status() === 400) {
                $this->deactivateToken($token);
            }

            $errorBody = $response->json() ?? $response->body();
            Log::error('FCM API error', [
                'status' => $response->status(),
                'body'   => $errorBody,
            ]);
            $errorMsg = is_array($errorBody) && isset($errorBody['error']['message']) 
                ? $errorBody['error']['message'] 
                : 'HTTP ' . $response->status();
            return ['success' => false, 'error' => $errorMsg, 'data' => $errorBody];

        } catch (\Throwable $e) {
            Log::error('FCM exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
    {
        if (!$this->isConfigured()) {
            Log::warning('FCM not configured - check FIREBASE_PROJECT_ID and credentials file');
            return ['success' => false, 'error' => 'FCM tidak dikonfigurasi'];
        }

        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return ['success' => false, 'error' => 'Gagal mendapatkan access token'];
            }

            $message = [
                'message' => [
                    'topic'        => $topic,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'webpush' => [
                        'fcm_options' => [
                            'link' => $data['url'] ?? url('/'),
                        ],
                    ],
                    'data' => array_map('strval', $data ?: ['type' => 'broadcast']),
                ],
            ];

            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type'  => 'application/json',
            ])->post($url, $message);

            if ($response->successful()) {
                Log::info('FCM topic broadcast sent', ['topic' => $topic]);
                return ['success' => true, 'data' => $response->json()];
            }

            $errorBody = $response->json() ?? $response->body();
            Log::error('FCM topic broadcast error', [
                'status' => $response->status(),
                'body'   => $errorBody,
            ]);
            $errorMsg = is_array($errorBody) && isset($errorBody['error']['message']) 
                ? $errorBody['error']['message'] 
                : 'HTTP ' . $response->status();
            return ['success' => false, 'error' => $errorMsg, 'data' => $errorBody];

        } catch (\Throwable $e) {
            Log::error('FCM topic exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendToMultiple(array $tokens, string $title, string $body, array $data = []): int
    {
        $successCount = 0;
        foreach ($tokens as $token) {
            if ($this->send($token, $title, $body, $data)) {
                $successCount++;
            }
        }
        return $successCount;
    }

    public function sendAgendaReminder(string $token, Agenda $agenda, string $type = 'immediate'): bool
    {
        $typeLabel = match ($type) {
            '24h'   => 'Pengingat H-1',
            '6h'    => 'Pengingat 6 Jam Lagi',
            default => 'Pengingat Agenda',
        };

        $title = "{$typeLabel} — {$agenda->perihal_kegiatan}";
        $body  = $agenda->waktu_mulai?->translatedFormat('l, d F Y H:i') . ' WIB di ' . $agenda->tempat;

        return $this->send($token, $title, $body, [
            'agenda_id'  => (string) $agenda->id,
            'agenda_slug'=> $agenda->slug,
            'url'        => url('/agenda/' . $agenda->slug),
            'type'       => $type,
        ]);
    }

    public function sendToAgendaSubscribers(Agenda $agenda, string $type = 'immediate'): int
    {
        $tokens = FcmToken::active()
            ->whereJsonContains('subscribed_agendas', $agenda->id)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return 0;
        }

        $successCount = 0;
        foreach ($tokens as $token) {
            if ($this->sendAgendaReminder($token, $agenda, $type)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    private function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $credentials = $this->getCredentials();

            if (!$credentials || !isset($credentials['private_key']) || !isset($credentials['client_email'])) {
                Log::error('Invalid Firebase credentials');
                return null;
            }

            // Create JWT
            $now = time();
            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode([
                'iss'   => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]));

            $signature = '';
            $privateKey = openssl_pkey_get_private($credentials['private_key']);
            openssl_sign("{$header}.{$payload}", $signature, $privateKey, OPENSSL_ALGO_SHA256);
            $signature = base64_encode($signature);

            $jwt = "{$header}.{$payload}.{$signature}";

            // Exchange JWT for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if ($response->successful()) {
                $this->accessToken = $response->json('access_token');
                return $this->accessToken;
            }

            Log::error('Failed to get FCM access token', ['body' => $response->body()]);
            return null;

        } catch (\Throwable $e) {
            Log::error('FCM token generation error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function deactivateToken(string $token): void
    {
        FcmToken::where('token', $token)->update(['is_active' => false]);
    }

    /**
     * Subscribe token to a topic for broadcast messages
     */
    public function subscribeToTopic(string $token, string $topic = 'agenda-updates'): bool
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return false;
        }

        try {
            $response = Http::withToken($accessToken)
                ->withHeaders(['access_token_auth' => 'true'])
                ->post("https://iid.googleapis.com/iid/v1/{$token}/rel/topics/{$topic}");

            if ($response->successful()) {
                Log::info('FCM token subscribed to topic', ['topic' => $topic]);
                return true;
            }

            Log::warning('FCM topic subscription failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('FCM topic subscription error', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
