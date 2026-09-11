<?php

namespace App\Http\Resources\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapsSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mapplsStaticKey = $this->value['mapplsStaticKey']
            ?? env('MAPPLS_STATIC_KEY')
            ?? env('NEXT_PUBLIC_MAPPLS_STATIC_KEY')
            ?? '';

        return [
            'variable' => $this->variable,
            'value' => [
                'mapProvider' => 'google',
                'googleMapKey' => $this->value['googleMapKey'] ?? config('services.google.maps_api_key', ''),
                'mapplsStaticKey' => $mapplsStaticKey,
                'mapplsStaticKeyConfigured' => !empty($mapplsStaticKey),
                'defaultLatitude' => $this->value['defaultLatitude'] ?? '28.6139',
                'defaultLongitude' => $this->value['defaultLongitude'] ?? '77.2090',
                'defaultZoom' => $this->value['defaultZoom'] ?? '13',
            ],
        ];
    }
}
