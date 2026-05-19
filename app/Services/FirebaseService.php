<?php

namespace App\Services;

use App\Models\UserFcmToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected ?Messaging $messaging = null;
    protected ?array $status = null;

    public function __construct(protected FirebaseConfigService $firebaseConfigService)
    {
        $this->status = $this->firebaseConfigService->getServiceAccountStatus();

        if (!($this->status['valid'] ?? false)) {
            Log::warning('Firebase service account validation failed', $this->status ?? []);
            return;
        }

        try {
            $firebase = (new Factory)->withServiceAccount($this->status['credentials']);
            $this->messaging = $firebase->createMessaging();

        } catch (\Throwable $e) {
            Log::error('Firebase initialization failed', [
                'error' => $e->getMessage(),
            ]);

            $this->messaging = null; // explicitly disable
        }
    }

    /**
     * Send single notification
     */
    public function sendNotification($token, $title, $body, $image = "", $data = []): array
    {
        if ($this->messaging === null) {
            return [
                'success' => false,
                'error' => 'Firebase is not configured',
                'details' => $this->status,
            ];
        }

        try {
            $notification = Notification::create(
                title: $title,
                body: $body,
                imageUrl: $image
            );

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data)
                ->withDefaultSounds()
                ->toToken($token);

            $this->messaging->send($message);

            return ['success' => true];

        } catch (\Throwable $e) {
            Log::error('Firebase sendNotification failed', [
                'error' => $e->getMessage(),
                'token' => $token,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send bulk notifications
     */
    public function sendBulkNotification(
        array $tokens,
        string $title,
        string $body,
        string $image = null,
        array $data = [],
        int $chunkSize = 50
    ): array {
        if ($this->messaging === null) {
            Log::warning('Firebase bulk send skipped: not configured');

            return [
                'success' => 0,
                'failure' => count($tokens),
                'error' => 'Firebase is not configured',
                'details' => $this->status,
            ];
        }

        $results = [
            'success' => 0,
            'failure' => 0,
            'removed_tokens' => [],
        ];

        $notification = Notification::create(
            title: $title,
            body: $body,
            imageUrl: $image
        );

        Collection::make($tokens)->chunk($chunkSize)->each(function ($chunk) use (&$results, $notification, $data) {
            try {
                $message = CloudMessage::new()
                    ->withNotification($notification)
                    ->withData($data)
                    ->withDefaultSounds();

                $multicastResult = $this->messaging->sendMulticast(
                    $message,
                    $chunk->toArray()
                );

                $results['success'] += $multicastResult->successes()->count();
                $results['failure'] += $multicastResult->failures()->count();

                $invalidTokens = $multicastResult->invalidTokens();

                if (!empty($invalidTokens)) {
                    UserFcmToken::whereIn('fcm_token', $invalidTokens)->delete();
                    $results['removed_tokens'] = array_merge(
                        $results['removed_tokens'],
                        $invalidTokens
                    );
                }

            } catch (\Throwable $e) {
                Log::error('Firebase bulk send chunk failed', [
                    'error' => $e->getMessage(),
                ]);

                $results['failure'] += $chunk->count();
            }
        });

        return $results;
    }
}
