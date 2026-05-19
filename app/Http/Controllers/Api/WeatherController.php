<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    public function __invoke(Request $request)
    {
        $apiKey = config('services.weather.key', '5c41d03a2b63015df9e69c516beca7ff');
        $defaultCity = config('services.weather.city', 'Sambas');

        $lat = $request->query('lat');
        $lon = $request->query('lon');

        $cacheKey = $lat && $lon ? "weather_{$lat}_{$lon}" : "weather_{$defaultCity}";

        $data = Cache::remember($cacheKey, 600, function () use ($lat, $lon, $defaultCity, $apiKey) {
            if ($lat && $lon) {
                $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=id";
            } else {
                $url = "https://api.openweathermap.org/data/2.5/weather?q={$defaultCity}&appid={$apiKey}&units=metric&lang=id";
            }

            $response = Http::timeout(5)->get($url);
            
            return $response->successful() ? $response->json() : null;
        });

        if ($data && isset($data['cod']) && $data['cod'] == 200) {
            $city = $data['name'];
            $desc = ucfirst($data['weather'][0]['description']);
            $temp = round($data['main']['temp']);
            $humidity = $data['main']['humidity'];
            $wind = $data['wind']['speed'];
            
            return response()->json([
                'temp' => $temp,
                'condition' => $desc,
                'humidity' => $humidity,
                'wind' => $wind
            ]);
        }

        return response()->json(['error' => 'Tidak tersedia'], 404);
    }
}
