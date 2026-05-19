<?php

namespace App\Http\Resources\Setting;

use App\Services\NotificationService;
use App\Services\FirebaseConfigService;
use App\Traits\PanelAware;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $firebaseConfig = app(FirebaseConfigService::class);
        $status = $firebaseConfig->getServiceAccountStatus();
        $path = $status['path'] ?? $firebaseConfig->getServiceAccountPath();
        $jsonContent = $status['credentials'] ?? [];

        // Only admin panel can access serviceAccountFile

        if ($this->getPanel() === 'admin') {
            $data['value']['serviceAccountFile'] = $this->resource->value['serviceAccountFile'] ?? '';
            $data['value']['serviceAccountFileExist'] = file_exists($path);
            $data['value']['serviceAccountFileData'] = $jsonContent;
            $data['value']['serviceAccountFileStatus'] = $status;
        }

        return $data;
    }
}
