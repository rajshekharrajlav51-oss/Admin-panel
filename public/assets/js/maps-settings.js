(function () {
    document.addEventListener('DOMContentLoaded', async function () {
        const config = window.AdminMapConfig || {};
        const status = document.getElementById('map-settings-status');
        const search = document.getElementById('map-settings-search');
        const results = document.getElementById('map-settings-search-results');
        const providerSelect = document.getElementById('map-provider');
        let map = null;
        let marker = null;

        function setStatus(message, type) {
            if (!status) return;
            status.className = `alert alert-${type || 'info'} mb-3`;
            status.textContent = message;
        }

        async function render(provider) {
            const center = {
                lat: Number(document.getElementById('default-latitude')?.value) || config.defaultCenter?.lat || 28.6139,
                lng: Number(document.getElementById('default-longitude')?.value) || config.defaultCenter?.lng || 77.2090
            };
            const zoom = Number(document.getElementById('default-zoom')?.value) || config.defaultZoom || 13;
            document.getElementById('map-settings-preview').innerHTML = '';

            try {
                if (provider === 'google') {
                    const key = document.getElementById('google-map-key')?.value || config.googleMapKey;
                    map = await AdminMapProvider.createGoogleMap('map-settings-preview', {
                        googleMapKey: key,
                        center,
                        zoom
                    });
                    marker = new google.maps.Marker({map, position: center});
                    setStatus('Google Maps preview loaded.', 'success');
                    return;
                }

                const key = document.getElementById('mappls-static-key')?.value || config.mapplsStaticKey;
                map = await AdminMapProvider.createMapplsMap('map-settings-preview', {
                    mapplsStaticKey: key,
                    center,
                    zoom,
                    onClick: (latLng) => {
                        document.getElementById('default-latitude').value = latLng.lat;
                        document.getElementById('default-longitude').value = latLng.lng;
                        AdminMapProvider.setMapplsMarkerPosition(marker, latLng);
                    }
                });
                marker = AdminMapProvider.addMapplsMarker(map, center);
                setStatus('Mappls preview loaded with Static Key.', 'success');
            } catch (error) {
                setStatus(error.message || 'Map preview failed to load.', 'danger');
            }
        }

        async function runSearch() {
            const query = search?.value?.trim();
            if (!query || query.length < 3 || providerSelect?.value !== 'mappls') {
                results?.classList.add('d-none');
                return;
            }

            try {
                const locations = await AdminMapProvider.searchMappls(query, config.mapplsRoutes);
                results.innerHTML = locations.map((item, index) => (
                    `<button type="button" class="list-group-item list-group-item-action" data-index="${index}">
                        <div class="fw-semibold">${item.name || 'Selected location'}</div>
                        <small class="text-secondary">${item.address || `${item.lat}, ${item.lng}`}</small>
                    </button>`
                )).join('');
                results._locations = locations;
                results.classList.toggle('d-none', locations.length === 0);
            } catch (error) {
                results.innerHTML = `<div class="list-group-item text-danger">${error.message}</div>`;
                results.classList.remove('d-none');
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

            document.getElementById('default-latitude').value = location.lat;
            document.getElementById('default-longitude').value = location.lng;
            AdminMapProvider.setMapplsCenter(map, {lat: location.lat, lng: location.lng});
            AdminMapProvider.setMapplsMarkerPosition(marker, {lat: location.lat, lng: location.lng});
            results.classList.add('d-none');
        });

        providerSelect?.addEventListener('change', function () {
            render(this.value);
        });

        ['default-latitude', 'default-longitude', 'default-zoom', 'mappls-static-key', 'google-map-key'].forEach((id) => {
            document.getElementById(id)?.addEventListener('change', () => render(providerSelect?.value || config.provider || 'mappls'));
        });

        await render(providerSelect?.value || config.provider || 'mappls');
    });
})();
