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

    $provider = $settings['mapProvider'] ?? env('MAP_PROVIDER', 'mappls');
    $mapplsStaticKey = $settings['mapplsStaticKey'] ?? env('MAPPLS_STATIC_KEY', env('NEXT_PUBLIC_MAPPLS_STATIC_KEY', ''));
    $googleMapKey = $settings['googleMapKey'] ?? env('GOOGLE_MAP_KEY', '');
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
                                        <option value="mappls" {{ $provider === 'mappls' ? 'selected' : '' }}>Mappls</option>
                                        <option value="google" {{ $provider === 'google' ? 'selected' : '' }}>Google Maps</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="mappls-static-key" class="form-label">Mappls Static Key</label>
                                    <input type="password" class="form-control" name="mapplsStaticKey"
                                           id="mappls-static-key" value="{{ $mapplsStaticKey }}"
                                           autocomplete="off" maxlength="255">
                                    <div class="form-hint">
                                        Status:
                                        @if(!empty($mapplsStaticKey))
                                            <span class="badge bg-green-lt">Configured</span>
                                        @else
                                            <span class="badge bg-red-lt">Missing</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
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
                            <div class="mb-3">
                                <a href="{{ route('admin.mappls.diagnostics') }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                                    Open Mappls Diagnostics
                                </a>
                            </div>
                            <div id="map-settings-search-wrap" class="mb-3">
                                <input type="search" class="form-control" id="map-settings-search"
                                       placeholder="Search location">
                                <div class="list-group mt-2 d-none" id="map-settings-search-results"></div>
                            </div>
                            <div id="map-settings-preview" class="border" style="height: 420px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        window.AdminMapConfig = {
            provider: @json($provider),
            googleMapKey: @json($googleMapKey),
            mapplsStaticKey: @json($mapplsStaticKey),
            defaultCenter: {
                lat: Number(@json($defaultLatitude)) || 28.6139,
                lng: Number(@json($defaultLongitude)) || 77.2090
            },
            defaultZoom: Number(@json($defaultZoom)) || 13,
            mapplsRoutes: {
                autosuggest: @json(route('admin.mappls.autosuggest')),
                geocode: @json(route('admin.mappls.geocode')),
                placeDetails: @json(route('admin.mappls.place-details')),
                reverseGeocode: @json(route('admin.mappls.reverse-geocode'))
            }
        };
    </script>
    <script src="{{ hyperAsset('assets/js/admin-map-provider.js') }}"></script>
    <script src="{{ hyperAsset('assets/js/maps-settings.js') }}"></script>
@endpush
