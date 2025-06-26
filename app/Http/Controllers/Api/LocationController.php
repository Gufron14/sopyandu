<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    protected static string $baseUrl = 'https://open-api.my.id/api/wilayah';

    // General reusable static method with caching (1 month)
    protected static function requestWilayah(string $path)
    {
        $cacheKey = 'wilayah' . md5($path); // Unique cache key based on the path

        // Cache for 1 month (43200 minutes)
        return Cache::remember($cacheKey, 43200, function () use ($path) {
            $response = Http::get(self::$baseUrl . $path);
            return $response->successful() ? $response->json() : [];
        });
    }

    // For API routes
    public function getProvinces()
    {
        return response()->json(self::requestWilayah('/provinces'));
    }

    public function getCities($province_id)
    {
        return response()->json(self::requestWilayah("/regencies/$province_id"));
    }

    public function getDistricts($city_id)
    {
        return response()->json(self::requestWilayah("/districts/$city_id"));
    }

    public function getVillages($district_id)
    {
        return response()->json(self::requestWilayah("/villages/$district_id"));
    }

    // Reusable static methods
    public static function getProvincesStatic()
    {
        return self::requestWilayah('/provinces');
    }

    public static function getCitiesStatic($province_id)
    {
        return self::requestWilayah("/regencies/$province_id");
    }

    public static function getDistrictsStatic($city_id)
    {
        return self::requestWilayah("/districts/$city_id");
    }

    public static function getVillagesStatic($district_id)
    {
        return self::requestWilayah("/villages/$district_id");
    }

    // Optional helper: get name by ID from a list
    public static function getNameById($list, $id)
    {
        return collect($list)->firstWhere('id', $id)['name'] ?? 'N/A';
    }
}
