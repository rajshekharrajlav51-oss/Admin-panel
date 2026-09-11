@extends('layouts.admin.app')

@section('title', 'Mappls Diagnostics')

@section('header_data')
    @php
        $page_title = 'Mappls Diagnostics';
        $page_pretitle = 'Admin Maps';
    @endphp
@endsection

@section('admin-content')
    @php
        $maskedSdkUrl = 'https://sdk.mappls.com/map/sdk/web?v=3.0&access_token=' . (!empty($mapplsStaticKey) ? '***' . substr($mapplsStaticKey, -4) : '');
    @endphp
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Mappls Diagnostics</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">SDK Request</h3>
                        </div>
                        <div class="card-body">
                            <pre id="mappls-diagnostics" class="bg-light p-3 rounded small mb-0 text-dark" style="white-space: pre-wrap; min-height: 360px; color: #182433;">{{ json_encode([
                                'state' => 'blade-rendered',
                                'sdk_url_masked' => $maskedSdkUrl,
                                'sdk_host' => 'sdk.mappls.com',
                                'sdk_path' => '/map/sdk/web',
                                'sdk_version' => '3.0',
                                'access_token' => !empty($mapplsStaticKey) ? 'Configured' : 'Missing',
                                'key_length' => strlen((string) $mapplsStaticKey),
                                'origin' => request()->getSchemeAndHttpHost(),
                                'path' => request()->path(),
                                'csp_header' => request()->headers->has('content-security-policy') ? 'Present on request' : 'Not detectable from Blade response',
                            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Basic Test Map</h3>
                        </div>
                        <div class="card-body">
                            <div id="mappls-diagnostic-status" class="alert alert-info mb-3">Loading Mappls SDK...</div>
                            <div id="mappls-test-map" class="border" style="height: 500px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        window.AdminMapplsDiagnosticConfig = {
            mapplsStaticKey: @json($mapplsStaticKey),
            sdkUrlMasked: @json($maskedSdkUrl),
            defaultCenter: {
                lat: Number(@json($defaultLatitude)) || 28.6139,
                lng: Number(@json($defaultLongitude)) || 77.2090
            },
            defaultZoom: Number(@json($defaultZoom)) || 10
        };
    </script>
    <script src="{{ hyperAsset('assets/js/admin-map-provider.js') }}"></script>
    <script src="{{ hyperAsset('assets/js/mappls-diagnostics.js') }}"></script>
@endpush
