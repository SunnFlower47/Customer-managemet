@extends('layouts.app')

@section('title', 'Mapping - WiFi Billing Management')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
<style>
    #mapping-map {
        height: calc(100vh - 200px);
        min-height: 600px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        z-index: 1;
    }
    .leaflet-container {
        z-index: 1;
    }
    .map-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 30;
        background: white;
        padding: 12px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        min-width: 200px;
        max-height: 80vh;
        overflow-y: auto;
    }
    .map-legend {
        position: absolute;
        bottom: 10px;
        left: 10px;
        z-index: 30;
        background: white;
        padding: 12px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        max-width: 180px;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-size: 12px;
    }
    .legend-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Mobile responsive */
    @media (max-width: 640px) {
        .map-controls {
            top: 5px;
            right: 5px;
            padding: 8px;
            min-width: 140px;
            max-width: 150px;
            max-height: 70vh;
            font-size: 10px;
        }
        .map-controls label {
            font-size: 10px !important;
            gap: 4px;
        }
        .map-controls .text-sm {
            font-size: 10px !important;
        }
        .map-controls .space-y-2 > * {
            margin-bottom: 4px;
        }
        .map-controls .space-y-3 > * {
            margin-bottom: 6px;
        }
        .map-controls input[type="checkbox"] {
            width: 12px;
            height: 12px;
        }
        .map-legend {
            bottom: 5px;
            left: 5px;
            padding: 6px 8px;
            max-width: 120px;
            font-size: 10px;
        }
        .map-legend p {
            font-size: 10px !important;
            margin-bottom: 4px !important;
        }
        .legend-item {
            gap: 4px;
            margin-bottom: 4px;
            font-size: 9px;
        }
        .legend-icon {
            width: 14px;
            height: 14px;
        }
        .legend-item span {
            font-size: 9px;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-map"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Mapping ODP & Pelanggan</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Visualisasi lokasi ODP dan pelanggan di peta</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            @can('edit-pelanggan')
            <button type="button"
                    onclick="openAddLocationModal()"
                    class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-plus mr-2 text-xs sm:text-sm"></i>Tambah Koordinat Pelanggan
            </button>
            @endcan
            <a href="{{ route('odps.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition">
                <i class="fas fa-list mr-2 text-xs sm:text-sm"></i>Daftar ODP
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="app-card app-card--soft">
        <form method="GET" action="{{ route('mapping.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-project-diagram mr-1"></i>Filter ODC
                </label>
                <select name="odc_id"
                        id="filter-odc"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua ODC</option>
                    @foreach($allOdcs as $odc)
                    <option value="{{ $odc->id }}" {{ request('odc_id') == $odc->id ? 'selected' : '' }}>{{ $odc->kode_odc }} - {{ $odc->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-filter mr-1"></i>Filter ODP
                </label>
                <select name="odp_id"
                        id="filter-odp"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua ODP</option>
                    @foreach($allOdps as $odp)
                    <option value="{{ $odp->id }}" {{ request('odp_id') == $odp->id ? 'selected' : '' }}>{{ $odp->kode_odp }} - {{ $odp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user-tie mr-1"></i>Filter Penagih
                </label>
                <select name="penagih_id"
                        id="filter-penagih"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua Penagih</option>
                    @foreach($penagihs as $penagih)
                    <option value="{{ $penagih->id }}" {{ request('penagih_id') == $penagih->id ? 'selected' : '' }}>{{ $penagih->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-info-circle mr-1"></i>Status Pelanggan
                </label>
                <select name="status"
                        id="filter-status"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="isolir" {{ request('status') === 'isolir' ? 'selected' : '' }}>Isolir</option>
                    <option value="bayar double" {{ request('status') === 'bayar double' ? 'selected' : '' }}>Bayar Double</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold bg-green-600 text-white rounded-xl hover:bg-green-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('mapping.index') }}"
                   class="px-4 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Map Container -->
    <div class="app-card app-card--soft p-0 relative">
        <div id="mapping-map"></div>

        <!-- Map Controls -->
        <div class="map-controls">
            <div class="space-y-3">
                <div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-layer-group text-purple-500"></i>Layer
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" id="toggle-odc" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span>ODC</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" id="toggle-odp" checked class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span>ODP</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" id="toggle-pelanggan" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span>Pelanggan</span>
                        </label>
                        {{-- <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" id="toggle-connection" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span>Garis ODC-ODP</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" id="toggle-pelanggan-connection" checked class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span>Garis Pelanggan-ODP</span>
                        </label>
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" id="toggle-odp-connection" checked class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                            <span>Garis ODP-ODP</span>
                        </label> --}}
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" id="toggle-satellite" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>Mode Satelit</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Legend -->
        <div class="map-legend">
            <p class="text-xs font-semibold text-gray-700 mb-2">Legenda</p>
            <div class="legend-item">
                <div class="legend-icon" style="background: #4f46e5;"></div>
                <span>ODC</span>
            </div>
            <div class="legend-item">
                <div class="legend-icon" style="background: #9333ea;"></div>
                <span>ODP</span>
            </div>
            <div class="legend-item">
                <div class="legend-icon" style="background: #10b981;"></div>
                <span>Pelanggan</span>
            </div>
            {{-- <div class="legend-item">
                <div style="width: 20px; height: 3px; background: #6366f1; border-radius: 2px; margin-right: 8px; opacity: 0.7;"></div>
                <span>Garis ODC-ODP</span>
            </div>
            <div class="legend-item">
                <div style="width: 20px; height: 2px; background: #10b981; border-radius: 2px; margin-right: 8px; opacity: 0.6;"></div>
                <span>Garis Pelanggan-ODP</span>
            </div>
            <div class="legend-item">
                <div style="width: 20px; height: 2.5px; background: #f59e0b; border-radius: 2px; margin-right: 8px; opacity: 0.7;"></div>
                <span>Garis ODP-ODP</span>
            </div> --}}
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="app-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Total ODP</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $odps->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="app-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Pelanggan dengan Koordinat</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pelanggans->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="app-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-semibold">Pelanggan Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pelanggans->where('status', 'aktif')->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-check text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="app-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-semibold">ODP Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $odps->where('status', 'aktif')->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Tambah Koordinat Pelanggan (Admin Only) -->
@can('edit-pelanggan')
<div id="add-location-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Tambah Koordinat Pelanggan</h3>
                <button onclick="closeAddLocationModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="p-6 space-y-4">
            <!-- Search Pelanggan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search mr-2 text-blue-500"></i>Cari Pelanggan
                </label>
                <input type="text"
                       id="search-pelanggan"
                       placeholder="Cari berdasarkan nama, PPPoE, atau nomor HP"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <div id="pelanggan-results" class="mt-2 space-y-2 max-h-48 overflow-y-auto hidden"></div>
            </div>

            <!-- Selected Pelanggan Info -->
            <div id="selected-pelanggan-info" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-900" id="selected-pelanggan-nama"></p>
                        <p class="text-xs text-gray-600" id="selected-pelanggan-pppoe"></p>
                    </div>
                    <button onclick="clearSelectedPelanggan()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Map Picker -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map mr-2 text-green-500"></i>Pilih Lokasi di Peta
                </label>
                <div id="location-picker-map" style="height: 300px; border-radius: 12px; border: 2px solid #e5e7eb;"></div>
                <div class="mt-2 flex items-center gap-2 text-xs text-gray-600">
                    <input type="checkbox" id="toggle-satellite-picker" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span>Mode Satelit</span>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>Klik di peta untuk mengatur koordinat
                </p>
            </div>

            <!-- Koordinat Input -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Latitude</label>
                    <input type="number"
                           id="modal-latitude"
                           step="any"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Longitude</label>
                    <input type="number"
                           id="modal-longitude"
                           step="any"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                </div>
            </div>

            <!-- ODP Selection -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>Pilih ODP Terkait
                </label>
                <select id="modal-odp-id"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    <option value="">Pilih ODP (Opsional)</option>
                    @foreach($allOdps as $odp)
                    <option value="{{ $odp->id }}">{{ $odp->kode_odp }} - {{ $odp->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
            <button onclick="closeAddLocationModal()"
                    class="px-6 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                Batal
            </button>
            <button onclick="savePelangganLocation()"
                    id="save-location-btn"
                    disabled
                    class="px-6 py-2.5 text-sm font-semibold bg-green-600 text-white rounded-xl hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-save mr-2"></i>Simpan Koordinat
            </button>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
    let map, odcLayer, odpLayer, pelangganLayer, connectionLayer, pelangganConnectionLayer, odpConnectionLayer, locationPickerMap, locationPickerMarker;
    let selectedPelangganId = null;
    let searchTimeout = null;
    let currentTileLayer = null;
    let satelliteMode = false;

    // Tile layer helpers
    const createOsmLayer = () => L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    });

    const createSatelliteLayer = () => L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri',
        maxZoom: 19
    });

    const mainOsmLayer = createOsmLayer();
    const mainSatelliteLayer = createSatelliteLayer();

    // Initialize main mapping map
    function initMainMap() {
        const defaultLat = -6.49492336972348;
        const defaultLng = 107.53623899978002;

        map = L.map('mapping-map').setView([defaultLat, defaultLng], 13);

        // Add default tile layer
        currentTileLayer = mainOsmLayer;
        currentTileLayer.addTo(map);

        // Create layer groups
        odcLayer = L.layerGroup().addTo(map);
        odpLayer = L.layerGroup().addTo(map);
        pelangganLayer = L.markerClusterGroup({
            chunkedLoading: true,
            maxClusterRadius: 50
        }).addTo(map);
        connectionLayer = L.layerGroup().addTo(map);
        pelangganConnectionLayer = L.layerGroup().addTo(map);
        odpConnectionLayer = L.layerGroup().addTo(map);

        // Load markers and connections
        loadOdcMarkers();
        loadODPMarkers();
        loadPelangganMarkers();
        // loadConnectionLines();
        // loadPelangganConnectionLines();
        // loadOdpConnectionLines();

        // Toggle layers
        document.getElementById('toggle-odc').addEventListener('change', function(e) {
            if (e.target.checked) {
                map.addLayer(odcLayer);
            } else {
                map.removeLayer(odcLayer);
            }
        });

        document.getElementById('toggle-odp').addEventListener('change', function(e) {
            if (e.target.checked) {
                map.addLayer(odpLayer);
            } else {
                map.removeLayer(odpLayer);
            }
        });

        document.getElementById('toggle-pelanggan').addEventListener('change', function(e) {
            if (e.target.checked) {
                map.addLayer(pelangganLayer);
            } else {
                map.removeLayer(pelangganLayer);
            }
        });

        // document.getElementById('toggle-connection').addEventListener('change', function(e) {
        //     if (e.target.checked) {
        //         map.addLayer(connectionLayer);
        //     } else {
        //         map.removeLayer(connectionLayer);
        //     }
        // });

        // document.getElementById('toggle-pelanggan-connection').addEventListener('change', function(e) {
        //     if (e.target.checked) {
        //         map.addLayer(pelangganConnectionLayer);
        //     } else {
        //         map.removeLayer(pelangganConnectionLayer);
        //     }
        // });

        // document.getElementById('toggle-odp-connection').addEventListener('change', function(e) {
        //     if (e.target.checked) {
        //         map.addLayer(odpConnectionLayer);
        //     } else {
        //         map.removeLayer(odpConnectionLayer);
        //     }
        // });

        // Toggle satellite mode
        document.getElementById('toggle-satellite').addEventListener('change', function(e) {
            satelliteMode = e.target.checked;
            map.removeLayer(currentTileLayer);
            currentTileLayer = satelliteMode ? mainSatelliteLayer : mainOsmLayer;
            currentTileLayer.addTo(map);
        });
    }

    // Load ODC markers
    function loadOdcMarkers() {
        odcLayer.clearLayers();

        @foreach($odcs as $odc)
        @if($odc->latitude && $odc->longitude)
        const odcIcon{{ $odc->id }} = L.divIcon({
            className: 'odc-marker',
            html: '<div style="background: #4f46e5; width: 34px; height: 34px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;"><i class="fas fa-project-diagram" style="color: white; font-size: 14px;"></i></div>',
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        const odcMarker{{ $odc->id }} = L.marker([{{ $odc->latitude }}, {{ $odc->longitude }}], { icon: odcIcon{{ $odc->id }} })
            .addTo(odcLayer)
            .bindPopup(`
                <div class="text-sm">
                    <h3 class="font-bold text-indigo-600 mb-1">{{ $odc->kode_odc }}</h3>
                    <p class="text-gray-700 mb-1">{{ $odc->nama }}</p>
                    <p class="text-xs text-gray-500 mb-2">{{ $odc->alamat ?? 'Tidak ada alamat' }}</p>
                    <p class="text-xs text-gray-600 mb-2">ODP tersambung: {{ $odc->odps->count() }}</p>
                    @if($odc->odps->count())
                    <div class="mb-2 max-h-24 overflow-y-auto">
                        @foreach($odc->odps as $odp)
                        <div class="flex items-center justify-between text-xs text-gray-700 mb-1">
                            <span>{{ $odp->kode_odp }} - {{ $odp->nama }}</span>
                            <a href="{{ route('odps.show', $odp) }}" class="text-blue-600 hover:underline">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    <a href="{{ route('odcs.show', $odc) }}" class="text-indigo-600 hover:underline text-xs mr-3">
                        <i class="fas fa-eye mr-1"></i>Detail ODC
                    </a>
                    @if($odc->latitude && $odc->longitude)
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $odc->latitude }},{{ $odc->longitude }}" target="_blank" class="text-green-600 hover:underline text-xs">
                        <i class="fas fa-external-link-alt mr-1"></i>Google Maps
                    </a>
                    @endif
                </div>
            `);
        @endif
        @endforeach
    }

    // Load ODP markers
    function loadODPMarkers() {
        odpLayer.clearLayers();

        @foreach($odps as $odp)
        const odpIcon{{ $odp->id }} = L.divIcon({
            className: 'odp-marker',
            html: '<div style="background: #9333ea; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;"><i class="fas fa-map-marker-alt" style="color: white; font-size: 14px;"></i></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const odpMarker{{ $odp->id }} = L.marker([{{ $odp->latitude }}, {{ $odp->longitude }}], { icon: odpIcon{{ $odp->id }} })
            .addTo(odpLayer)
            .bindPopup(`
                <div class="text-sm">
                    <h3 class="font-bold text-purple-600 mb-1">${@json($odp->kode_odp)}</h3>
                    <p class="text-gray-700 mb-1">${@json($odp->nama)}</p>
                    <p class="text-xs text-gray-500 mb-2">${@json($odp->alamat ?: 'Tidak ada alamat')}</p>
                    <p class="text-xs text-gray-600 mb-2">Port: ${@json($odp->port_terpakai)}/${@json($odp->kapasitas)}</p>
                    @if($odp->parentOdp)
                    <p class="text-xs text-amber-600 mb-1">
                        <i class="fas fa-link mr-1"></i>Terhubung ke ODP: <strong>${@json($odp->parentOdp->kode_odp)}</strong> - ${@json($odp->parentOdp->nama)}
                    </p>
                    @endif
                    @if($odp->odc)
                    <p class="text-xs text-indigo-600 mb-1">
                        <i class="fas fa-project-diagram mr-1"></i>ODC: <strong>${@json($odp->odc->kode_odc)}</strong> - ${@json($odp->odc->nama)}
                    </p>
                    @else
                    <p class="text-xs text-gray-400 mb-1">
                        <i class="fas fa-project-diagram mr-1"></i>Belum terhubung ke ODC
                    </p>
                    @endif
                    @if($odp->childOdps->count() > 0)
                    <p class="text-xs text-purple-600 mb-2">
                        <i class="fas fa-sitemap mr-1"></i>ODP Child: {{ $odp->childOdps->count() }} terhubung
                    </p>
                    @endif
                    <a href="{{ route('odps.show', $odp) }}" class="text-blue-600 hover:underline text-xs mr-3">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $odp->latitude }},{{ $odp->longitude }}" target="_blank" class="text-green-600 hover:underline text-xs">
                        <i class="fas fa-external-link-alt mr-1"></i>Google Maps
                    </a>
                </div>
            `);
        @endforeach
    }

    // Load pelanggan markers
    function loadPelangganMarkers() {
        pelangganLayer.clearLayers();

        @foreach($pelanggans as $pelanggan)
        const pelangganIcon{{ $pelanggan->id }} = L.divIcon({
            className: 'pelanggan-marker',
            html: '<div style="background: #10b981; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        L.marker([{{ $pelanggan->latitude }}, {{ $pelanggan->longitude }}], { icon: pelangganIcon{{ $pelanggan->id }} })
            .addTo(pelangganLayer)
            .bindPopup(`
                <div class="text-sm">
                    <h3 class="font-bold text-green-600 mb-1">${@json($pelanggan->nama)}</h3>
                    <p class="text-xs text-gray-600 mb-1">${@json($pelanggan->pppoe)}</p>
                    <p class="text-xs text-gray-500 mb-2">${@json($pelanggan->alamat ?: 'Tidak ada alamat')}</p>
                    @if($pelanggan->odp)
                    <p class="text-xs text-purple-600 mb-1"><i class="fas fa-map-marker-alt mr-1"></i>ODP: ${@json($pelanggan->odp->kode_odp)}</p>
                    @if($pelanggan->odp->odc)
                    <p class="text-xs text-indigo-600 mb-2"><i class="fas fa-project-diagram mr-1"></i>ODC: ${@json($pelanggan->odp->odc->kode_odc)}</p>
                    @endif
                    @endif
                    <a href="{{ route('pelanggans.show', $pelanggan) }}" class="text-blue-600 hover:underline text-xs mr-3">
                        <i class="fas fa-eye mr-1"></i>Detail
                    </a>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $pelanggan->latitude }},{{ $pelanggan->longitude }}" target="_blank" class="text-green-600 hover:underline text-xs">
                        <i class="fas fa-external-link-alt mr-1"></i>Google Maps
                    </a>
                </div>
            `);
        @endforeach
    }

    // Load connection lines (ODC to ODP)
    // Hanya untuk ODP yang terhubung langsung ke ODC (tidak punya parent_odp_id)
    // function loadConnectionLines() {
    //     connectionLayer.clearLayers();

    //     @foreach($odcs as $odc)
    //     @if($odc->latitude && $odc->longitude && $odc->odps->count() > 0)
    //     @foreach($odc->odps as $odp)
    //     @if($odp->latitude && $odp->longitude && !$odp->parent_odp_id)
    //     // Hanya gambar garis jika ODP terhubung langsung (tidak punya parent ODP)
    //     // Create polyline from ODC to ODP
    //     const connectionLine{{ $odc->id }}_{{ $odp->id }} = L.polyline(
    //         [
    //             [{{ $odc->latitude }}, {{ $odc->longitude }}],
    //             [{{ $odp->latitude }}, {{ $odp->longitude }}]
    //         ],
    //         {
    //             color: '#6366f1', // Indigo color
    //             weight: 3,
    //             opacity: 0.7,
    //             dashArray: '5, 5', // Dashed line
    //             smoothFactor: 1
    //         }
    //     ).addTo(connectionLayer);

    //     // Add popup to line showing connection info
    //     connectionLine{{ $odc->id }}_{{ $odp->id }}.bindPopup(`
    //         <div class="text-xs">
    //             <p class="font-semibold text-indigo-600 mb-1">Koneksi ODC-ODP</p>
    //             <p class="text-gray-700 mb-1">
    //                 <strong>ODC:</strong> {{ $odc->kode_odc }} - {{ $odc->nama }}
    //             </p>
    //             <p class="text-gray-700">
    //                 <strong>ODP:</strong> {{ $odp->kode_odp }} - {{ $odp->nama }}
    //             </p>
    //         </div>
    //     `);
    //     @endif
    //     @endforeach
    //     @endif
    //     @endforeach
    // }

    // // Load connection lines (Pelanggan to ODP)
    // function loadPelangganConnectionLines() {
    //     pelangganConnectionLayer.clearLayers();

    //     @foreach($pelanggans as $pelanggan)
    //     @if($pelanggan->odp && $pelanggan->latitude && $pelanggan->longitude && $pelanggan->odp->latitude && $pelanggan->odp->longitude)
    //     // Create polyline from Pelanggan to ODP
    //     const pelangganConnectionLine{{ $pelanggan->id }} = L.polyline(
    //         [
    //             [{{ $pelanggan->latitude }}, {{ $pelanggan->longitude }}],
    //             [{{ $pelanggan->odp->latitude }}, {{ $pelanggan->odp->longitude }}]
    //         ],
    //         {
    //             color: '#10b981', // Green color for pelanggan-ODP connection
    //             weight: 2,
    //             opacity: 0.6,
    //             dashArray: '3, 3', // Smaller dash for pelanggan connections
    //             smoothFactor: 1
    //         }
    //     ).addTo(pelangganConnectionLayer);

    //     // Add popup to line showing connection info
    //     pelangganConnectionLine{{ $pelanggan->id }}.bindPopup(`
    //         <div class="text-xs">
    //             <p class="font-semibold text-green-600 mb-1">Koneksi Pelanggan-ODP</p>
    //             <p class="text-gray-700 mb-1">
    //                 <strong>Pelanggan:</strong> {{ $pelanggan->nama }} ({{ $pelanggan->pppoe }})
    //             </p>
    //             <p class="text-gray-700">
    //                 <strong>ODP:</strong> {{ $pelanggan->odp->kode_odp }} - {{ $pelanggan->odp->nama }}
    //             </p>
    //         </div>
    //     `);
    //     @endif
    //     @endforeach
    // }

    // // Load connection lines (ODP to ODP parent-child)
    // function loadOdpConnectionLines() {
    //     odpConnectionLayer.clearLayers();

    //     @foreach($odps as $odp)
    //     @if($odp->parentOdp && $odp->latitude && $odp->longitude && $odp->parentOdp->latitude && $odp->parentOdp->longitude)
    //     // Create polyline from child ODP to parent ODP
    //     const odpConnectionLine{{ $odp->id }} = L.polyline(
    //         [
    //             [{{ $odp->latitude }}, {{ $odp->longitude }}],
    //             [{{ $odp->parentOdp->latitude }}, {{ $odp->parentOdp->longitude }}]
    //         ],
    //         {
    //             color: '#f59e0b', // Amber color for ODP-ODP connection
    //             weight: 2.5,
    //             opacity: 0.7,
    //             dashArray: '4, 4', // Dashed line
    //             smoothFactor: 1
    //         }
    //     ).addTo(odpConnectionLayer);

    //     // Add popup to line showing connection info
    //     odpConnectionLine{{ $odp->id }}.bindPopup(`
    //         <div class="text-xs">
    //             <p class="font-semibold text-amber-600 mb-1">Koneksi ODP Parent-Child</p>
    //             <p class="text-gray-700 mb-1">
    //                 <strong>Child:</strong> {{ $odp->kode_odp }} - {{ $odp->nama }}
    //             </p>
    //             <p class="text-gray-700">
    //                 <strong>Parent:</strong> {{ $odp->parentOdp->kode_odp }} - {{ $odp->parentOdp->nama }}
    //             </p>
    //         </div>
    //     `);
    //     @endif
    //     @endforeach
    // }

    // Initialize location picker map (for modal)
    let locationPickerTileLayer = null;

    function initLocationPickerMap() {
        const defaultLat = -6.49492336972348;
        const defaultLng = 107.53623899978002;

        locationPickerMap = L.map('location-picker-map').setView([defaultLat, defaultLng], 15);

        locationPickerTileLayer = createOsmLayer();
        locationPickerTileLayer.addTo(locationPickerMap);

        const satellitePickerCheckbox = document.getElementById('toggle-satellite-picker');
        if (satellitePickerCheckbox) {
            satellitePickerCheckbox.addEventListener('change', function(e) {
                if (locationPickerTileLayer) {
                    locationPickerMap.removeLayer(locationPickerTileLayer);
                }
                locationPickerTileLayer = e.target.checked ? createSatelliteLayer() : createOsmLayer();
                locationPickerTileLayer.addTo(locationPickerMap);
            });
        }

        locationPickerMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(locationPickerMap);

        locationPickerMarker.on('dragend', function(e) {
            const pos = locationPickerMarker.getLatLng();
            document.getElementById('modal-latitude').value = pos.lat.toFixed(8);
            document.getElementById('modal-longitude').value = pos.lng.toFixed(8);
        });

        locationPickerMap.on('click', function(e) {
            const pos = e.latlng;
            locationPickerMarker.setLatLng(pos);
            document.getElementById('modal-latitude').value = pos.lat.toFixed(8);
            document.getElementById('modal-longitude').value = pos.lng.toFixed(8);
        });

        // Force re-render so map tiles appear even inside hidden modal
        setTimeout(() => {
            locationPickerMap.invalidateSize();
        }, 200);
    }

    // Search pelanggan
    document.getElementById('search-pelanggan')?.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();

        if (query.length < 2) {
            document.getElementById('pelanggan-results').classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`{{ route('mapping.search-pelanggans') }}?search=${encodeURIComponent(query)}`);
                const data = await response.json();

                const resultsDiv = document.getElementById('pelanggan-results');
                resultsDiv.innerHTML = '';

                if (data.success && data.data.length > 0) {
                    resultsDiv.classList.remove('hidden');
                    data.data.forEach(pelanggan => {
                        const item = document.createElement('div');
                        item.className = 'bg-white border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer';
                        item.innerHTML = `
                            <p class="text-sm font-semibold text-gray-900">${pelanggan.nama}</p>
                            <p class="text-xs text-gray-500">${pelanggan.pppoe} • ${pelanggan.no_hp}</p>
                            ${pelanggan.latitude ? '<p class="text-xs text-green-600 mt-1"><i class="fas fa-check-circle mr-1"></i>Sudah ada koordinat</p>' : ''}
                        `;
                        item.addEventListener('click', () => selectPelanggan(pelanggan));
                        resultsDiv.appendChild(item);
                    });
                } else {
                    resultsDiv.classList.add('hidden');
                }
            } catch (error) {
                console.error('Error searching pelanggans:', error);
            }
        }, 300);
    });

    // Select pelanggan
    function selectPelanggan(pelanggan) {
        selectedPelangganId = pelanggan.id;
        document.getElementById('selected-pelanggan-nama').textContent = pelanggan.nama;
        document.getElementById('selected-pelanggan-pppoe').textContent = pelanggan.pppoe;
        document.getElementById('selected-pelanggan-info').classList.remove('hidden');
        document.getElementById('pelanggan-results').classList.add('hidden');
        document.getElementById('search-pelanggan').value = '';

        // Set existing coordinates if available
        if (pelanggan.latitude && pelanggan.longitude) {
            document.getElementById('modal-latitude').value = pelanggan.latitude;
            document.getElementById('modal-longitude').value = pelanggan.longitude;
            locationPickerMarker.setLatLng([pelanggan.latitude, pelanggan.longitude]);
            locationPickerMap.setView([pelanggan.latitude, pelanggan.longitude], 15);
        }

        // Set ODP if available
        if (pelanggan.odp_id) {
            document.getElementById('modal-odp-id').value = pelanggan.odp_id;
        }

        updateSaveButton();
    }

    // Clear selected pelanggan
    function clearSelectedPelanggan() {
        selectedPelangganId = null;
        document.getElementById('selected-pelanggan-info').classList.add('hidden');
        document.getElementById('modal-latitude').value = '';
        document.getElementById('modal-longitude').value = '';
        document.getElementById('modal-odp-id').value = '';
        updateSaveButton();
    }

    // Update save button state
    function updateSaveButton() {
        const lat = document.getElementById('modal-latitude').value;
        const lng = document.getElementById('modal-longitude').value;
        const btn = document.getElementById('save-location-btn');

        if (selectedPelangganId && lat && lng) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    }

    // Open add location modal
    function openAddLocationModal() {
        document.getElementById('add-location-modal').classList.remove('hidden');
        document.getElementById('add-location-modal').classList.add('flex');

        // Initialize location picker map if not already initialized
        if (!locationPickerMap) {
            setTimeout(initLocationPickerMap, 100);
        } else {
            // ensure map redraws whenever modal reopened
            setTimeout(() => {
                locationPickerMap.invalidateSize();
            }, 150);
        }
    }

    // Close add location modal
    function closeAddLocationModal() {
        document.getElementById('add-location-modal').classList.add('hidden');
        document.getElementById('add-location-modal').classList.remove('flex');
        clearSelectedPelanggan();
    }

    // Save pelanggan location
    async function savePelangganLocation() {
        if (!selectedPelangganId) return;

        const lat = document.getElementById('modal-latitude').value;
        const lng = document.getElementById('modal-longitude').value;
        const odpId = document.getElementById('modal-odp-id').value;

        try {
            const response = await fetch(`/mapping/pelanggans/${selectedPelangganId}/location`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: parseFloat(lat),
                    longitude: parseFloat(lng),
                    odp_id: odpId ? parseInt(odpId) : null
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Koordinat pelanggan berhasil disimpan!');
                closeAddLocationModal();
                location.reload();
            } else {
                alert('Gagal menyimpan koordinat: ' + (data.message || 'Terjadi kesalahan'));
            }
        } catch (error) {
            console.error('Error saving location:', error);
            alert('Terjadi kesalahan saat menyimpan koordinat');
        }
    }

    // Watch for coordinate changes
    document.getElementById('modal-latitude')?.addEventListener('input', updateSaveButton);
    document.getElementById('modal-longitude')?.addEventListener('input', updateSaveButton);

    // Initialize main map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initMainMap();
    });
</script>
@endpush

