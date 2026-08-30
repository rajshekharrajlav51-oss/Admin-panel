(function (window, document) {
    document.addEventListener('DOMContentLoaded', function () {
        const config = window.AdminMapplsDiagnosticConfig || {};
        const output = document.getElementById('mappls-diagnostics');
        const status = document.getElementById('mappls-diagnostic-status');
        const key = String(config.mapplsStaticKey || '');
        const sdkUrl = `https://sdk.mappls.com/map/sdk/web?v=3.0&access_token=${encodeURIComponent(key)}`;
        const startedAt = Date.now();
        let cspViolation = null;

        function sdkFacts() {
            const url = new URL(sdkUrl);

            return {
                state: 'javascript-running',
                sdk_url_masked: config.sdkUrlMasked || `${url.origin}${url.pathname}?v=${url.searchParams.get('v')}&access_token=${key ? `***${key.slice(-4)}` : ''}`,
                sdk_host: url.host,
                sdk_origin: url.origin,
                sdk_path: url.pathname,
                sdk_version: url.searchParams.get('v'),
                query_parameters: Array.from(url.searchParams.keys()),
                access_token: url.searchParams.has('access_token') && url.searchParams.get('access_token') !== '' ? 'Configured' : 'Missing',
                access_token_length: String(url.searchParams.get('access_token') || '').length,
                static_key_configured: Boolean(key),
                static_key_length: key.length,
                browser_origin: window.location.origin,
                browser_path: window.location.pathname,
                browser_online: navigator.onLine,
                csp_violation: cspViolation || 'None captured',
                http_status: 'Pending',
                browser_load_status: 'Pending',
                error: null
            };
        }

        function resourceTiming() {
            const entries = performance.getEntriesByType('resource')
                .filter((entry) => entry.name && entry.name.indexOf('https://sdk.mappls.com/map/sdk/web') === 0);
            const entry = entries[entries.length - 1];

            if (!entry) {
                return {
                    resource_timing: 'No resource timing entry captured'
                };
            }

            return {
                resource_timing: {
                    initiator_type: entry.initiatorType || null,
                    response_status: Number.isFinite(entry.responseStatus) && entry.responseStatus > 0 ? entry.responseStatus : 'Unavailable',
                    transfer_size: entry.transferSize ?? 'Unavailable',
                    encoded_body_size: entry.encodedBodySize ?? 'Unavailable',
                    decoded_body_size: entry.decodedBodySize ?? 'Unavailable',
                    duration_ms: Math.round(entry.duration || 0)
                }
            };
        }

        function write(details) {
            if (!output) return;
            output.textContent = JSON.stringify({
                ...sdkFacts(),
                ...resourceTiming(),
                ...details
            }, null, 2);
        }

        function setStatus(message, type) {
            if (!status) return;
            status.className = `alert alert-${type || 'info'} mb-3`;
            status.textContent = message;
        }

        window.addEventListener('securitypolicyviolation', function (event) {
            cspViolation = {
                blocked_uri: event.blockedURI,
                violated_directive: event.violatedDirective,
                effective_directive: event.effectiveDirective,
                original_policy: event.originalPolicy ? 'Present' : 'Unavailable'
            };
            write({
                http_status: 'Unavailable',
                browser_load_status: 'Blocked by CSP',
                error: `CSP blocked ${event.blockedURI || 'the SDK request'}`
            });
        });

        function finishAsFailed(message) {
            setStatus(message, 'danger');
            write({
                http_status: 'Unavailable - browser did not expose script HTTP status',
                browser_load_status: 'Failed',
                elapsed_ms: Date.now() - startedAt,
                error: message,
                note: 'If DevTools Network shows 403, fix Mappls key referrer/domain. If it shows net::ERR_FAILED or blocked, fix network/firewall/extension. If it shows CSP, allow sdk.mappls.com.'
            });
        }

        write({state: 'starting-script-request'});

        if (!key) {
            finishAsFailed('Mappls Static Key is missing before browser script request.');
            return;
        }

        const existing = document.getElementById('mappls-diagnostic-sdk');
        if (existing) existing.remove();

        const script = document.createElement('script');
        script.id = 'mappls-diagnostic-sdk';
        script.type = 'text/javascript';
        script.async = true;
        script.defer = true;
        script.src = sdkUrl;

        write({
            state: 'script-element-created',
            actual_script_src_masked: config.sdkUrlMasked,
            browser_load_status: 'Script appended'
        });

        script.onload = function () {
            const hasMap = typeof window.mappls?.Map === 'function';
            write({
                state: hasMap ? 'sdk-loaded' : 'sdk-loaded-api-missing',
                http_status: 'Loaded by browser script element',
                browser_load_status: hasMap ? 'Loaded' : 'Loaded but mappls.Map missing',
                elapsed_ms: Date.now() - startedAt,
                typeof_mappls: typeof window.mappls,
                typeof_map: typeof window.mappls?.Map,
                typeof_marker: typeof window.mappls?.Marker,
                typeof_polygon: typeof window.mappls?.Polygon
            });

            if (!hasMap) {
                setStatus('Mappls SDK loaded, but mappls.Map is missing.', 'danger');
                return;
            }

            try {
                const map = new window.mappls.Map('mappls-test-map', {
                    center: [config.defaultCenter.lat, config.defaultCenter.lng],
                    zoom: config.defaultZoom || 13,
                    zoomControl: true,
                    location: true
                });

                window.setTimeout(function () {
                    if (typeof map.resize === 'function') map.resize();
                }, 0);

                setStatus('Basic Mappls SDK map initialized.', 'success');
                write({
                    state: 'basic-map-initialized',
                    http_status: 'Loaded by browser script element',
                    browser_load_status: 'Loaded',
                    elapsed_ms: Date.now() - startedAt,
                    typeof_mappls: typeof window.mappls,
                    typeof_map: typeof window.mappls?.Map
                });
            } catch (error) {
                setStatus(error.message || 'Mappls map initialization failed.', 'danger');
                write({
                    state: 'map-initialization-failed',
                    http_status: 'Loaded by browser script element',
                    browser_load_status: 'Loaded',
                    elapsed_ms: Date.now() - startedAt,
                    error: error.message || String(error)
                });
            }
        };

        script.onerror = function (event) {
            finishAsFailed(`Mappls SDK script error event fired (${event?.type || 'error'}).`);
        };

        window.setTimeout(function () {
            if (typeof window.mappls?.Map !== 'function') {
                finishAsFailed('Mappls SDK script did not load within 15 seconds.');
            }
        }, 15000);

        document.head.appendChild(script);
    });
})(window, document);
