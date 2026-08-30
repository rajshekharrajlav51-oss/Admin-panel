(function () {
    document.addEventListener('DOMContentLoaded', async function () {
        const config = window.AdminMapplsDiagnosticConfig || {};
        const output = document.getElementById('mappls-diagnostics');
        const status = document.getElementById('mappls-diagnostic-status');
        const sdkUrl = `https://sdk.mappls.com/map/sdk/web?v=3.0&access_token=${encodeURIComponent(config.mapplsStaticKey || '')}`;

        function safeUrlFacts() {
            const url = new URL(sdkUrl);
            return {
                mapplsKeyConfigured: Boolean(config.mapplsStaticKey),
                mapplsKeyLength: String(config.mapplsStaticKey || '').length,
                sdkOrigin: url.origin,
                sdkPath: url.pathname,
                sdkVersion: url.searchParams.get('v'),
                accessTokenExists: url.searchParams.has('access_token') && url.searchParams.get('access_token') !== '',
                accessTokenLength: String(url.searchParams.get('access_token') || '').length,
                pageOrigin: window.location.origin,
                pagePath: window.location.pathname
            };
        }

        function write(details) {
            if (output) {
                output.textContent = JSON.stringify({
                    ...safeUrlFacts(),
                    ...details
                }, null, 2);
            }
        }

        function setStatus(message, type) {
            if (!status) return;
            status.className = `alert alert-${type || 'info'} mb-3`;
            status.textContent = message;
        }

        write({state: 'starting'});

        try {
            const sdk = await AdminMapProvider.loadMapplsSdk(config.mapplsStaticKey);
            write({
                state: 'sdk-loaded',
                typeofMappls: typeof window.mappls,
                typeofMap: typeof sdk?.Map,
                typeofMarker: typeof sdk?.Marker,
                typeofPolygon: typeof sdk?.Polygon
            });

            const map = new sdk.Map('mappls-test-map', {
                center: [config.defaultCenter.lat, config.defaultCenter.lng],
                zoom: config.defaultZoom || 10,
                zoomControl: true,
                location: true
            });

            window.setTimeout(() => {
                if (typeof map.resize === 'function') map.resize();
            }, 0);

            setStatus('Basic Mappls SDK map initialized.', 'success');
            write({
                state: 'basic-map-initialized',
                typeofMappls: typeof window.mappls,
                typeofMap: typeof sdk?.Map,
                typeofMarker: typeof sdk?.Marker,
                typeofPolygon: typeof sdk?.Polygon
            });
        } catch (error) {
            setStatus(error.message || 'Mappls diagnostic failed.', 'danger');
            write({
                state: 'failed',
                error: error.message || String(error),
                typeofMappls: typeof window.mappls,
                typeofMap: typeof window.mappls?.Map,
                note: 'Open DevTools > Network > JS and inspect the sdk.mappls.com request for the exact browser status.'
            });
        }
    });
})();
