@extends('layouts.admin.app')

@section('title', 'View Delivery Zone')

@section('header_data')
    @php($page_title = 'View Delivery Zone')
@endsection

@php
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
@endphp

@section('admin-content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">{{ $deliveryZone->name }}</h2>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.delivery-zones.edit', $deliveryZone->id) }}" class="btn btn-primary">
                        <i class="ti ti-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-outline-secondary ms-2">Back</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Zone Map</h3>
                        </div>
                        <div class="card-body">
                            @if(empty($googleApiKey))
                                <div class="alert alert-warning">
                                    Google Maps API key is not configured. Delivery Zone map will load after a valid browser API key is added.
                                </div>
                            @endif
                            <div id="delivery-zone-map" class="delivery-zone-map"></div>
                            <input type="hidden" id="center-latitude" value="{{ $deliveryZone->center_latitude }}">
                            <input type="hidden" id="center-longitude" value="{{ $deliveryZone->center_longitude }}">
                            <input type="hidden" id="radius-km" value="{{ $deliveryZone->radius_km }}">
                            <input type="hidden" id="boundary-json" value="{{ e(!empty($deliveryZone->boundary_json) ? json_encode($deliveryZone->boundary_json) : '') }}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Zone Details</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-6">Center</dt>
                                <dd class="col-6">{{ $deliveryZone->center_latitude }}, {{ $deliveryZone->center_longitude }}</dd>
                                <dt class="col-6">Radius</dt>
                                <dd class="col-6">{{ $deliveryZone->radius_km }} KM</dd>
                                <dt class="col-6">Delivery Time</dt>
                                <dd class="col-6">{{ $deliveryZone->delivery_time_per_km }} min/KM</dd>
                                <dt class="col-6">Buffer Time</dt>
                                <dd class="col-6">{{ $deliveryZone->buffer_time }} minutes</dd>
                                <dt class="col-6">Regular Delivery Charges</dt>
                                <dd class="col-6">{{ $deliveryZone->regular_delivery_charges ?? 0 }}</dd>
                                <dt class="col-6">Rush Delivery</dt>
                                <dd class="col-6">{{ $deliveryZone->rush_delivery_enabled ? 'Enabled' : 'Disabled' }}</dd>
                                <dt class="col-6">Rush Time/KM</dt>
                                <dd class="col-6">{{ $deliveryZone->rush_delivery_time_per_km ?? '-' }}</dd>
                                <dt class="col-6">Rush Charges</dt>
                                <dd class="col-6">{{ $deliveryZone->rush_delivery_charges ?? '-' }}</dd>
                                <dt class="col-6">Free Delivery Amount</dt>
                                <dd class="col-6">{{ $deliveryZone->free_delivery_amount ?? '-' }}</dd>
                                <dt class="col-6">Distance Charges</dt>
                                <dd class="col-6">{{ $deliveryZone->distance_based_delivery_charges ?? '-' }}</dd>
                                <dt class="col-6">Per Store Drop-off Fee</dt>
                                <dd class="col-6">{{ $deliveryZone->per_store_drop_off_fee ?? '-' }}</dd>
                                <dt class="col-6">Handling Charges</dt>
                                <dd class="col-6">{{ $deliveryZone->handling_charges ?? '-' }}</dd>
                                <dt class="col-6">Delivery Boy Base Fee</dt>
                                <dd class="col-6">{{ $deliveryZone->delivery_boy_base_fee ?? '-' }}</dd>
                                <dt class="col-6">Delivery Boy Pickup Fee</dt>
                                <dd class="col-6">{{ $deliveryZone->delivery_boy_per_store_pickup_fee ?? '-' }}</dd>
                                <dt class="col-6">Delivery Boy Distance Fee</dt>
                                <dd class="col-6">{{ $deliveryZone->delivery_boy_distance_based_fee ?? '-' }}</dd>
                                <dt class="col-6">Delivery Boy Incentive</dt>
                                <dd class="col-6">{{ $deliveryZone->delivery_boy_per_order_incentive ?? '-' }}</dd>
                                <dt class="col-6">Status</dt>
                                <dd class="col-6">
                                    <span class="badge {{ $deliveryZone->status === 'active' ? 'bg-green-lt' : 'bg-secondary-lt' }}">
                                        {{ ucfirst($deliveryZone->status) }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>.delivery-zone-map{height:560px;min-height:360px;border:1px solid var(--tblr-border-color);border-radius:4px;z-index:1}</style>
@endpush

@push('script')
    <script>
        window.DeliveryZoneMapConfig = @json($deliveryZoneMapConfig);
    </script>
    <script src="{{ hyperAsset('assets/js/map-provider.js') }}"></script>
    <script src="{{ hyperAsset('assets/js/delivery-zone-map.js') }}"></script>
    @if(!empty($googleApiKey))
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ urlencode($googleApiKey) }}&libraries=maps,places,marker&callback=initDeliveryZoneMap&loading=async"
            async defer>
        </script>
    @else
        <script>
            window.DeliveryZoneGoogleMapsConfigurationMissing = true;
        </script>
    @endif
@endpush
