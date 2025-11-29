@props([
    'mapId' => 'location-picker-map',
    'latitudeInputId' => 'latitude',
    'longitudeInputId' => 'longitude',
    'defaultLat' => -6.49492336972348,
    'defaultLng' => 107.53623899978002,
    'zoom' => 15,
    'draggable' => true,
    'showSatelliteToggle' => true
])

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #{{ $mapId }} {
        height: 400px;
        width: 100%;
        border-radius: 0.75rem;
        z-index: 1;
    }
    .leaflet-container {
        border-radius: 0.75rem;
    }
</style>
@endpush

<div id="{{ $mapId }}"></div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    const mapId = '{{ $mapId }}';
    const latInputId = '{{ $latitudeInputId }}';
    const lngInputId = '{{ $longitudeInputId }}';
    const defaultLat = {{ $defaultLat }};
    const defaultLng = {{ $defaultLng }};
    const zoom = {{ $zoom }};
    const draggable = {{ $draggable ? 'true' : 'false' }};
    const showSatelliteToggle = {{ $showSatelliteToggle ? 'true' : 'false' }};

    // Unique IDs untuk setiap instance
    const uniqueId = mapId.replace(/[^a-zA-Z0-9]/g, '_');
    const toggleBtnId = 'toggle-satellite-' + uniqueId;

    let map, marker, currentTileLayer;
    let satelliteMode = false;

    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri',
        maxZoom: 19
    });

    function initMap() {
        const latInput = document.getElementById(latInputId);
        const lngInput = document.getElementById(lngInputId);

        if (!latInput || !lngInput) {
            console.error('Latitude or Longitude input not found');
            return;
        }

        // Get initial coordinates from input or use default
        let lat = parseFloat(latInput.value) || defaultLat;
        let lng = parseFloat(lngInput.value) || defaultLng;

        // Create map
        map = L.map(mapId).setView([lat, lng], zoom);

        // Add default tile layer
        currentTileLayer = osmLayer;
        currentTileLayer.addTo(map);

        // Add satellite toggle checkbox
        if (showSatelliteToggle) {
            const satelliteControl = L.control({ position: 'topright' });
            satelliteControl.onAdd = function() {
                const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                div.style.background = 'transparent';
                div.style.border = 'none';
                div.style.boxShadow = 'none';
                div.innerHTML = '<label class="flex items-center gap-2 bg-white px-3 py-2 rounded shadow text-xs font-semibold cursor-pointer hover:bg-gray-50 transition" style="margin: 0; user-select: none;"><input type="checkbox" id="' + toggleBtnId + '" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" style="margin: 0; cursor: pointer;"><span style="cursor: pointer;">🛰️ Satelit</span></label>';
                L.DomEvent.disableClickPropagation(div);
                return div;
            };
            satelliteControl.addTo(map);

            const checkbox = document.getElementById(toggleBtnId);
            const label = checkbox.parentElement;
            const span = label.querySelector('span');

            checkbox.addEventListener('change', function(e) {
                e.stopPropagation();
                satelliteMode = checkbox.checked;
                map.removeLayer(currentTileLayer);
                currentTileLayer = satelliteMode ? satelliteLayer : osmLayer;
                currentTileLayer.addTo(map);
                span.textContent = satelliteMode ? '🗺️ Peta' : '🛰️ Satelit';
            });
        }

        // Create marker
        marker = L.marker([lat, lng], { draggable: draggable }).addTo(map);

        // Update inputs when marker is dragged
        if (draggable) {
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                latInput.value = pos.lat.toFixed(8);
                lngInput.value = pos.lng.toFixed(8);
            });
        }

        // Update marker when map is clicked
        map.on('click', function(e) {
            const pos = e.latlng;
            marker.setLatLng(pos);
            latInput.value = pos.lat.toFixed(8);
            lngInput.value = pos.lng.toFixed(8);
        });

        // Update marker when inputs change
        function updateMarker() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);

            if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                const pos = [lat, lng];
                marker.setLatLng(pos);
                map.setView(pos, zoom);
            }
        }

        latInput.addEventListener('change', updateMarker);
        lngInput.addEventListener('change', updateMarker);
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }
})();
</script>
@endpush

