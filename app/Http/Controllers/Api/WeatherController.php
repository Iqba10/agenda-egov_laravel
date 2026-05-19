<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    public function __invoke(Request $request)
    {
        $apiKey = config('services.weather.key', '5c41d03a2b63015df9e69c516beca7ff');
        $defaultCity = config('services.weather.city', 'Sambas');

        // Koordinat Sambas, Kalimantan Barat
        $lat = $request->query('lat', '1.361');
        $lon = $request->query('lon', '109.305');

        $cacheKey = "weather_{$lat}_{$lon}";

        $data = Cache::remember($cacheKey, 600, function () use ($lat, $lon, $defaultCity, $apiKey) {
            // Coba OpenWeatherMap dulu
            $owmData = $this->fetchFromOpenWeatherMap($lat, $lon, $apiKey);
            if ($owmData) {
                return $owmData;
            }

            // Fallback ke Open-Meteo (gratis, tanpa API key)
            $openMeteoData = $this->fetchFromOpenMeteo($lat, $lon);
            if ($openMeteoData) {
                return $openMeteoData;
            }

            return null;
        });

        if ($data) {
            return response()->json($data);
        }

        return response()->json(['error' => 'Tidak tersedia'], 404);
    }

    private function fetchFromOpenWeatherMap($lat, $lon, $apiKey)
    {
        try {
            $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=id";
            
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for local dev
            ])->timeout(5)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['cod']) && $data['cod'] == 200) {
                    return [
                        'temp' => round($data['main']['temp']),
                        'condition' => ucfirst($data['weather'][0]['description']),
                        'humidity' => $data['main']['humidity'],
                        'wind' => round($data['wind']['speed'] * 3.6, 1), // m/s ke km/h
                        'source' => 'openweathermap'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('OpenWeatherMap API error: ' . $e->getMessage());
        }

        return null;
    }

    private function fetchFromOpenMeteo($lat, $lon)
    {
        try {
            $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&timezone=Asia/Jakarta";
            
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(5)->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['current'])) {
                    $current = $data['current'];
                    return [
                        'temp' => round($current['temperature_2m']),
                        'condition' => $this->getWeatherCondition($current['weather_code']),
                        'humidity' => $current['relative_humidity_2m'],
                        'wind' => round($current['wind_speed_10m'], 1),
                        'source' => 'open-meteo'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Open-Meteo API error: ' . $e->getMessage());
        }

        return null;
    }

    private function getWeatherCondition($code)
    {
        $conditions = [
            0 => 'Cerah',
            1 => 'Cerah Berawan',
            2 => 'Berawan Sebagian',
            3 => 'Berawan',
            45 => 'Berkabut',
            48 => 'Berkabut Tebal',
            51 => 'Gerimis Ringan',
            53 => 'Gerimis',
            55 => 'Gerimis Lebat',
            61 => 'Hujan Ringan',
            63 => 'Hujan',
            65 => 'Hujan Lebat',
            71 => 'Salju Ringan',
            73 => 'Salju',
            75 => 'Salju Lebat',
            80 => 'Hujan Ringan',
            81 => 'Hujan',
            82 => 'Hujan Lebat',
            95 => 'Badai Petir',
            96 => 'Badai Petir dengan Hujan Es',
            99 => 'Badai Petir Hebat',
        ];

        return $conditions[$code] ?? 'Tidak Diketahui';
    }
}
