<?php

use App\Enums\SettingTypeEnum;
use App\Models\Setting;
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$maps = Setting::find(SettingTypeEnum::MAPS())?->value ?? [];
$key = $maps['mapplsStaticKey']
    ?? env('MAPPLS_STATIC_KEY')
    ?? env('NEXT_PUBLIC_MAPPLS_STATIC_KEY')
    ?? '';

$sdkUrl = 'https://sdk.mappls.com/map/sdk/web?v=3.0&access_token=' . rawurlencode($key);
$parts = parse_url($sdkUrl);
$query = [];
parse_str($parts['query'] ?? '', $query);

$result = [
    'mappls_key_configured' => $key !== '',
    'mappls_key_length' => strlen((string) $key),
    'sdk_origin' => ($parts['scheme'] ?? '') . '://' . ($parts['host'] ?? ''),
    'sdk_path' => $parts['path'] ?? '',
    'sdk_version' => $query['v'] ?? null,
    'access_token_present' => array_key_exists('access_token', $query) && $query['access_token'] !== '',
    'access_token_length' => strlen((string) ($query['access_token'] ?? '')),
    'probe_status' => null,
    'probe_content_type' => null,
    'probe_error' => null,
    'probe_body_start' => null,
];

if ($key !== '') {
    if (function_exists('curl_init')) {
        $ch = curl_init($sdkUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'Mozilla/5.0 MapplsDiagnostics/1.0',
            CURLOPT_HTTPHEADER => ['Accept: application/javascript,text/javascript,*/*;q=0.8'],
            CURLOPT_REFERER => 'http://127.0.0.1:8000/admin/settings/maps',
        ]);

        $body = curl_exec($ch);
        $result['probe_status'] = curl_getinfo($ch, CURLINFO_RESPONSE_CODE) ?: null;
        $result['probe_content_type'] = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: null;

        if ($body === false) {
            $result['probe_error'] = curl_error($ch) ?: 'cURL request failed';
        } else {
            $result['probe_body_start'] = substr(preg_replace('/\s+/', ' ', $body), 0, 180);
        }

        curl_close($ch);
    } else {
        $result['probe_error'] = 'PHP cURL extension is not available';
    }
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
