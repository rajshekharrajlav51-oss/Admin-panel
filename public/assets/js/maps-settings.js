(function () {
    document.addEventListener('DOMContentLoaded', async function () {
        const config = window.AdminMapConfig || {};
        const status = document.getElementById('map-settings-status');
        const search = document.getElementById('map-settings-search');
        const results = document.getElementById('map-settings-search-results');
        const providerSelect = document.getElementById('map-provider');
        const preview = document.getElementById('map-settings-preview');
        let map = null;
        let marker = null;

        function setStatus(message, type) {
            if (!status) return;
            status.className = `alert alert-${type || 'info'} mb-3`;
            status.textContent = message;
        }

        function setProviderFields(provider) {
            document.querySelectorAll('[data-provider-field]').forEach((element) => {
                const visible = element.dataset.providerField === provider;
                element.classList.toggle('d-none', !visible);
                element.querySelectorAll('input, select, textarea').forEach((input) => {
                    input.disabled = !visible;
                });
            });
        }

        function clearPreview() {
            if (marker?.setMap) {
                marker.setMap(null);
            }

            if (map && window.google?.maps?.event) {
                window.google.maps.event.clearInstanceListeners(map);
            } else if (map && typeof map.remove === 'function') {
                map.remove();
            }

            map = null;
            marker = null;
            preview?.replaceChildren();
        }

        function updateDefaultCoordinates(latLng) {
            const latitude = document.getElementById('default-latitude');
            const longitude = document.getElementById('default-longitude');
            if (latitude) latitude.value = latLng.lat;
            if (longitude) longitude.value = latLng.lng;
        }

        function renderSearchResults(locations) {
            if (!results) return;

            results.replaceChildren();
            locations.forEach((item, index) => {
                const button = document.createElement('button');
                const title = document.createElement('div');
                const subtitle = document.createElement('small');

                button.type = 'button';
                button.className = 'list-group-item list-group-item-action';
                button.dataset.index = String(index);
                title.className = 'fw-semibold';
                title.textContent = item.name || 'Selected location';
                subtitle.className = 'text-secondary';
                subtitle.textContent = item.address || `${item.lat}, ${item.lng}`;

                button.appendChild(title);
                button.appendChild(subtitle);
                results.appendChild(button);
            });

            results._locations = locations;
            results.classList.toggle('d-none', locations.length === 0);
        }

        function renderSearchError(message) {
            if (!results) return;

            results.replaceChildren();
            const item = document.createElement('div');
            item.className = 'list-group-item text-danger';
            item.textContent = message || 'Location search failed.';
            results.appendChild(item);
            results.classList.remove('d-none');
        }

        async function render(provider) {
            const center = {
                lat: Number(document.getElementById('default-latitude')?.value) || config.defaultCenter?.lat || 28.6139,
                lng: Number(document.getElementById('default-longitude')?.value) || config.defaultCenter?.lng || 77.2090
            };
            const zoom = Number(document.getElementById('default-zoom')?.value) || config.defaultZoom || 13;
            clearPreview();
            setProviderFields(provider);

            try {
                const key = document.getElementById('google-map-key')?.value || config.googleMapKey;
                map = await AdminMapProvider.createGoogleMap('map-settings-preview', {
                    googleMapKey: key,
                    center,
                    zoom
                });
                marker = AdminMapProvider.addGoogleMarker(map, center, {draggable: true});
                map.addListener('click', (event) => {
                    if (!event.latLng) return;
                    const latLng = {lat: event.latLng.lat(), lng: event.latLng.lng()};
                    updateDefaultCoordinates(latLng);
                    AdminMapProvider.setGoogleMarkerPosition(marker, latLng);
                });
                marker.addListener('dragend', (event) => {
                    if (!event.latLng) return;
                    updateDefaultCoordinates({lat: event.latLng.lat(), lng: event.latLng.lng()});
                });
                setStatus('Google Maps preview loaded.', 'success');
            } catch (error) {
                setStatus(error.message || 'Map preview failed to load.', 'danger');
            }
        }

        async function runSearch() {
            const query = search?.value?.trim();
            if (!query || query.length < 3) {
                results?.classList.add('d-none');
                return;
            }

            try {
                renderSearchResults(await AdminMapProvider.searchGoogle(query, map));
            } catch (error) {
                renderSearchError(error.message);
            }
        }

        search?.addEventListener('input', function () {
            window.clearTimeout(search._timer);
            search._timer = window.setTimeout(runSearch, 350);
        });

        results?.addEventListener('click', function (event) {
            const button = event.target.closest('[data-index]');
            if (!button) return;
            const location = results._locations?.[Number(button.dataset.index)];
            if (!location) return;

            const latLng = {lat: location.lat, lng: location.lng};
            updateDefaultCoordinates(latLng);
            AdminMapProvider.setGoogleCenter(map, latLng);
            AdminMapProvider.setGoogleMarkerPosition(marker, latLng);
            results.classList.add('d-none');
        });

        providerSelect?.addEventListener('change', function () {
            render(this.value);
        });

        ['default-latitude', 'default-longitude', 'default-zoom', 'google-map-key'].forEach((id) => {
            document.getElementById(id)?.addEventListener('change', () => render(providerSelect?.value || config.provider || 'google'));
        });

        await render(providerSelect?.value || config.provider || 'google');
    });
})();
