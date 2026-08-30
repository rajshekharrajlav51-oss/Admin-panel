let dzMap = null;
let dzMarker = null;
let dzPolygon = null;
let dzOriginalPath = [];
let dzDraftPath = [];
let dzDraftMarkers = [];
let dzProvider = 'mappls';

function getInput(id) {
    return document.getElementById(id);
}

function getCenter() {
    const lat = Number(getInput('center-latitude')?.value);
    const lng = Number(getInput('center-longitude')?.value);
    const configured = window.AdminMapConfig?.defaultCenter || {lat: 28.6139, lng: 77.2090};

    return {
        lat: Number.isFinite(lat) ? lat : configured.lat,
        lng: Number.isFinite(lng) ? lng : configured.lng
    };
}

function readBoundary() {
    const value = getInput('boundary-json')?.value;
    if (!value) return [];

    try {
        const parsed = JSON.parse(value);
        if (!Array.isArray(parsed)) return [];
        return parsed
            .map((point) => ({lat: Number(point.lat), lng: Number(point.lng)}))
            .filter((point) => Number.isFinite(point.lat) && Number.isFinite(point.lng));
    } catch (error) {
        return [];
    }
}

function updateBoundaryInput(path) {
    if (!Array.isArray(path) || path.length === 0) {
        getInput('boundary-json').value = '';
        return;
    }

    getInput('boundary-json').value = JSON.stringify(path);
    const center = getPolygonCentroid(path);
    if (center) {
        getInput('center-latitude').value = center.lat;
        getInput('center-longitude').value = center.lng;
        getInput('radius-km').value = getMaxRadiusKm(center, path).toFixed(3);
    }
}

function getPolygonCentroid(path) {
    if (!path.length) return null;
    const total = path.reduce((carry, point) => ({
        lat: carry.lat + point.lat,
        lng: carry.lng + point.lng
    }), {lat: 0, lng: 0});

    return {lat: total.lat / path.length, lng: total.lng / path.length};
}

function getMaxRadiusKm(center, path) {
    return path.reduce((max, point) => Math.max(max, haversineDistance(center, point)), 0);
}

function haversineDistance(coord1, coord2) {
    const radius = 6371;
    const dLat = toRad(coord2.lat - coord1.lat);
    const dLng = toRad(coord2.lng - coord1.lng);
    const lat1 = toRad(coord1.lat);
    const lat2 = toRad(coord2.lat);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
        + Math.sin(dLng / 2) * Math.sin(dLng / 2) * Math.cos(lat1) * Math.cos(lat2);

    return radius * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
}

function toRad(deg) {
    return deg * Math.PI / 180;
}

function clearMapplsDraft() {
    dzDraftMarkers.forEach((marker) => AdminMapProvider.removeMapplsLayer(dzMap, marker));
    dzDraftMarkers = [];
    dzDraftPath = [];
}

function clearMapplsPolygon() {
    AdminMapProvider.removeMapplsLayer(dzMap, dzPolygon);
    dzPolygon = null;
    clearMapplsDraft();
    getInput('boundary-json').value = '';
}

function drawMapplsPolygon(path, options) {
    AdminMapProvider.removeMapplsLayer(dzMap, dzPolygon);
    dzPolygon = AdminMapProvider.addMapplsPolygon(dzMap, path, {
        fillColor: options?.fillColor || '#ff0000',
        fillOpacity: options?.fillOpacity ?? 0.18,
        strokeColor: options?.strokeColor || '#ff0000',
        strokeOpacity: options?.strokeOpacity ?? 0.9,
        strokeWeight: options?.strokeWeight || 2,
        fitbounds: Boolean(options?.fitbounds),
        editable: Boolean(options?.editable)
    });
}

function addMapplsVertex(latLng) {
    dzDraftPath.push(latLng);
    dzDraftMarkers.push(AdminMapProvider.addMapplsMarker(dzMap, latLng));
    updateBoundaryInput(dzDraftPath);

    if (dzDraftPath.length >= 3) {
        drawMapplsPolygon(dzDraftPath, {editable: true});
    }
}

