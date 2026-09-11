<?php

namespace App\Http\Resources\Setting;

use App\Services\NotificationService;
use App\Traits\PanelAware;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class NotificationSettingResource extends JsonResource
{
    use PanelAware;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::guard('sanctum')->user();
        $notification = app(NotificationService::class);
        $data = [
            'variable' => $this->variable,
            'value' => [
                'firebaseProjectId' => $this->resource->value['firebaseProjectId'] ?? '',
                'userRequest' => $this->resource->value['userRequest'] ?? '',
                'vapIdKey' => $this->resource->value['vapIdKey'] ?? '',
                'notification_unread_count' => $user?->id ? $notification->getUnreadCount($user->id) : 0
            ]
        ];

        // Only admin panel can access serviceAccountFile

        if ($this->getPanel() === 'admin') {
            $path = storage_path('app/private/settings/service-account-file.json');
            $serviceAccountFileExists = is_file($path) && is_readable($path);
            $jsonContent = null;

            if ($serviceAccountFileExists) {
                $json = file_get_contents($path);
                $decoded = json_decode($json ?: '', true);
                $jsonContent = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
            }

            $data['value']['serviceAccountFile'] = $this->resource->value['serviceAccountFile'] ?? '';
            $data['value']['serviceAccountFileExist'] = $serviceAccountFileExists;
            $data['value']['serviceAccountFileData'] = $jsonContent;
        }

        return $data;
    }
}
