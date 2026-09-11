(function (window) {
    function toPoint(value) {
        const point = {
            lat: Number(value?.lat ?? value?.latitude ?? value?.[1]),
            lng: Number(value?.lng ?? value?.longitude ?? value?.[0])
        };

        return Number.isFinite(point.lat) && Number.isFinite(point.lng) ? point : null;
    }

    function normalizeBoundary(boundary) {
        if (!boundary) return [];

        let parsed = boundary;
        if (typeof parsed === 'string') {
            try {
                parsed = JSON.parse(parsed);
            } catch (error) {
                return [];
            }
        }

        const coordinates = parsed?.coordinates?.[0] || parsed;
        if (!Array.isArray(coordinates)) return [];

        return coordinates
            .map(toPoint)
            .filter(Boolean);
    }

    function fromGoogleLatLng(latLng) {
        return {
            lat: typeof latLng?.lat === 'function' ? latLng.lat() : Number(latLng?.lat),
            lng: typeof latLng?.lng === 'function' ? latLng.lng() : Number(latLng?.lng)
        };
    }

    class GoogleMapsProvider {
        constructor(container, options = {}) {
            this.container = container;
            this.options = options;
            this.map = null;
            this.marker = null;
            this.circle = null;
            this.boundaryLayer = null;
            this.geocoder = null;
            this.placesService = null;
            this.autocompleteService = null;
        }

        initialize() {
            if (!window.google?.maps) throw new Error('Google Maps library failed to load.');
            if (this.map) return this;

            const center = toPoint(this.options.center) || {lat: 28.6139, lng: 77.2090};
            this.geocoder = new window.google.maps.Geocoder();
            if (window.google.maps.places) {
                this.autocompleteService = new window.google.maps.places.AutocompleteService();
            }
            this.map = new window.google.maps.Map(this.container, {
                center,
                zoom: this.options.zoom || 13,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                gestureHandling: 'cooperative'
            });

            this.marker = new window.google.maps.Marker({
                map: this.map,
                position: center,
                draggable: !this.options.readOnly
            });

            this.circle = new window.google.maps.Circle({
                map: this.map,
                center,
                radius: Number(this.options.radiusKm || 1) * 1000,
                strokeColor: '#206bc4',
                strokeOpacity: 0.85,
                strokeWeight: 2,
                fillColor: '#4299e1',
                fillOpacity: 0.18,
                clickable: false
            });

            if (window.google.maps.places) {
                this.placesService = new window.google.maps.places.PlacesService(this.map);
            }

            this.map.addListener('click', (event) => {
                if (this.options.readOnly || !event.latLng) return;
                this.setCenter(fromGoogleLatLng(event.latLng));
                this.options.onCenterChange?.(this.getCenter());
            });

            this.marker.addListener('dragend', (event) => {
                if (!event.latLng) return;
                this.setCenter(fromGoogleLatLng(event.latLng), false);
                this.options.onCenterChange?.(this.getCenter());
            });

            this.setBoundary(this.options.boundary, {fitBounds: Boolean(this.options.readOnly)});
            window.setTimeout(() => this.invalidateSize(), 0);
            return this;
        }

        setCenter(center, recenter = true) {
            const normalized = toPoint(center);
            if (!normalized || !this.map || !this.marker || !this.circle) return;

            this.marker.setPosition(normalized);
            this.circle.setCenter(normalized);
            if (recenter) {
                this.map.setCenter(normalized);
            }
        }

        getCenter() {
            const point = this.marker?.getPosition();
            return fromGoogleLatLng(point);
        }

        setRadius(radiusKm) {
            const radius = Number(radiusKm);
            if (!this.circle || !Number.isFinite(radius) || radius <= 0) return;

            this.circle.setRadius(radius * 1000);
        }

        setBoundary(boundary, options = {}) {
            const points = normalizeBoundary(boundary);
            if (!this.map) return [];

            if (this.boundaryLayer) {
                this.boundaryLayer.setMap(null);
                this.boundaryLayer = null;
            }

            if (points.length < 3) return [];

            this.boundaryLayer = new window.google.maps.Polygon({
                paths: points,
                map: this.map,
                strokeColor: options.color || '#d63939',
                strokeOpacity: 0.9,
                strokeWeight: options.weight || 2,
                fillColor: options.fillColor || '#d63939',
                fillOpacity: options.fillOpacity ?? 0.12,
                clickable: false
            });

            if (options.fitBounds) {
                const bounds = new window.google.maps.LatLngBounds();
                points.forEach((point) => bounds.extend(point));
                this.map.fitBounds(bounds, 24);
            }

            return points;
        }

        invalidateSize() {
            if (!this.map) return;
            const center = this.map.getCenter();
            window.google.maps.event.trigger(this.map, 'resize');
            if (center) this.map.setCenter(center);
        }

        async searchLocation(query) {
            if (!query || !this.geocoder) return [];

            const places = await this.searchPlaces(query);
            if (places.length) return places;

            const response = await this.geocoder.geocode({address: query});
            return (response.results || []).slice(0, 5).map((item) => {
                const point = fromGoogleLatLng(item.geometry?.location);
                return {
                    lat: point.lat,
                    lng: point.lng,
                    label: item.formatted_address || item.name || `${point.lat}, ${point.lng}`
                };
            }).filter((item) => Number.isFinite(item.lat) && Number.isFinite(item.lng));
        }

        searchPlaces(query) {
            if (!this.autocompleteService || !this.placesService || !window.google.maps.places) {
                return Promise.resolve([]);
            }

            return new Promise((resolve) => {
                this.autocompleteService.getPlacePredictions({input: query}, async (predictions, status) => {
                    const serviceStatus = window.google.maps.places.PlacesServiceStatus;
                    if (status !== serviceStatus.OK || !Array.isArray(predictions)) {
                        resolve([]);
                        return;
                    }

                    const locations = await Promise.all(predictions.slice(0, 5).map((prediction) => (
                        new Promise((placeResolve) => {
                            this.placesService.getDetails({
                                placeId: prediction.place_id,
                                fields: ['geometry', 'formatted_address', 'name']
                            }, (place, detailsStatus) => {
                                if (detailsStatus !== serviceStatus.OK || !place?.geometry?.location) {
                                    placeResolve(null);
                                    return;
                                }

                                const point = fromGoogleLatLng(place.geometry.location);
                                placeResolve({
                                    lat: point.lat,
                                    lng: point.lng,
                                    label: place.formatted_address || place.name || prediction.description
                                });
                            });
                        })
                    )));

                    resolve(locations.filter((item) => item && Number.isFinite(item.lat) && Number.isFinite(item.lng)));
                });
            });
        }

        destroyMap() {
            if (this.marker) this.marker.setMap(null);
            if (this.circle) this.circle.setMap(null);
            if (this.boundaryLayer) this.boundaryLayer.setMap(null);
            if (this.map) window.google?.maps?.event?.clearInstanceListeners(this.map);
            this.map = null;
            this.marker = null;
            this.circle = null;
            this.boundaryLayer = null;
            this.geocoder = null;
            this.placesService = null;
            this.autocompleteService = null;
        }
    }

    window.MapProvider = {
        create: (provider, container, options) => {
            if (provider !== 'google') throw new Error(`Unsupported map provider: ${provider}`);
            return new GoogleMapsProvider(container, options).initialize();
        },
        GoogleMapsProvider
    };
})(window);