async function initMapplsDeliveryZone() {
    const config = window.AdminMapConfig || {};
    const center = getCenter();

    dzMap = await AdminMapProvider.createMapplsMap('map', {
        mapplsStaticKey: config.mapplsStaticKey,
        center,
        zoom: config.defaultZoom || 13,
        onClick: (latLng) => addMapplsVertex(latLng)
    });

    dzMarker = AdminMapProvider.addMapplsMarker(dzMap, center);
    dzOriginalPath = readBoundary();
    if (dzOriginalPath.length >= 3) {
        dzDraftPath = dzOriginalPath.slice();
        drawMapplsPolygon(dzDraftPath, {fitbounds: true, editable: true});
        updateBoundaryInput(dzDraftPath);
    }

    await renderOtherDeliveryZonesMappls();
    wireMapplsSearch();
}

async function renderOtherDeliveryZonesMappls() {
    const currentZoneIdEl = getInput('current-zone-id');
    const currentZoneId = currentZoneIdEl ? Number(currentZoneIdEl.value) : null;
    const response = await fetch('/api/delivery-zone?per_page=500', {headers: {Accept: 'application/json'}});
    if (!response.ok) return;

    const json = await response.json();
    const items = (json?.data && Array.isArray(json.data.data)) ? json.data.data : (Array.isArray(json.data) ? json.data : []);
    items.forEach((zone) => {
        if (currentZoneId && Number(zone.id) === currentZoneId) return;
        if (!Array.isArray(zone.boundary_json) || zone.boundary_json.length < 3) return;
        const path = zone.boundary_json
            .map((point) => ({lat: Number(point.lat), lng: Number(point.lng)}))
            .filter((point) => Number.isFinite(point.lat) && Number.isFinite(point.lng));
        if (path.length < 3) return;

        AdminMapProvider.addMapplsPolygon(dzMap, path, {
            fillColor: '#1a73e8',
            fillOpacity: 0.08,
            strokeColor: '#0066ff',
            strokeOpacity: 0.8
        });
    });
}

function wireMapplsSearch() {
    const search = getInput('delivery-zone-search');
    const results = getInput('delivery-zone-search-results');
    const routes = window.AdminMapConfig?.mapplsRoutes;
    if (!search || !results) return;

    search.addEventListener('input', function () {
        window.clearTimeout(search._timer);
        search._timer = window.setTimeout(async () => {
            const query = search.value.trim();
            if (query.length < 3) {
                results.classList.add('d-none');
                return;
            }

            try {
                const locations = await AdminMapProvider.searchMappls(query, routes);
                results._locations = locations;
                results.innerHTML = locations.map((item, index) => (
                    `<button type="button" class="list-group-item list-group-item-action" data-index="${index}">
                        <div class="fw-semibold">${item.name || 'Selected location'}</div>
                        <small class="text-secondary">${item.address || `${item.lat}, ${item.lng}`}</small>
                    </button>`
                )).join('');
                results.classList.toggle('d-none', locations.length === 0);
            } catch (error) {
                results.innerHTML = `<div class="list-group-item text-danger">${error.message}</div>`;
                results.classList.remove('d-none');
            }
        }, 350);
    });

    results.addEventListener('click', function (event) {
        const button = event.target.closest('[data-index]');
        if (!button) return;
        const location = results._locations?.[Number(button.dataset.index)];
        if (!location) return;

        const latLng = {lat: location.lat, lng: location.lng};
        getInput('center-latitude').value = latLng.lat;
        getInput('center-longitude').value = latLng.lng;
        AdminMapProvider.setMapplsCenter(dzMap, latLng);
        AdminMapProvider.setMapplsMarkerPosition(dzMarker, latLng);
        results.classList.add('d-none');
    });
}

