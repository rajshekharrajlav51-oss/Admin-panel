<?php

namespace App\Types\Settings;

use App\Interfaces\SettingInterface;
use App\Traits\SettingTrait;

class MapsSettingType implements SettingInterface
{
    use SettingTrait;

    public string $mapProvider = 'mappls';
    public string $googleMapKey = '';
    public string $mapplsStaticKey = '';
    public string $defaultLatitude = '28.6139';
    public string $defaultLongitude = '77.2090';
    public string $defaultZoom = '13';

    protected static function getValidationRules(): array
    {
        return [
            'mapProvider' => 'required|in:mappls,google',
            'googleMapKey' => 'nullable|string|max:255|required_if:mapProvider,google',
            'mapplsStaticKey' => 'nullable|string|max:255',
            'defaultLatitude' => 'nullable|numeric|between:-90,90',
            'defaultLongitude' => 'nullable|numeric|between:-180,180',
            'defaultZoom' => 'nullable|integer|min:1|max:22',
        ];
    }
}
