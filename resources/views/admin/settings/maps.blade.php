@extends('layouts.admin.app', ['page' => $menuAdmin['settings']['active'] ?? "", 'sub_page' => $menuAdmin['settings']['route']['maps']['sub_active'] ?? "" ])

@section('title', 'Maps Settings')

@section('header_data')
    @php
        $page_title = 'Maps Settings';
        $page_pretitle = __('labels.admin') . " " . __('labels.settings');
    @endphp
@endsection

@php
    $breadcrumbs = [
        ['title' => __('labels.home'), 'url' => route('admin.dashboard')],
        ['title' => __('labels.settings'), 'url' => route('admin.settings.index')],
        ['title' => 'Maps Settings', 'url' => null],
    ];

    $provider = 'google';
    $googleMapKey = '';
    foreach ([
        $settings['googleMapKey'] ?? null,
        config('services.google.maps_api_key'),
    ] as $key) {
        if (filled($key)) {
            $googleMapKey = (string) $key;
            break;
        }
    }
    $defaultLatitude = $settings['defaultLatitude'] ?? '28.6139';
    $defaultLongitude = $settings['defaultLongitude'] ?? '77.2090';
    $defaultZoom = $settings['defaultZoom'] ?? '13';
@endphp

@section('admin-content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Maps Settings</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-lg-5">
                    <div class="card">
                        <form action="{{ route('admin.settings.store') }}" class="form-submit" method="post">
                            @csrf
                            <input type="hidden" name="type" value="{{ \App\Enums\SettingTypeEnum::MAPS() }}">
                            <div class="card-header">
                                <h3 class="card-title">Provider Configuration</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="map-provider" class="form-label required">Map Provider</label>
                                    <select name="mapProvider" id="map-provider" class="form-select" required>
                                        <option value="google" selected>Google Maps</option>
                                    </select>
                                </div>

                                <div class="mb-3" data-provider-field="google">
                                    <label for="google-map-key" class="form-label">Google Maps API Key</label>
                                    <input type="password" class="form-control" name="googleMapKey"
                                           id="google-map-key" value="{{ $googleMapKey }}"
                                           autocomplete="off" maxlength="255">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="default-latitude" class="form-label">Default Latitude</label>
                                            <input type="number" step="any" class="form-control" name="defaultLatitude"
                                                   id="default-latitude" value="{{ $defaultLatitude }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="default-longitude" class="form-label">Default Longitude</label>
                                            <input type="number" step="any" class="form-control" name="defaultLongitude"
                                                   id="default-longitude" value="{{ $defaultLongitude }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="default-zoom" class="form-label">Default Zoom</label>
                                    <input type="number" class="form-control" name="defaultZoom"
                                           id="default-zoom" value="{{ $defaultZoom }}" min="1" max="22">
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">{{ __('labels.save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Test Map</h3>
                        </div>
                        <div class="card-body">
                            <div id="map-settings-status" class="alert alert-info mb-3">Loading map preview...</div>
                            <div id="map-settings-search-wrap" class="mb-3">
                                <input type="search" class="form-control" id="map-settings-search"
                                       placeholder="Search location">
                                <div class="list-group mt-2 d-none" id="map-settings-search-results"></div>
                            </div>
                            <div id="map-settings-preview" class="border map-settings-preview"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>.map-settings-preview{height:420px}</style>
@endpush

@push('script')
    <script>
        window.AdminMapConfig = {
            provider: @json($provider),
            googleMapKey: @json($googleMapKey),
            defaultCenter: {
                lat: Number(@json($defaultLatitude)) || 28.6139,
                lng: Number(@json($defaultLongitude)) || 77.2090
            },
            defaultZoom: Number(@json($defaultZoom)) || 13
        };
    </script>
    <script src="{{ hyperAsset('assets/js/admin-map-provider.js') }}"></script>
    <script src="{{ hyperAsset('assets/js/maps-settings.js') }}"></script>
@endpush
