<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SettingTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapplsProxyController extends Controller
{
    private const AUTOSUGGEST_URL = 'https://search.mappls.com/search/places/autosuggest/json';
    private const REVERSE_GEOCODE_URL = 'https://search.mappls.com/search/address/rev-geocode';
    private const GEOCODE_URL = 'https://search.mappls.com/search/address/geocode';
    private const PLACE_DETAILS_URL = 'https://place.mappls.com/O2O/entity/place-details';

    public function autosuggest(Request $request): JsonResponse
    {
        return $this->proxy(self::AUTOSUGGEST_URL, [
            'query' => $request->query('query'),
            'region' => $request->query('region', 'IND'),
        ]);
    }

    public function reverseGeocode(Request $request): JsonResponse
    {
        return $this->proxy(self::REVERSE_GEOCODE_URL, [
            'lat' => $request->query('lat'),
            'lng' => $request->query('lng'),
            'region' => $request->query('region', 'IND'),
        ]);
    }

    public function geocode(Request $request): JsonResponse
    {
        return $this->proxy(self::GEOCODE_URL, [
            'address' => $request->query('address'),
            'itemCount' => $request->query('itemCount', 1),
        ]);
    }

    public function placeDetails(Request $request): JsonResponse
    {
        $eloc = (string) $request->query('eloc', '');
        if ($eloc === '') {
            return response()->json(['success' => false, 'message' => 'Missing Mappls place id.', 'data' => null], 422);
        }

        return $this->proxy(self::PLACE_DETAILS_URL . '/' . rawurlencode($eloc), []);
    }

    private function proxy(string $url, array $params): JsonResponse
    {
        $key = $this->staticKey();
        if ($key === '') {
            return response()->json(['success' => false, 'message' => 'Mappls Static Key is not configured.', 'data' => null], 503);
        }

        $filteredParams = array_filter($params, fn($value) => $value !== null && $value !== '');
        $filteredParams['access_token'] = $key;

        try {
            $response = Http::timeout(10)->acceptJson()->get($url, $filteredParams);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Unable to reach Mappls service.', 'data' => null], 502);
        }

        return response()->json([
            'success' => $response->successful(),
            'message' => $response->successful() ? null : 'Mappls request failed.',
            'data' => $response->json(),
        ], $response->status());
    }

    private function staticKey(): string
    {
        $setting = Setting::find(SettingTypeEnum::MAPS());
        $value = $setting?->value ?? [];

        return (string) (
            $value['mapplsStaticKey']
            ?? env('MAPPLS_STATIC_KEY')
            ?? env('NEXT_PUBLIC_MAPPLS_STATIC_KEY')
            ?? ''
        );
    }
}
