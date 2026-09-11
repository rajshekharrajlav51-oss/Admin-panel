<?php

namespace App\Http\Resources\Setting;

use App\Enums\SettingTypeEnum;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $systemLogo = Setting::find(SettingTypeEnum::SYSTEM())?->value['logo'] ?? '';

        return [
            'variable' => $this->variable,
            'value' => [
                'siteName' => $this->value['siteName'] ?? '',
                'customerWebUrl' => $this->value['customerWebUrl'] ?? '',
                'siteCopyright' => $this->value['siteCopyright'] ?? '',
                'supportNumber' => $this->value['supportNumber'] ?? '',
                'supportEmail' => $this->value['supportEmail'] ?? '',
                'address' => $this->value['address'] ?? '',
                'shortDescription' => $this->value['shortDescription'] ?? '',
                'siteHeaderLogo' => $this->storageUrl(($this->value['siteHeaderLogo'] ?? '') ?: $systemLogo, true),
                'siteHeaderDarkLogo' => $this->storageUrl(($this->value['siteHeaderDarkLogo'] ?? '') ?: $systemLogo, true),

                'siteFooterLogo' => $this->storageUrl(($this->value['siteFooterLogo'] ?? '') ?: $systemLogo, true),

                'siteFavicon' => $this->storageUrl($this->value['siteFavicon'] ?? ''),
                'headerScript' => $this->value['headerScript'] ?? '',
                'footerScript' => $this->value['footerScript'] ?? '',
                'googleMapKey' => $this->value['googleMapKey'] ?? '',
                'mapIframe' => $this->value['mapIframe'] ?? '',
                'appDownloadSection' => $this->value['appDownloadSection'] ?? false,
                'appSectionTitle' => $this->value['appSectionTitle'] ?? '',
                'appSectionTagline' => $this->value['appSectionTagline'] ?? '',
                'appSectionPlaystoreLink' => $this->value['appSectionPlaystoreLink'] ?? '',
                'appSectionAppstoreLink' => $this->value['appSectionAppstoreLink'] ?? '',
                'appSectionShortDescription' => $this->value['appSectionShortDescription'] ?? '',
                'facebookLink' => $this->value['facebookLink'] ?? '',
                'instagramLink' => $this->value['instagramLink'] ?? '',
                'xLink' => $this->value['xLink'] ?? '',
                'youtubeLink' => $this->value['youtubeLink'] ?? '',
                'shippingFeatureSection' => $this->value['shippingFeatureSection'] ?? '',
                'shippingFeatureSectionTitle' => $this->value['shippingFeatureSectionTitle'] ?? '',
                'shippingFeatureSectionDescription' => $this->value['shippingFeatureSectionDescription'] ?? '',
                'returnFeatureSection' => $this->value['returnFeatureSection'] ?? '',
                'returnFeatureSectionTitle' => $this->value['returnFeatureSectionTitle'] ?? '',
                'returnFeatureSectionDescription' => $this->value['returnFeatureSectionDescription'] ?? '',
                'safetySecurityFeatureSection' => $this->value['safetySecurityFeatureSection'] ?? '',
                'safetySecurityFeatureSectionTitle' => $this->value['safetySecurityFeatureSectionTitle'] ?? '',
                'safetySecurityFeatureSectionDescription' => $this->value['safetySecurityFeatureSectionDescription'] ?? '',
                'supportFeatureSection' => $this->value['supportFeatureSection'] ?? '',
                'supportFeatureSectionTitle' => $this->value['supportFeatureSectionTitle'] ?? '',
                'supportFeatureSectionDescription' => $this->value['supportFeatureSectionDescription'] ?? '',
                'metaKeywords' => $this->value['metaKeywords'] ?? '',
                'metaDescription' => $this->value['metaDescription'] ?? '',
                'defaultLatitude' => $this->value['defaultLatitude'] ?? '',
                'defaultLongitude' => $this->value['defaultLongitude'] ?? '',
                'enableCountryValidation' => $this->value['enableCountryValidation'] ?? '',
                'allowedCountries' => $this->allowedCountries ?? [],
                'returnRefundPolicy' => $this->value['returnRefundPolicy'] ?? '',
                'shippingPolicy' => $this->value['shippingPolicy'] ?? '',
                'privacyPolicy' => $this->value['privacyPolicy'] ?? '',
                'termsCondition' => $this->value['termsCondition'] ?? '',
                'aboutUs' => $this->value['aboutUs'] ?? '',
                'pwaName' => $this->value['pwaName'] ?? '',
                'pwaDescription' => $this->value['pwaDescription'] ?? '',
                'pwaLogo144x144' => $this->storageUrl($this->value['pwaLogo144x144'] ?? ''),
                'pwaLogo192x192' => $this->storageUrl($this->value['pwaLogo192x192'] ?? ''),
                'pwaLogo512x512' => $this->storageUrl($this->value['pwaLogo512x512'] ?? ''),
            ]
        ];
    }

    private function storageUrl(?string $path, bool $versioned = false): string
    {
        if (empty($path)) {
            return '';
        }

        $url = url('storage/' . ltrim($path, '/'));

        return $versioned ? $url . '?v=' . time() : $url;
    }
}
