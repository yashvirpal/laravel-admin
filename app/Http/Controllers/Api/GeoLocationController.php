<?php
// app/Http/Controllers/Api/GeoLocationController.php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeoLocationController extends Controller
{
    public function getCountry(Request $request)
    {
        $ip = $request->ip();

        // Skip for local development
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return response()->json([
                'success' => true,
                'country_code' => 'IN',
                'country_name' => 'India',
                'ip' => $ip
            ]);
        }

        // Cache country for 24 hours per IP
        $cacheKey = "geo_country_{$ip}";

        $countryData = Cache::remember($cacheKey, 86400, function () use ($ip) {
            return $this->detectCountry($ip);
        });

        return response()->json($countryData);
    }

    private function detectCountry($ip)
    {
        // Try ipapi.co first
        try {
            $response = Http::timeout(3)->get("https://ipapi.co/{$ip}/json/");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'country_code' => $data['country_code'] ?? 'IN',
                    'country_name' => $data['country_name'] ?? 'India',
                    'ip' => $ip,
                    'source' => 'ipapi.co'
                ];
            }
        } catch (\Exception $e) {
            \Log::warning('ipapi.co failed: ' . $e->getMessage());
        }

        // Fallback to ip-api.com
        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'country_code' => $data['countryCode'] ?? 'IN',
                    'country_name' => $data['country'] ?? 'India',
                    'ip' => $ip,
                    'source' => 'ip-api.com'
                ];
            }
        } catch (\Exception $e) {
            \Log::warning('ip-api.com failed: ' . $e->getMessage());
        }

        // Default fallback
        return [
            'success' => false,
            'country_code' => 'IN',
            'country_name' => 'India',
            'ip' => $ip,
            'source' => 'fallback'
        ];
    }
}