<?php

namespace App\Http\Controllers;

use App\Enums\AdminPermissionEnum;
use App\Enums\SettingTypeEnum;
use App\Events\DeliveryZone\DeliveryZoneAfterCreate;
use App\Events\DeliveryZone\DeliveryZoneAfterDelete;
use App\Events\DeliveryZone\DeliveryZoneAfterUpdate;
use App\Events\DeliveryZone\DeliveryZoneBeforeCreate;
use App\Events\DeliveryZone\DeliveryZoneBeforeDelete;
use App\Events\DeliveryZone\DeliveryZoneBeforeUpdate;
use App\Http\Requests\DeliveryZone\StoreDeliveryZoneRequest;
use App\Http\Requests\DeliveryZone\UpdateDeliveryZoneRequest;
use App\Models\DeliveryZone;
use App\Models\Setting;
use App\Services\DeliveryZoneService;
use App\Traits\ChecksPermissions;
use App\Types\Api\ApiResponseType;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DeliveryZoneController extends Controller
{
    use ChecksPermissions, AuthorizesRequests;

    protected bool $editPermission = true;
    protected bool $deletePermission = true;
    protected bool $createPermission = true;

    public function __construct()
    {
        $this->editPermission = $this->hasPermission(AdminPermissionEnum::DELIVERY_ZONE_EDIT());
        $this->deletePermission = $this->hasPermission(AdminPermissionEnum::DELIVERY_ZONE_DELETE());
        $this->createPermission = $this->hasPermission(AdminPermissionEnum::DELIVERY_ZONE_CREATE());
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $columns = [
            ['data' => 'id', 'name' => 'id', 'title' => __('labels.id')],
            ['data' => 'name', 'name' => 'name', 'title' => __('labels.name')],
            ['data' => 'center_coordinates', 'name' => 'center_coordinates', 'title' => __('labels.center_coordinates')],
            ['data' => 'radius_km', 'name' => 'radius_km', 'title' => __('labels.radius_km')],
            ['data' => 'delivery_time_per_km', 'name' => 'delivery_time_per_km', 'title' => __('labels.delivery_time_per_km')],
            ['data' => 'buffer_time', 'name' => 'buffer_time', 'title' => __('labels.buffer_time')],
            ['data' => 'status', 'name' => 'status', 'title' => __('labels.status')],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('labels.created_at')],
            ['data' => 'action', 'name' => 'action', 'title' => __('labels.action'), 'orderable' => false, 'searchable' => false],
        ];

        $editPermission = $this->editPermission;
        $deletePermission = $this->deletePermission;
        $createPermission = $this->createPermission;

        return view('admin.delivery_zones.index', compact('columns', 'editPermission', 'deletePermission', 'createPermission'));
    }

    /**
     * Get delivery zones data for DataTable
     */
    public function getDeliveryZones(Request $request): JsonResponse
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $searchValue = $request->get('search')['value'] ?? '';
        $status = $request->get('status');

        $orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
        $orderDirection = $request->get('order')[0]['dir'] ?? 'asc';

        $columns = ['id', 'name', 'center_latitude', 'center_longitude', 'radius_km', 'status', 'created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $query = DeliveryZone::query();

        $totalRecords = DeliveryZone::count();
        $filteredRecords = $totalRecords; // Default to total records if no filtering is applied
        // Search filter
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('slug', 'like', "%{$searchValue}%");
            });
            $filteredRecords = $query->count();
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
            $filteredRecords = $query->count();
        }


        $data = $query
            ->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function ($deliveryZone) {
                return [
                    'id' => $deliveryZone->id,
                    'name' => $deliveryZone->name,
                    'center_coordinates' => $deliveryZone->center_latitude . ', ' . $deliveryZone->center_longitude,
                    'radius_km' => $deliveryZone->radius_km . ' km',
                    'delivery_time_per_km' => $deliveryZone->delivery_time_per_km . " " . __('labels.minutes'),
                    'buffer_time' => $deliveryZone->buffer_time . " " . __('labels.minutes'),
                    'status' => view('partials.status', ['status' => $deliveryZone->status ?? ""])->render(),
                    'created_at' => $deliveryZone->created_at->format('Y-m-d'),
                    'action' => view('partials.actions', [
                        'modelName' => 'delivery-zone',
                        'id' => $deliveryZone->id,
                        'title' => $deliveryZone->name,
                        'mode' => 'page_view',
                        'route' => route('admin.delivery-zones.view', $deliveryZone->id),
                        'editRoute' => route('admin.delivery-zones.edit', $deliveryZone->id),
                        'editPermission' => $this->editPermission,
                        'deletePermission' => $this->deletePermission
                    ])->render(),
                ];
            })
            ->toArray();

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', DeliveryZone::class);
        $mapSettings = $this->mapSettings();
        $googleApiKey = $mapSettings['googleMapKey'] ?? null;
        return view('admin.delivery_zones.form', compact('googleApiKey', 'mapSettings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeliveryZoneRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', DeliveryZone::class);
            event(new DeliveryZoneBeforeCreate());
            DB::beginTransaction();

            // Get validated data
            $validatedData = $request->validated();
            $boundaryJson = $this->parseBoundaryJson($validatedData['boundary_json'] ?? null);
            if ($boundaryJson === false) {
                DB::rollBack();
                return ApiResponseType::sendJsonResponse(
                    success: false,
                    message: __('messages.invalid_boundary_json'),
                    data: ['boundary_json' => [__('labels.boundary_json_invalid')]]
                );
            }

            $validatedData['boundary_json'] = $boundaryJson;
            $is_overlap = $this->checkZoneOverLap($validatedData);
            if ($is_overlap['success'] === false) {
                DB::rollBack();
                return ApiResponseType::sendJsonResponse(
                    success: false,
                    message: $is_overlap['message'],
                    data: $is_overlap['data']
                );
            }

            // Create the delivery zone
            $deliveryZone = DeliveryZone::create([
                'name' => $validatedData['name'],
                'center_latitude' => $validatedData['center_latitude'],
                'center_longitude' => $validatedData['center_longitude'],
                'delivery_time_per_km' => $validatedData['delivery_time_per_km'] ?? 0,
                'buffer_time' => $validatedData['buffer_time'] ?? 0,
                'radius_km' => $validatedData['radius_km'],
                'boundary_json' => $boundaryJson,
                'status' => $validatedData['status'],
                'rush_delivery_enabled' => $validatedData['rush_delivery_enabled'] ?? false,
                'rush_delivery_time_per_km' => $validatedData['rush_delivery_time_per_km'] ?? null,
                'rush_delivery_charges' => $validatedData['rush_delivery_charges'] ?? null,
                'regular_delivery_charges' => $validatedData['regular_delivery_charges'] ?? 0,
                'free_delivery_amount' => $validatedData['free_delivery_amount'] ?? null,
                'distance_based_delivery_charges' => $validatedData['distance_based_delivery_charges'] ?? null,
                'per_store_drop_off_fee' => $validatedData['per_store_drop_off_fee'] ?? null,
                'handling_charges' => $validatedData['handling_charges'] ?? null,
                'delivery_boy_base_fee' => $validatedData['delivery_boy_base_fee'] ?? null,
                'delivery_boy_per_store_pickup_fee' => $validatedData['delivery_boy_per_store_pickup_fee'] ?? null,
                'delivery_boy_distance_based_fee' => $validatedData['delivery_boy_distance_based_fee'] ?? null,
                'delivery_boy_per_order_incentive' => $validatedData['delivery_boy_per_order_incentive'] ?? null,
            ]);

            DB::commit();
            event(new DeliveryZoneAfterCreate($deliveryZone));
            return ApiResponseType::sendJsonResponse(
                success: true,
                message: __('messages.delivery_zone_created_successfully'),
                data: $deliveryZone,
                status: 201
            );

        } catch (ValidationException $e) {
            DB::rollBack();
            return ApiResponseType::sendJsonResponse(
                success: false,
                message: __('labels.validation_failed'),
                data: $e->errors()
            );
        } catch (AuthorizationException) {
            DB::rollBack();
            return ApiResponseType::sendJsonResponse(
                success: false,
                message: __('messages.unauthorized_action'),
                data: [],
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error(__('messages.error_creating_delivery_zone'), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $validatedData ?? []
            ]);

            return ApiResponseType::sendJsonResponse(
                success: false,
                message: __('messages.delivery_zone_creation_error'),
                data: config('app.debug') ? $e->getMessage() : __('messages.internal_server_error')
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $deliveryZone = DeliveryZone::findOrFail($id);
            return ApiResponseType::sendJsonResponse(
                success: true,
                message: __('messages.delivery_zone_retrieved_successfully'),
                data: $deliveryZone
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponseType::sendJsonResponse(
                success: false,
                message: __('messages.delivery_zone_not_found'),
                data: []
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $deliveryZone = DeliveryZone::findOrFail($id);
        $this->authorize('update', $deliveryZone);
        $mapSettings = $this->mapSettings();
        $googleApiKey = $mapSettings['googleMapKey'] ?? null;
        return view('admin.delivery_zones.form', compact('deliveryZone', 'googleApiKey', 'mapSettings'));
    }

    public function view($id): View
    {
        $deliveryZone = DeliveryZone::findOrFail($id);
        $this->authorize('view', $deliveryZone);
        $mapSettings = $this->mapSettings();
        $googleApiKey = $mapSettings['googleMapKey'] ?? null;
        $deliveryZoneMapConfig = [
            'provider' => 'google',
            'center' => [
                'lat' => (float) $deliveryZone->center_latitude,
                'lng' => (float) $deliveryZone->center_longitude,
            ],
            'radiusKm' => (float) $deliveryZone->radius_km,
            'boundary' => $deliveryZone->boundary_json,
            'zoom' => (int) ($mapSettings['defaultZoom'] ?? 13),
            'readOnly' => true,
        ];

        return view('admin.delivery_zones.view', compact('deliveryZone', 'googleApiKey', 'mapSettings', 'deliveryZoneMapConfig'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeliveryZoneRequest $request, $id): JsonResponse
    {
        try {
            $deliveryZone = DeliveryZone::findOrFail($id);
            $this->authorize('update', $deliveryZone);
            event(new DeliveryZoneBeforeUpdate($deliveryZone));
            $validatedData = $request->validated();
            $boundaryJson = $this->parseBoundaryJson($validatedData['boundary_json'] ?? null);
            if ($boundaryJson === false) {
                return ApiResponseType::sendJsonResponse(
                    success: false,
                    message: __('messages.invalid_boundary_json'),
                    data: ['boundary_json' => [__('labels.boundary_json_invalid')]]
                );
            }

            $validatedData['boundary_json'] = $boundaryJson;
            $is_overlap = $this->checkZoneOverLap($validatedData, $deliveryZone->id);
            if ($is_overlap['success'] === false) {
                return ApiResponseType::sendJsonResponse(
                    success: false,
                    message: $is_overlap['message'],
                    data: $is_overlap['data']
                );
            }

            $deliveryZone->update([
                'name' => $validatedData['name'],
                'center_latitude' => $validatedData['center_latitude'],
                'center_longitude' => $validatedData['center_longitude'],
                'delivery_time_per_km' => $validatedData['delivery_time_per_km'] ?? 0,
                'buffer_time' => $validatedData['buffer_time'] ?? 0,
                'radius_km' => $validatedData['radius_km'],
                'boundary_json' => $boundaryJson,
                'status' => $validatedData['status'],
                'rush_delivery_enabled' => $validatedData['rush_delivery_enabled'] ?? false,
                'rush_delivery_time_per_km' => $validatedData['rush_delivery_time_per_km'] ?? null,
                'rush_delivery_charges' => $validatedData['rush_delivery_charges'] ?? null,
                'regular_delivery_charges' => $validatedData['regular_delivery_charges'] ?? 0,
                'free_delivery_amount' => $validatedData['free_delivery_amount'] ?? null,
                'distance_based_delivery_charges' => $validatedData['distance_based_delivery_charges'] ?? null,
                'per_store_drop_off_fee' => $validatedData['per_store_drop_off_fee'] ?? null,
                'handling_charges' => $validatedData['handling_charges'] ?? null,
                'delivery_boy_base_fee' => $validatedData['delivery_boy_base_fee'] ?? null,
                'delivery_boy_per_store_pickup_fee' => $validatedData['delivery_boy_per_store_pickup_fee'] ?? null,
                'delivery_boy_distance_based_fee' => $validatedData['delivery_boy_distance_based_fee'] ?? null,
                'delivery_boy_per_order_incentive' => $validatedData['delivery_boy_per_order_incentive'] ?? null,
            ]);
            event(new DeliveryZoneAfterUpdate($deliveryZone));
            return ApiResponseType::sendJsonResponse(
                success: true,
                message: __('messages.delivery_zone_updated_successfully'),
                data: $deliveryZone
            );

        } catch (ValidationException $e) {
            return ApiResponseType::sendJsonResponse(
                success: false,
                message: __('labels.validation_failed'),
                data: $e->errors()
            );
        } catch (ModelNotFoundException) {
            return ApiResponseType::sendJsonResponse(
                success: false,
                message: __('messages.delivery_zone_not_found'),
                data: []
            );
        } catch (\Exception $e) {
            Log::error(__('messages.error_updating_delivery_zone'), [
                'error' => $e->getMessage(),
                'delivery_zone_id' => $id
            ]);

            return ApiResponseType::sendJsonResponse(
                success: false,
                message: __('messages.delivery_zone_update_error'),
                data: config('app.debug') ? $e->getMessage() : __('messages.internal_server_error')
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $deliveryZone = DeliveryZone::findOrFail($id);
            $this->authorize('delete', $deliveryZone);

            // Check if there are delivery boys assigned to this zone
            $deliveryBoysCount = $deliveryZone->deliveryBoys()->count();

            if ($deliveryBoysCount > 0) {
                return ApiResponseType::sendJsonResponse(
                    success: false,
                    message: __('messages.cannot_delete_delivery_zone_has_delivery_boys', [
                        'count' => $deliveryBoysCount
                    ]),
                    data: ['delivery_boys_count' => $deliveryBoysCount],
                );
            }
            event(new DeliveryZoneBeforeDelete($deliveryZone));

            $deliveryZone->delete();
            event(new DeliveryZoneAfterDelete());

            return ApiResponseType::sendJsonResponse(
                success: true,
                message: __('messages.delivery_zone_deleted_successfully'),
                data: []
            );
        } catch (ModelNotFoundException) {
            return ApiResponseType::sendJsonResponse(
                success: false,
                message: __('messages.delivery_zone_not_found'),
                data: [],
                status: 404
            );
        } catch (\Exception $e) {
            Log::error(__('messages.error_deleting_delivery_zone'), [
                'error' => $e->getMessage(),
                'delivery_zone_id' => $id
            ]);

            return ApiResponseType::sendJsonResponse(
                success: false,
                message: __('messages.delivery_zone_deletion_error'),
                data: config('app.debug') ? $e->getMessage() : __('messages.internal_server_error')
            );
        }
    }

    /**
     * Search delivery zones for select2 dropdown
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');
        $exceptId = $request->input('exceptId');
        $findId = $request->input('find_id');

        if ($findId) {
            $deliveryZones = DeliveryZone::where('id', $findId)
                ->select('id', 'name')
                ->get();
        } else {
            $deliveryZones = DeliveryZone::where('name', 'LIKE', '%' . $query . '%')
                ->select('id', 'name')
                ->when($exceptId, function ($q) use ($exceptId) {
                    $q->where('id', '!=', $exceptId);
                })
                ->take(10)
                ->get();
        }

        $results = $deliveryZones->map(function ($deliveryZone) {
            return [
                'id' => $deliveryZone->id,
                'value' => $deliveryZone->id,
                'text' => $deliveryZone->name,
            ];
        });

        return response()->json($results);
    }

    public function checkExists(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'center_latitude' => 'required|numeric|between:-90,90',
            'center_longitude' => 'required|numeric|between:-180,180',
            'radius_km' => 'required|numeric|min:0.1',
            'boundary_json' => 'nullable|json',
            'except_id' => 'nullable|integer|exists:delivery_zones,id',
        ]);

        $boundaryJson = $this->parseBoundaryJson($validatedData['boundary_json'] ?? null);
        if ($boundaryJson === false) {
            return ApiResponseType::sendJsonResponse(
                success: false,
                message: __('messages.invalid_boundary_json'),
                data: ['boundary_json' => [__('labels.boundary_json_invalid')]]
            );
        }

        $validatedData['boundary_json'] = $boundaryJson;
        $overlap = $this->checkZoneOverLap($validatedData, $validatedData['except_id'] ?? null);

        return ApiResponseType::sendJsonResponse(
            success: true,
            message: $overlap['success'] ? __('messages.no_overlap_found') : __('messages.delivery_zone_overlap_error'),
            data: [
                'exists' => !$overlap['success'],
                'overlap' => !$overlap['success'],
                'details' => $overlap['data'],
            ]
        );
    }

    private function parseBoundaryJson($boundaryJson): array|null|false
    {
        if (empty($boundaryJson)) {
            return null;
        }

        if (is_array($boundaryJson)) {
            return $boundaryJson;
        }

        $decodedBoundary = json_decode($boundaryJson, true);

        return json_last_error() === JSON_ERROR_NONE ? $decodedBoundary : false;
    }

    private function checkZoneOverLap($validatedData, $exceptId = null): array
    {
        $tempZone = new DeliveryZone($validatedData);

        // Check for overlaps with existing zones
        $overlapCheck = DeliveryZoneService::checkZoneOverlap($tempZone, $exceptId);

        if ($overlapCheck['has_overlap']) {
            $overlappingZoneNames = array_map(function ($overlap) {
                return $overlap['zone']->name;
            }, $overlapCheck['overlapping_zones']);

            return [
                'success' => false,
                'message' => __('messages.delivery_zone_overlap_error'),
                'data' => [
                    'overlapping_zones' => $overlappingZoneNames,
                    'overlap_count' => $overlapCheck['overlap_count'],
                    'details' => $overlapCheck['overlapping_zones']
                ]
            ];
        }
        return [
            'success' => true,
            'message' => __('messages.no_overlap_found'),
            'data' => []
        ];
    }

    private function mapSettings(): array
    {
        $maps = Setting::find(SettingTypeEnum::MAPS())?->value ?? [];
        $auth = Setting::find(SettingTypeEnum::AUTHENTICATION())?->value ?? [];
        $web = Setting::find(SettingTypeEnum::WEB())?->value ?? [];
        $googleMapKey = null;
        foreach ([
            $maps['googleMapKey'] ?? null,
            $auth['googleApiKey'] ?? null,
            $web['googleMapKey'] ?? null,
            config('services.google.maps_api_key'),
        ] as $key) {
            if (filled($key)) {
                $googleMapKey = (string) $key;
                break;
            }
        }

        return [
            'mapProvider' => 'google',
            'googleMapKey' => $googleMapKey,
            'mapplsStaticKey' => $maps['mapplsStaticKey']
                ?? env('MAPPLS_STATIC_KEY')
                ?? env('NEXT_PUBLIC_MAPPLS_STATIC_KEY')
                ?? '',
            'defaultLatitude' => $maps['defaultLatitude'] ?? $web['defaultLatitude'] ?? '28.6139',
            'defaultLongitude' => $maps['defaultLongitude'] ?? $web['defaultLongitude'] ?? '77.2090',
            'defaultZoom' => $maps['defaultZoom'] ?? '13',
        ];
    }
}
