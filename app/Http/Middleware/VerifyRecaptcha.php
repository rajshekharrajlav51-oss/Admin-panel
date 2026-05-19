<?php

namespace App\Http\Middleware;

use App\Enums\SettingTypeEnum;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $settings = Setting::where('variable', SettingTypeEnum::AUTHENTICATION())->first()?->value ?? [];
        } catch (\Throwable) {
            return $next($request);
        }

        $siteKey = trim((string) ($settings['googleRecaptchaSiteKey'] ?? ''));
        $secretKey = trim((string) ($settings['googleRecaptchaSecretKey'] ?? ''));

        // Keep reCAPTCHA optional until both keys are configured.
        if ($siteKey === '' && $secretKey === '') {
            return $next($request);
        }

        if ($siteKey === '' || $secretKey === '') {
            return response()->json([
                'success' => false,
                'message' => 'reCAPTCHA is not configured properly.',
                'data' => [
                    'site_key_configured' => $siteKey !== '',
                    'secret_key_configured' => $secretKey !== '',
                ],
            ], 503);
        }

        $token = $request->input('recaptcha_token', $request->input('g-recaptcha-response'));

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'reCAPTCHA token is required.',
                'data' => [],
            ], 422);
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to verify reCAPTCHA.',
                    'data' => [
                        'status' => $response->status(),
                    ],
                ], 502);
            }

            $payload = $response->json() ?? [];
            if (!($payload['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'reCAPTCHA verification failed.',
                    'data' => [
                        'error_codes' => $payload['error-codes'] ?? [],
                    ],
                ], 422);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'reCAPTCHA verification failed.',
                'data' => [
                    'error' => $e->getMessage(),
                ],
            ], 502);
        }

        return $next($request);
    }
}
