(function (window, document) {
    let mapplsSdkPromise = null;
    let loadedMapplsKey = '';
    let googleSdkPromise = null;

    function toLatLng(payload) {
        const source = payload && (payload.latLng || payload.lngLat || payload.data || payload);
        const lat = typeof source?.lat === 'function' ? source.lat() : source?.lat;
        const lng = typeof source?.lng === 'function' ? source.lng() : (source?.lng ?? source?.lon);

        if (Number.isFinite(Number(lat)) && Number.isFinite(Number(lng))) {
            return {lat: Number(lat), lng: Number(lng)};
        }

        return null;
    }

    function maskSdkUrl(url) {
        try {
            const parsed = new URL(url);
            if (parsed.searchParams.has('access_token')) {
                const token = parsed.searchParams.get('access_token') || '';
                parsed.searchParams.set('access_token', token ? `***${token.slice(-4)}` : '');
            }
            return parsed.toString();
        } catch (error) {
            return 'https://sdk.mappls.com/map/sdk/web?access_token=***';
        }
    }

    function logMapplsDiagnostics(level, message, details) {
        const logger = console[level] || console.info;
        logger.call(console, '[AdminMapProvider:Mappls]', message, details || {});
    }

    function describeMapplsGlobal() {
        return {
            typeofMappls: typeof window.mappls,
            typeofMap: typeof window.mappls?.Map,
            typeofMarker: typeof window.mappls?.Marker,
            typeofPolygon: typeof window.mappls?.Polygon
        };
    }

    function normalizeMapplsCenter(position) {
        return [Number(position.lat), Number(position.lng)];
    }

    function loadMapplsSdk(apiKey) {
        if (!apiKey) {
            return Promise.reject(new Error('Mappls Static Key is not configured.'));
        }

        if (window.mappls && loadedMapplsKey === apiKey) {
            return Promise.resolve(window.mappls);
        }

        if (!mapplsSdkPromise || loadedMapplsKey !== apiKey) {
            loadedMapplsKey = apiKey;
            mapplsSdkPromise = new Promise((resolve, reject) => {
                const scriptId = 'mappls-web-sdk';
                const sdkSrcCandidates = [
                    `https://apis.mappls.com/advancedmaps/api/js?v=3.0&libraries=places,drawing&key=${encodeURIComponent(apiKey)}`,
                    `https://apis.mappls.com/advancedmaps/api/js?v=3.0&libraries=places,drawing&access_token=${encodeURIComponent(apiKey)}`,
                    `https://apis.mappls.com/advancedmaps/api/js?v=3.0&libraries=places,drawing&key=${encodeURIComponent(apiKey)}&callback=window.AdminMapplsReadyCallback`,
                    `https://sdk.mappls.com/map/sdk/web?v=3.0&access_token=${encodeURIComponent(apiKey)}`,
                    `https://sdk.mappls.com/map/sdk/web?v=3.0&key=${encodeURIComponent(apiKey)}`
                ];
                let attemptIndex = 0;
                let script = document.getElementById(scriptId);
                let loadTimeout = null;

                const diagnosticDetails = {
                    sdkUrls: sdkSrcCandidates.map((value) => maskSdkUrl(value)),
                    keyPresent: Boolean(apiKey),
                    keyLength: String(apiKey).length,
                    origin: window.location.origin,
                    existingScriptFound: Boolean(script),
                    windowMapplsPresent: Boolean(window.mappls)
                };

                const cleanupExistingScripts = () => {
                    Array.from(document.querySelectorAll('script[src*="mappls.com"], script[src*="apis.mappls.com"], script[src*="sdk.mappls.com"]')).forEach((item) => {
                        if (item.id !== scriptId) item.remove();
                    });
                };

                const tryNextScript = () => {
                    if (attemptIndex >= sdkSrcCandidates.length) {
                        logMapplsDiagnostics('error', 'Mappls SDK failed to load from all configured endpoints.', {
                            ...diagnosticDetails,
                            api: describeMapplsGlobal()
                        });
                        mapplsSdkPromise = null;
                        reject(new Error('Mappls SDK did not load from the available endpoints. Check the key, domain whitelist, and browser/network restrictions.'));
                        return;
                    }

                    const url = sdkSrcCandidates[attemptIndex];
                    cleanupExistingScripts();

                    script = script && script.parentElement ? script : document.createElement('script');
                    script.id = scriptId;
                    script.async = true;
                    script.type = 'text/javascript';
                    script.src = url;
                    script.dataset.apiKey = apiKey;
                    script.dataset.sdkUrl = url;

                    const onError = (event) => {
                        window.clearTimeout(loadTimeout);
                        attemptIndex += 1;
                        logMapplsDiagnostics('warn', 'Mappls SDK candidate failed. Trying next endpoint.', {
                            ...diagnosticDetails,
                            attemptedUrl: maskSdkUrl(url),
                            eventType: event?.type || 'error',
                            api: describeMapplsGlobal()
                        });
                        if (script && script.parentElement) script.remove();
                        tryNextScript();
                    };

                    script.onerror = onError;
                    script.onload = () => {
                        window.clearTimeout(loadTimeout);
                        if (window.mappls && typeof window.mappls.Map === 'function') {
                            logMapplsDiagnostics('info', 'SDK loaded.', {
                                ...diagnosticDetails,
                                loadedUrl: maskSdkUrl(url),
                                windowMapplsPresent: true,
                                api: describeMapplsGlobal()
                            });
                            resolve(window.mappls);
                            return;
                        }

                        if (!window.mappls || typeof window.mappls.Map !== 'function') {
                            attemptIndex += 1;
                            logMapplsDiagnostics('warn', 'Candidate loaded but Map API is missing. Trying next endpoint.', {
                                ...diagnosticDetails,
                                attemptedUrl: maskSdkUrl(url),
                                api: describeMapplsGlobal()
                            });
                            if (script && script.parentElement) script.remove();
                            tryNextScript();
                            return;
                        }
                    };

                    if (!script.parentElement) {
                        document.head.appendChild(script);
                    }

                    loadTimeout = window.setTimeout(() => {
                        logMapplsDiagnostics('warn', 'Mappls SDK candidate timed out. Trying next endpoint.', {
                            ...diagnosticDetails,
                            attemptedUrl: maskSdkUrl(url)
                        });
                        if (script && script.parentElement) script.remove();
                        attemptIndex += 1;
                        tryNextScript();
                    }, 15000);
                };

                logMapplsDiagnostics('info', 'Loading Mappls SDK script.', diagnosticDetails);
                tryNextScript();
            });
        }

        return mapplsSdkPromise;
    }

    function loadGoogleSdk(apiKey) {
        if (!apiKey) {
            return Promise.reject(new Error('Google Maps API key is not configured.'));
        }

        if (window.google?.maps) {
            return Promise.resolve(window.google.maps);
        }

        if (!googleSdkPromise) {
            googleSdkPromise = new Promise((resolve, reject) => {
                const callback = `__adminGoogleMapsLoaded_${Date.now()}`;
                const script = document.createElement('script');
                const params = new URLSearchParams({
                    key: apiKey,
                    libraries: 'maps,marker,places,drawing',
                    callback,
                    v: 'weekly'
                });

                window[callback] = () => {
                    delete window[callback];
                    resolve(window.google.maps);
                };
                script.async = true;
                script.defer = true;
                script.src = `https://maps.googleapis.com/maps/api/js?${params.toString()}`;
                script.onerror = () => reject(new Error('Google Maps SDK script failed to load.'));
                document.head.appendChild(script);
            });
        }

        return googleSdkPromise;
    }

    function normalizeLocation(raw, fallbackLabel) {
        const lat = Number(raw?.lat ?? raw?.latitude ?? raw?.location?.lat ?? raw?.geometry?.location?.lat);
        const lng = Number(raw?.lng ?? raw?.longitude ?? raw?.location?.lng ?? raw?.geometry?.location?.lng);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;

        return {
            lat,
            lng,
            name: raw?.placeName || raw?.name || raw?.formatted_address || raw?.formattedAddress || fallbackLabel,
            address: raw?.placeAddress || raw?.address || raw?.formatted_address || raw?.formattedAddress || ''
        };
    }

    async function fetchJson(url, params) {
        const target = new URL(url, window.location.origin);
        Object.entries(params || {}).forEach(([key, value]) => {
            if (value !== undefined && value !== null && value !== '') {
                target.searchParams.set(key, value);
            }
        });

        const response = await fetch(target.toString(), {headers: {Accept: 'application/json'}});
        const payload = await response.json().catch(() => null);
        if (!response.ok || payload?.success === false) {
            throw new Error(payload?.message || 'Map request failed.');
        }

        return payload?.data ?? payload;
    }

    async function resolveMapplsLocation(item, routes, query) {
        const direct = normalizeLocation(item, query);
        if (direct) return direct;

        const eloc = item?.eLoc || item?.eloc || item?.placeId || item?.place_id;
        if (eloc && routes?.placeDetails) {
            const details = await fetchJson(routes.placeDetails, {eloc});
            const fromDetails = normalizeLocation({...item, ...details}, query);
            if (fromDetails) return fromDetails;
        }

        const address = item?.placeAddress || item?.formattedAddress || item?.address || item?.placeName || query;
        if (address && routes?.geocode) {
            const geocode = await fetchJson(routes.geocode, {address, itemCount: 1});
            const raw = geocode?.copResults?.[0] || geocode?.results?.[0] || geocode?.data?.[0] || geocode?.result;
            return normalizeLocation({...item, ...raw}, query);
        }

        return null;
    }

    async function searchMappls(query, routes) {
        if (!query || !routes?.autosuggest) return [];

        const data = await fetchJson(routes.autosuggest, {query, region: 'IND'});
        const items = data?.suggestedLocations || data?.predictions || data?.places || data?.data || data?.results || [];
        const resolved = await Promise.all(
            (Array.isArray(items) ? items : []).slice(0, 8).map((item) => resolveMapplsLocation(item, routes, query))
        );

        return resolved.filter(Boolean);
    }

    function addMapplsMarker(map, position, options) {
        const markerOptions = {
            map,
            position,
            fitbounds: false,
            draggable: Boolean(options?.draggable)
        };

        try {
            return new window.mappls.Marker(markerOptions);
        } catch (error) {
            return window.mappls.Marker(markerOptions);
        }
    }

    function setMapplsMarkerPosition(marker, position) {
        if (!marker) return;
        if (typeof marker.setPosition === 'function') {
            marker.setPosition(position);
            return;
        }
        marker.position = position;
    }

    function setMapplsCenter(map, position) {
        if (!map || !position) return;
        const center = normalizeMapplsCenter(position);

        if (typeof map.setCenter === 'function') {
            map.setCenter(center);
            return;
        }

        if (typeof map.panTo === 'function') {
            map.panTo(center);
        }
    }

    function removeMapplsLayer(map, layer) {
        if (!layer) return;
        if (typeof layer.remove === 'function') {
            layer.remove();
            return;
        }
        if (window.mappls?.remove) {
            window.mappls.remove({map, layer});
        }
    }

    function addMapplsPolygon(map, path, options) {
        if (!path || path.length < 3) return null;

        const polygonOptions = {
            map,
            fillColor: options?.fillColor || '#ff0000',
            fillOpacity: options?.fillOpacity ?? 0.18,
            strokeColor: options?.strokeColor || '#ff0000',
            strokeOpacity: options?.strokeOpacity ?? 0.9,
            strokeWeight: options?.strokeWeight || 2,
            fitbounds: Boolean(options?.fitbounds),
            editable: Boolean(options?.editable)
        };

        try {
            return new window.mappls.Polygon({
                ...polygonOptions,
                path
            });
        } catch (error) {
            return new window.mappls.Polygon({
                ...polygonOptions,
                paths: path
            });
        }
    }

    async function createMapplsMap(containerId, config) {
        const api = await loadMapplsSdk(config.mapplsStaticKey);
        const mapOptions = {
            center: normalizeMapplsCenter(config.center),
            zoom: config.zoom || 13,
            zoomControl: true,
            location: true
        };
        let map = null;

        try {
            map = new api.Map(containerId, mapOptions);
        } catch (error) {
            logMapplsDiagnostics('warn', 'Constructor init failed, retrying function init.', {
                error: error?.message || String(error),
                api: describeMapplsGlobal()
            });
            map = api.Map(containerId, mapOptions);
        }

        if (config.onClick) {
            map.addListener('click', (event) => {
                const latLng = toLatLng(event);
                if (latLng) config.onClick(latLng, event);
            });
        }

        window.setTimeout(() => {
            if (typeof map.resize === 'function') map.resize();
        }, 0);

        return map;
    }

    async function createGoogleMap(containerId, config) {
        await loadGoogleSdk(config.googleMapKey);
        return new window.google.maps.Map(document.getElementById(containerId), {
            center: config.center,
            zoom: config.zoom || 13,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
            gestureHandling: 'cooperative'
        });
    }

    window.AdminMapProvider = {
        loadMapplsSdk,
        loadGoogleSdk,
        createMapplsMap,
        createGoogleMap,
        addMapplsMarker,
        addMapplsPolygon,
        removeMapplsLayer,
        setMapplsMarkerPosition,
        setMapplsCenter,
        searchMappls,
        toLatLng
    };
})(window, document);
