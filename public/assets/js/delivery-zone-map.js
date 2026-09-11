(function (window, document) {
    let deliveryZoneMapInitialized = false;

    function readBoundary(config, boundaryInput) {
        if (config.boundary) return config.boundary;
        if (!boundaryInput?.value) return null;

        try {
            return JSON.parse(boundaryInput.value);
        } catch (error) {
            return null;
        }
    }

    function showMapError(mapElement, message) {
        mapElement.replaceChildren();
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger m-3';
        alert.textContent = message || 'Map failed to load.';
        mapElement.appendChild(alert);
    }

    function renderLocationResults(results, locations) {
        results.replaceChildren();
        locations.forEach((location, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'list-group-item list-group-item-action';
            button.dataset.index = String(index);
            button.textContent = location.label || `${location.lat}, ${location.lng}`;
            results.appendChild(button);
        });
        results._locations = locations;
        results.classList.toggle('d-none', locations.length === 0);
    }

    function renderSearchError(results, message) {
        results.replaceChildren();
        const item = document.createElement('div');
        item.className = 'list-group-item text-danger';
        item.textContent = message || 'Location search failed.';
        results.appendChild(item);
        results.classList.remove('d-none');
    }

    function initializeDeliveryZoneMap() {
        if (deliveryZoneMapInitialized) return;

        const config = window.DeliveryZoneMapConfig || {};
        const mapElement = document.getElementById('delivery-zone-map');
        if (!mapElement) return;

        deliveryZoneMapInitialized = true;
        if (mapElement._deliveryZoneProvider) {
            mapElement._deliveryZoneProvider.destroyMap();
            mapElement._deliveryZoneProvider = null;
        }

        const latitude = document.getElementById('center-latitude');
        const longitude = document.getElementById('center-longitude');
        const radius = document.getElementById('radius-km');
        const boundaryInput = document.getElementById('boundary-json');
        const search = document.getElementById('zone-location-search');
        const results = document.getElementById('zone-location-results');

        const updateCoordinates = (center) => {
            if (!latitude || !longitude) return;
            latitude.value = Number(center.lat).toFixed(8);
            longitude.value = Number(center.lng).toFixed(8);
        };

        try {
            if (!window.MapProvider) {
                throw new Error('Map provider failed to load.');
            }

            const provider = window.MapProvider.create(config.provider || 'google', mapElement, {
                center: config.center,
                radiusKm: radius?.value || config.radiusKm,
                boundary: readBoundary(config, boundaryInput),
                zoom: config.zoom,
                readOnly: Boolean(config.readOnly),
                onCenterChange: updateCoordinates
            });

            mapElement._deliveryZoneProvider = provider;
            provider.setRadius(Number(radius?.value || config.radiusKm || 1));
            updateCoordinates(provider.getCenter());
        } catch (error) {
            showMapError(mapElement, error.message);
            return;
        }

        const provider = mapElement._deliveryZoneProvider;

        radius?.addEventListener('input', () => {
            provider.setRadius(Number(radius.value));
        });

        document.getElementById('use-current-location')?.addEventListener('click', () => {
            if (!navigator.geolocation) {
                window.Toast?.fire({icon: 'error', title: 'Geolocation is not available.'});
                return;
            }

            navigator.geolocation.getCurrentPosition((position) => {
                provider.setCenter({lat: position.coords.latitude, lng: position.coords.longitude});
                updateCoordinates(provider.getCenter());
                provider.invalidateSize();
            }, () => window.Toast?.fire({icon: 'error', title: 'Unable to read your location.'}));
        });

        if (config.readOnly) return;

        search?.addEventListener('input', function () {
            window.clearTimeout(search._timer);
            search._timer = window.setTimeout(async () => {
                const query = search.value.trim();
                if (query.length < 3) {
                    results?.classList.add('d-none');
                    return;
                }

                if (!results) return;

                try {
                    renderLocationResults(results, await provider.searchLocation(query));
                } catch (error) {
                    renderSearchError(results, error.message);
                }
            }, 400);
        });

        results?.addEventListener('click', (event) => {
            const button = event.target.closest('[data-index]');
            const location = results._locations?.[Number(button?.dataset.index)];
            if (!location) return;

            provider.setCenter(location);
            updateCoordinates(provider.getCenter());
            results.classList.add('d-none');
        });

        window.addEventListener('beforeunload', () => provider.destroyMap(), {once: true});
        window.setTimeout(() => provider.invalidateSize(), 200);
    }

    window.initDeliveryZoneMap = initializeDeliveryZoneMap;

    document.addEventListener('DOMContentLoaded', () => {
        if (window.DeliveryZoneGoogleMapsConfigurationMissing) {
            const mapElement = document.getElementById('delivery-zone-map');
            if (mapElement) {
                showMapError(mapElement, 'Google Maps API key is not configured.');
            }
            return;
        }

        if (window.google?.maps) {
            initializeDeliveryZoneMap();
        }
    });
})(window, document);
