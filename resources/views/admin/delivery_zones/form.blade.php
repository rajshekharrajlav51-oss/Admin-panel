@extends('layouts.admin.app')

@php
	$zone = $deliveryZone ?? null;
	$isEdit = !empty($zone);
	$center = [
		'lat' => (float) ($zone->center_latitude ?? $mapSettings['defaultLatitude'] ?? 25.5941),
		'lng' => (float) ($zone->center_longitude ?? $mapSettings['defaultLongitude'] ?? 85.1376),
	];
	$radius = (float) ($zone->radius_km ?? 10);
	$status = $zone->status ?? 'active';
	$boundaryJson = !empty($zone?->boundary_json) ? json_encode($zone->boundary_json) : '';
	$deliveryZoneMapConfig = [
		'provider' => 'google',
		'center' => $center,
		'radiusKm' => $radius,
		'boundary' => $zone->boundary_json ?? null,
		'zoom' => (int) ($mapSettings['defaultZoom'] ?? 13),
	];
@endphp

@section('title', $isEdit ? 'Edit Delivery Zone' : 'Add Delivery Zone')

@section('header_data')
	@php
		$page_title = $isEdit ? 'Edit Delivery Zone' : 'Add Delivery Zone';
		$page_pretitle = __('labels.delivery_zones');
	@endphp
@endsection

@section('admin-content')
	<div class="page-header d-print-none">
		<div class="container-xl">
			<div class="row align-items-center">
				<div class="col"><h2 class="page-title">{{ $page_title }}</h2></div>
				<div class="col-auto"><a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
			</div>
		</div>
	</div>

	<div class="page-body">
		<div class="container-xl">
			<form class="form-submit" method="POST"
				  action="{{ $isEdit ? route('admin.delivery-zones.update', $deliveryZone->id) : route('admin.delivery-zones.store') }}">
				@csrf
				<div class="row row-cards">
					<div class="col-lg-7">
						<div class="card">
							<div class="card-header"><h3 class="card-title">Select Zone Location</h3></div>
							<div class="card-body">
								<div class="input-group mb-2">
									<input type="search" id="zone-location-search" class="form-control" placeholder="Search location or address" autocomplete="off">
									<button type="button" id="use-current-location" class="btn btn-outline-primary"><i class="ti ti-current-location me-1"></i>Use Current Location</button>
								</div>
								@if(empty($googleApiKey))
									<div class="alert alert-warning">
										Google Maps API key is not configured. Delivery Zone map will load after a valid browser API key is added.
									</div>
								@endif
								<div id="zone-location-results" class="list-group d-none mb-3"></div>
								<div id="delivery-zone-map" class="delivery-zone-map"></div>
								<div class="row mt-3">
									<div class="col-md-6"><label for="center-latitude" class="form-label required">Center Latitude</label><input id="center-latitude" name="center_latitude" class="form-control" type="number" step="any" value="{{ $center['lat'] }}" required></div>
									<div class="col-md-6"><label for="center-longitude" class="form-label required">Center Longitude</label><input id="center-longitude" name="center_longitude" class="form-control" type="number" step="any" value="{{ $center['lng'] }}" required></div>
								</div>
								<input type="hidden" id="boundary-json" name="boundary_json" value="{{ e($boundaryJson) }}">
							</div>
						</div>
					</div>
					<div class="col-lg-5">
						<div class="card">
							<div class="card-header"><h3 class="card-title">Zone Details</h3></div>
							<div class="card-body">
								<div class="mb-3"><label for="zone-name" class="form-label required">Zone Name</label><input id="zone-name" name="name" class="form-control" value="{{ $zone->name ?? '' }}" required maxlength="255"></div>
								<div class="mb-3"><label for="radius-km" class="form-label required">Radius (KM)</label><input id="radius-km" name="radius_km" class="form-control" type="number" min="0.1" step="0.1" value="{{ $radius }}" required></div>
								<div class="mb-3"><label for="delivery-time-per-km" class="form-label required">Delivery Time per KM (minutes)</label><input id="delivery-time-per-km" name="delivery_time_per_km" class="form-control" type="number" min="0" step="1" value="{{ $zone->delivery_time_per_km ?? 0 }}" required></div>
								<div class="mb-3"><label for="buffer-time" class="form-label required">Buffer Time (minutes)</label><input id="buffer-time" name="buffer_time" class="form-control" type="number" min="0" step="1" value="{{ $zone->buffer_time ?? 0 }}" required></div>
								<div class="mb-3"><label for="zone-status" class="form-label required">Status</label><select id="zone-status" name="status" class="form-select" required><option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option><option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option></select></div>
								<input type="hidden" name="regular_delivery_charges" value="{{ $zone->regular_delivery_charges ?? 0 }}">
								<input type="hidden" name="rush_delivery_enabled" value="{{ !empty($zone?->rush_delivery_enabled) ? 1 : 0 }}">
								<input type="hidden" name="rush_delivery_time_per_km" value="{{ $zone->rush_delivery_time_per_km ?? 0 }}">
								<input type="hidden" name="rush_delivery_charges" value="{{ $zone->rush_delivery_charges ?? 0 }}">
								<input type="hidden" name="free_delivery_amount" value="{{ $zone->free_delivery_amount ?? 0 }}">
								<input type="hidden" name="distance_based_delivery_charges" value="{{ $zone->distance_based_delivery_charges ?? 0 }}">
								<input type="hidden" name="per_store_drop_off_fee" value="{{ $zone->per_store_drop_off_fee ?? 0 }}">
								<input type="hidden" name="handling_charges" value="{{ $zone->handling_charges ?? 0 }}">
								<input type="hidden" name="delivery_boy_base_fee" value="{{ $zone->delivery_boy_base_fee ?? 0 }}">
								<input type="hidden" name="delivery_boy_per_store_pickup_fee" value="{{ $zone->delivery_boy_per_store_pickup_fee ?? 0 }}">
								<input type="hidden" name="delivery_boy_distance_based_fee" value="{{ $zone->delivery_boy_distance_based_fee ?? 0 }}">
								<input type="hidden" name="delivery_boy_per_order_incentive" value="{{ $zone->delivery_boy_per_order_incentive ?? 0 }}">
							</div>
							<div class="card-footer d-flex justify-content-end gap-2"><a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-outline-secondary">Cancel</a><button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Save Delivery Zone</button></div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection

@push('styles')
	<style>.delivery-zone-map{height:520px;min-height:360px;border:1px solid var(--tblr-border-color);border-radius:4px;z-index:1}</style>
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
