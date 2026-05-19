<?php

namespace App\Services;

use App\Enums\SettingTypeEnum;
use App\Models\UserOtp;
use Illuminate\Support\Facades\Log;

class OtpService
{
    protected SmsService $smsService;
    protected FirebaseConfigService $firebaseConfigService;
    protected SettingService $settingService;

    public function __construct(
        SmsService $smsService,
        FirebaseConfigService $firebaseConfigService,
        SettingService $settingService
    )
    {
        $this->smsService = $smsService;
        $this->firebaseConfigService = $firebaseConfigService;
        $this->settingService = $settingService;
    }

    /**
     * Generate a 6-digit OTP code
     */
    private function generateOtpCode(): string
    {
        return str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Sanitize mobile number to 10 digits
     */
    public function sanitizeMobile(string $mobile): string
    {
        // Remove all non-numeric characters
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        // If it's a 12-digit number starting with 91, remove the 91
        if (strlen($mobile) === 12 && str_starts_with($mobile, '91')) {
            $mobile = substr($mobile, 2);
        }

        // Return the last 10 digits to be safe (if length >= 10)
        if (strlen($mobile) > 10) {
            $mobile = substr($mobile, -10);
        }

        return $mobile;
    }

    /**
     * Generate and store OTP for a mobile number
     */
    public function generateOtp(string $mobile): array
    {
        try {
            $mobile = $this->sanitizeMobile($mobile);

            // Generate new OTP
            $otpCode = $this->generateOtpCode();
            $expiresAt = now()->addMinutes(10);

            Log::info('Generating OTP', [
                'mobile' => $mobile,
                'otp_code' => $otpCode,
                'expires_at' => $expiresAt
            ]);

            // Create or update OTP for the mobile number
            // This replaces the entry if one exists, ensuring we don't create new rows unnecessarily
            $userOtp = UserOtp::updateOrCreate(
                ['mobile' => $mobile],
                [
                    'otp' => $otpCode,
                    'expires_at' => $expiresAt,
                    'attempts' => 0,
                    'verified_at' => null
                ]
            );

            Log::info('OTP stored in database', [
                'mobile' => $mobile,
                'otp_code' => $otpCode,
                'user_otp_id' => $userOtp->id,
                'expires_at' => $userOtp->expires_at
            ]);

            return [
                'success' => true,
                'otp_code' => $otpCode,
                'expires_at' => $expiresAt,
                'message' => 'OTP generated successfully'
            ];

        } catch (\Exception $e) {
            Log::error('OTP generation failed', [
                'mobile' => $mobile,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to generate OTP'
            ];
        }
    }

    /**
     * Generate OTP and send via SMS
     */
    public function sendOtp(string $mobile): array
    {
        $sanitizedMobile = $this->sanitizeMobile($mobile);
        $gateway = $this->getOtpGateway();

        // Debug logging
        Log::info('OTP Send Attempt', [
            'input_mobile' => $mobile,
            'sanitized_mobile' => $sanitizedMobile,
            'gateway' => $gateway,
        ]);

        if ($gateway === 'firebase') {
            $firebaseStatus = $this->firebaseConfigService->getFirebaseRuntimeStatus();

            if (!($firebaseStatus['service_account']['valid'] ?? false)) {
                return [
                    'success' => false,
                    'message' => 'Firebase Phone Auth is enabled, but Firebase service account is invalid.',
                    'debug' => $firebaseStatus['service_account'] ?? [],
                ];
            }

            if (!($firebaseStatus['frontend']['valid'] ?? false)) {
                return [
                    'success' => false,
                    'message' => 'Firebase Phone Auth is enabled, but frontend Firebase configuration is incomplete.',
                    'debug' => $firebaseStatus['frontend'] ?? [],
                ];
            }

            return [
                'success' => true,
                'message' => 'Firebase Phone Auth is enabled. Complete OTP verification from the frontend Firebase SDK.',
                'expires_in' => null,
                'channel' => 'firebase',
            ];
        }

        if ($gateway !== 'custom') {
            return [
                'success' => false,
                'message' => 'No OTP gateway is enabled.',
            ];
        }

        // Generate OTP
        $otpResult = $this->generateOtp($sanitizedMobile);

        if (!$otpResult['success']) {
            return $otpResult;
        }

        // Send OTP via SMS
        // Twilio and most SMS gateways expect +91 for India if not already present
        $smsMobile = $sanitizedMobile;
        if (strlen($smsMobile) === 10) {
            $smsMobile = '+91' . $smsMobile;
        }

        $message = "Your OTP is: {$otpResult['otp_code']}. Valid for 10 minutes.";

        Log::info('Sending SMS', [
            'sms_mobile' => $smsMobile,
            'otp_code' => $otpResult['otp_code'],
            'message' => $message
        ]);

        $smsResult = $this->smsService->sendSms($smsMobile, $message);

        if (!$smsResult['success']) {
            Log::error('OTP SMS sending failed', [
                'mobile' => $smsMobile,
                'error' => $smsResult
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send OTP: ' . $smsResult['message'],
                'debug' => $smsResult['debug'] ?? null
            ];
        }

        Log::info('OTP sent successfully', [
            'mobile' => $sanitizedMobile,
            'otp_code' => $otpResult['otp_code']
        ]);

        return [
            'success' => true,
            'message' => 'OTP sent successfully',
            'expires_in' => 600 // 10 minutes in seconds
        ];
    }

    /**
     * Verify OTP code for a mobile number
     */
    public function verifyOtp(string $mobile, string $otpCode): array
    {
        if ($this->getOtpGateway() === 'firebase') {
            return [
                'success' => false,
                'message' => 'Firebase Phone Auth is enabled. Verify the phone number on the frontend and then call auth/phone/callback.',
            ];
        }

        $mobile = $this->sanitizeMobile($mobile);

        // Debug logging
        Log::info('OTP Verification Attempt', [
            'input_mobile' => $mobile,
            'input_otp' => $otpCode
        ]);

        // Find active OTP for this mobile
        $userOtp = UserOtp::forMobile($mobile)
            ->active()
            ->orderBy('created_at', 'desc')
            ->first();

        // Debug: Check what we found
        Log::info('OTP Lookup Result', [
            'found' => $userOtp ? true : false,
            'lookup_mobile' => $mobile
        ]);

        if ($userOtp) {
            Log::info('OTP Details Found', [
                'otp_code' => $userOtp->otp,
                'expires_at' => $userOtp->expires_at,
                'verified_at' => $userOtp->verified_at,
                'attempts' => $userOtp->attempts,
                'is_expired' => $userOtp->isExpired(),
                'mobile_in_db' => $userOtp->mobile
            ]);
        }

        if (!$userOtp) {
            // Debug: check if any OTP exists for this mobile (expired or verified)
            $anyOtp = UserOtp::forMobile($mobile)->orderBy('created_at', 'desc')->first();
            Log::warning('OTP verification failed - no active OTP', [
                'mobile_searched' => $mobile,
                'any_record_exists' => $anyOtp ? true : false,
                'record_details' => $anyOtp ? [
                    'expires_at' => $anyOtp->expires_at,
                    'verified_at' => $anyOtp->verified_at,
                    'attempts' => $anyOtp->attempts,
                    'is_expired' => $anyOtp->expires_at < now(),
                    'mobile_in_db' => $anyOtp->mobile,
                ] : null,
            ]);
            return [
                'success' => false,
                'message' => 'No active OTP found. Please request a new OTP.'
            ];
        }

        // Check if expired
        if ($userOtp->isExpired()) {
            return [
                'success' => false,
                'message' => 'OTP has expired. Please request a new OTP.'
            ];
        }

        // Check if max attempts reached
        if ($userOtp->maxAttemptsReached()) {
            return [
                'success' => false,
                'message' => 'Maximum verification attempts reached. Please request a new OTP.'
            ];
        }

        // Verify OTP code
        // Since we removed encryption accessors in the model, $userOtp->otp is the plain text value
        if ((string)$userOtp->otp !== (string)$otpCode) {
            $userOtp->incrementAttempts();
            $remainingAttempts = 3 - $userOtp->attempts;

            return [
                'success' => false,
                'message' => "Invalid OTP. {$remainingAttempts} attempt(s) remaining.",
                'remaining_attempts' => $remainingAttempts
            ];
        }

        // OTP is valid - mark as verified
        $userOtp->markAsVerified();

        return [
            'success' => true,
            'message' => 'OTP verified successfully'
        ];
    }

    /**
     * Clean up expired OTPs (can be scheduled)
     */
    public function cleanExpiredOtps(): int
    {
        return UserOtp::where('expires_at', '<', now()->subDay())->delete();
    }

    public function getOtpGateway(): string
    {
        $authSetting = $this->settingService->getRawSetting(SettingTypeEnum::AUTHENTICATION());
        $config = $authSetting?->value ?? [];

        if (!empty($config['customSms'])) {
            return 'custom';
        }

        if (!empty($config['firebase'])) {
            return 'firebase';
        }

        return '';
    }
}
