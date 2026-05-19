<?php

namespace App\Services;

use App\Enums\SettingTypeEnum;
use App\Models\Setting;

class FirebaseConfigService
{
    public const SERVICE_ACCOUNT_PATH = 'app/private/settings/service-account-file.json';

    public function getAuthSettings(): array
    {
        try {
            return Setting::where('variable', SettingTypeEnum::AUTHENTICATION())->first()?->value ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function getNotificationSettings(): array
    {
        try {
            return Setting::where('variable', SettingTypeEnum::NOTIFICATION())->first()?->value ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function isFirebaseEnabled(): bool
    {
        $settings = $this->getAuthSettings();

        return !empty($settings['firebase']);
    }

    public function isCustomSmsEnabled(): bool
    {
        $settings = $this->getAuthSettings();

        return !empty($settings['customSms']);
    }

    public function getServiceAccountPath(): string
    {
        return storage_path(self::SERVICE_ACCOUNT_PATH);
    }

    public function getServiceAccountStatus(): array
    {
        $path = $this->getServiceAccountPath();

        if (!file_exists($path)) {
            return [
                'valid' => false,
                'message' => 'Firebase service account file is missing.',
                'errors' => ['missing_file'],
                'path' => $path,
            ];
        }

        $raw = file_get_contents($path);
        $decoded = json_decode($raw, true);

        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            return [
                'valid' => false,
                'message' => 'Firebase service account JSON is invalid.',
                'errors' => ['invalid_json'],
                'path' => $path,
            ];
        }

        return $this->validateServiceAccountData($decoded, $path);
    }

    public function validateServiceAccountData(array $decoded, ?string $path = null): array
    {
        $requiredKeys = ['project_id', 'client_email', 'private_key'];
        $missingKeys = [];

        foreach ($requiredKeys as $key) {
            if (empty($decoded[$key]) || !is_string($decoded[$key])) {
                $missingKeys[] = $key;
            }
        }

        if (($decoded['type'] ?? null) !== 'service_account') {
            $missingKeys[] = 'type=service_account';
        }

        if (!empty($missingKeys)) {
            return [
                'valid' => false,
                'message' => 'Firebase service account JSON is missing required Admin SDK keys.',
                'errors' => $missingKeys,
                'path' => $path ?? $this->getServiceAccountPath(),
                'project_id' => $decoded['project_id'] ?? null,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Firebase service account is valid.',
            'errors' => [],
            'path' => $path ?? $this->getServiceAccountPath(),
            'project_id' => $decoded['project_id'],
            'credentials' => $decoded,
        ];
    }

    public function getFrontendConfig(): array
    {
        $auth = $this->getAuthSettings();
        $notification = $this->getNotificationSettings();

        return [
            'apiKey' => $auth['fireBaseApiKey'] ?? '',
            'authDomain' => $auth['fireBaseAuthDomain'] ?? '',
            'projectId' => $auth['fireBaseProjectId'] ?? '',
            'storageBucket' => $auth['fireBaseStorageBucket'] ?? '',
            'messagingSenderId' => $auth['fireBaseMessagingSenderId'] ?? '',
            'appId' => $auth['fireBaseAppId'] ?? '',
            'measurementId' => $auth['fireBaseMeasurementId'] ?? '',
            'vapidKey' => $notification['vapIdKey'] ?? '',
        ];
    }

    public function validateFrontendConfig(): array
    {
        $config = $this->getFrontendConfig();
        $requiredKeys = ['apiKey', 'authDomain', 'projectId', 'messagingSenderId', 'appId'];
        $missingKeys = [];

        foreach ($requiredKeys as $key) {
            if (empty($config[$key])) {
                $missingKeys[] = $key;
            }
        }

        return [
            'valid' => empty($missingKeys),
            'message' => empty($missingKeys)
                ? 'Firebase frontend configuration is valid.'
                : 'Firebase frontend configuration is incomplete.',
            'errors' => $missingKeys,
            'config' => $config,
        ];
    }

    public function getFirebaseRuntimeStatus(): array
    {
        $serviceAccount = $this->getServiceAccountStatus();
        $frontend = $this->validateFrontendConfig();

        return [
            'enabled' => $this->isFirebaseEnabled(),
            'service_account' => $serviceAccount,
            'frontend' => $frontend,
        ];
    }
}