async function initGoogleDeliveryZone() {
    const config = window.AdminMapConfig || {};
    const [{Map}, {AdvancedMarkerElement}, {DrawingManager}] = await Promise.all([
        google.maps.importLibrary('maps'),
        google.maps.importLibrary('marker'),
        google.maps.importLibrary('drawing')
    ]);

    const center = getCenter();
    dzMap = new Map(document.getElementById('map'), {
        center,
        zoom: config.defaultZoom || 13,
        mapId: '4504f8b37365c3d0',
        mapTypeControl: false
    });
    dzMarker = new AdvancedMarkerElement({map: dzMap, position: center});

    const drawingManager = new DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: ['polygon']
        },
        polygonOptions: {
            fillColor: '#FF0000',
            fillOpacity: 0.2,
            strokeWeight: 2,
            clickable: true,
            editable: true,
            zIndex: 1
        }
    });
    drawingManager.setMap(dzMap);

    google.maps.event.addListener(drawingManager, 'polygoncomplete', function (newPolygon) {
        if (dzPolygon) dzPolygon.setMap(null);
        dzPolygon = newPolygon;
        updateGoogleBoundaryInput(dzPolygon);
        setGooglePolygonListeners(dzPolygon);
        drawingManager.setDrawingMode(null);
    });

    const boundary = readBoundary();
    dzOriginalPath = boundary.slice();
    if (boundary.length >= 3) {
        const path = boundary.map((coord) => new google.maps.LatLng(coord.lat, coord.lng));
        dzPolygon = new google.maps.Polygon({
            paths: path,
            fillColor: '#FF0000',
            fillOpacity: 0.2,
            strokeWeight: 2,
            editable: true,
            map: dzMap
        });
        dzMap.fitBounds(getGoogleBoundsForPath(path));
        updateGoogleBoundaryInput(dzPolygon);
        setGooglePolygonListeners(dzPolygon);
    }
}

function updateGoogleBoundaryInput(polygon) {
    const path = polygon.getPath().getArray().map((latlng) => ({
        lat: latlng.lat(),
        lng: latlng.lng()
    }));
    updateBoundaryInput(path);
}

function setGooglePolygonListeners(polygon) {
    google.maps.event.clearListeners(polygon.getPath(), 'set_at');
    google.maps.event.clearListeners(polygon.getPath(), 'insert_at');
    google.maps.event.clearListeners(polygon.getPath(), 'remove_at');
    polygon.getPath().addListener('set_at', () => updateGoogleBoundaryInput(polygon));
    polygon.getPath().addListener('insert_at', () => updateGoogleBoundaryInput(polygon));
    polygon.getPath().addListener('remove_at', () => updateGoogleBoundaryInput(polygon));
}

function getGoogleBoundsForPath(path) {
    const bounds = new google.maps.LatLngBounds();
    path.forEach((latlng) => bounds.extend(latlng));
    return bounds;
}

function wireControls() {
    getInput('clear-last')?.addEventListener('click', function () {
        if (dzProvider === 'google') {
            if (dzPolygon) dzPolygon.setMap(null);
            dzPolygon = null;
            getInput('boundary-json').value = '';
            return;
        }

        clearMapplsPolygon();
    });

    getInput('reset-zone')?.addEventListener('click', function () {
        if (!dzOriginalPath.length) return;

        if (dzProvider === 'google') {
            if (dzPolygon) dzPolygon.setMap(null);
            const path = dzOriginalPath.map((coord) => new google.maps.LatLng(coord.lat, coord.lng));
            dzPolygon = new google.maps.Polygon({
                paths: path,
                fillColor: '#FF0000',
                fillOpacity: 0.2,
                strokeWeight: 2,
                editable: true,
                map: dzMap
            });
            dzMap.fitBounds(getGoogleBoundsForPath(path));
            updateGoogleBoundaryInput(dzPolygon);
            setGooglePolygonListeners(dzPolygon);
            return;
        }

        clearMapplsDraft();
        dzDraftPath = dzOriginalPath.slice();
        drawMapplsPolygon(dzDraftPath, {fitbounds: true, editable: true});
        updateBoundaryInput(dzDraftPath);
    });
}

document.addEventListener('DOMContentLoaded', async function () {
    dzProvider = window.AdminMapConfig?.provider || 'mappls';
    wireControls();

    try {
        if (dzProvider === 'google') {
            await AdminMapProvider.loadGoogleSdk(window.AdminMapConfig?.googleMapKey);
            await initGoogleDeliveryZone();
        } else {
            await initMapplsDeliveryZone();
        }
    } catch (error) {
        const mapEl = getInput('map');
        if (mapEl) {
            mapEl.innerHTML = `<div class="alert alert-danger m-3">${error.message || 'Map failed to load.'}</div>`;
        }
        console.error('Error initializing delivery zone map:', error);
    }

    document.addEventListener('click', function (event) {
        handleDelete(event, '.delete-delivery-zone', `/${panel}/delivery-zones/`, 'You are about to delete this Zone.');
    });
});
