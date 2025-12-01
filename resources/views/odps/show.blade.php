@extends('layouts.app')

@section('title', 'Detail ODP - WiFi Billing Management')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #odp-map {
        height: 400px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
    }
    .leaflet-container {
        z-index: 1;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">{{ $odp->kode_odp }} - {{ $odp->nama }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Detail ODP dan pelanggan yang terhubung</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <span class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold {{ $odp->status === 'aktif' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' }} rounded-xl">
                <i class="fas fa-circle mr-2 text-[8px]"></i>{{ ucfirst($odp->status) }}
            </span>
            @can('edit-pelanggan')
            <a href="{{ route('odps.edit', $odp) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-edit mr-2 text-xs sm:text-sm"></i>Edit
            </a>
            @endcan
            <a href="{{ route('odps.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <!-- Informasi ODP -->
            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Informasi ODP</p>
                    <h2 class="text-base font-semibold text-gray-900">Detail & status</h2>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kode ODP</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $odp->kode_odp }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $odp->nama }}</dd>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-1">Kapasitas</dt>
                        <dd class="text-lg font-bold text-blue-900">{{ $odp->port_terpakai }}/{{ $odp->kapasitas }} Port</dd>
                    </div>
                    <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-1">Pelanggan Terhubung</dt>
                        <dd class="text-lg font-bold text-green-900">{{ $odp->pelanggans->count() }} pelanggan</dd>
                    </div>
                    <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-indigo-700 uppercase tracking-wide mb-1">Terhubung ke ODC</dt>
                        <dd class="text-sm text-gray-900">
                            @if($odp->odc)
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <p class="font-semibold">{{ $odp->odc->kode_odc }}</p>
                                        <p class="text-xs text-gray-600">{{ $odp->odc->nama }}</p>
                                    </div>
                                    <a href="{{ route('odcs.show', $odp->odc) }}" class="text-indigo-600 hover:underline text-xs font-semibold">
                                        <i class="fas fa-eye mr-1"></i>Detail ODC
                                    </a>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    <i class="fas fa-exclamation-circle mr-1 text-[10px]"></i>Belum terhubung ke ODC
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
                @if($odp->alamat)
                <div class="bg-white border border-gray-100 rounded-xl px-4 py-3">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Alamat</dt>
                    <dd class="text-sm text-gray-700 leading-relaxed">{{ $odp->alamat }}</dd>
                </div>
                @endif
                <div class="bg-white border border-gray-100 rounded-xl px-4 py-3">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Koordinat</dt>
                    <dd class="text-sm text-gray-700">
                        <div class="flex items-center gap-4">
                            <span><i class="fas fa-latitude mr-1"></i>{{ number_format($odp->latitude, 8) }}</span>
                            <span><i class="fas fa-longitude mr-1"></i>{{ number_format($odp->longitude, 8) }}</span>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $odp->latitude }},{{ $odp->longitude }}"
                               target="_blank"
                               class="ml-auto inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                <i class="fas fa-external-link-alt mr-1"></i>Buka di Google Maps
                            </a>
                        </div>
                    </dd>
                </div>
            </div>

            <!-- Map -->
            <div class="app-card">
                <div>
                    <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold mb-2">Peta Lokasi</p>
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Lokasi ODP dan pelanggan terhubung</h2>
                </div>
                <div id="odp-map"></div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <div class="app-card space-y-4">
                <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Statistik</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-plug text-blue-500"></i>Port Terpakai</span>
                        <span class="text-lg font-bold text-gray-900">{{ $odp->port_terpakai }}/{{ $odp->kapasitas }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-users text-green-500"></i>Total Pelanggan</span>
                        <span class="text-lg font-bold text-gray-900">{{ $odp->pelanggans->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-user-check text-emerald-500"></i>Pelanggan Aktif</span>
                        <span class="text-lg font-bold text-gray-900">{{ $odp->pelanggans->where('status', 'aktif')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-plug text-purple-500"></i>Port Tersedia</span>
                        <span class="text-lg font-bold text-gray-900">{{ max(0, $odp->kapasitas - $odp->port_terpakai) }}</span>
                    </div>
                </div>
            </div>

            @if($odp->foto)
            <div class="app-card mt-6">
                <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold mb-3">Foto ODP</p>
                <img src="{{ asset('storage/' . $odp->foto) }}" alt="Foto ODP" class="w-full h-auto rounded-xl border border-gray-200">
            </div>
            @endif
        </div>
    </div>

    <!-- Pelanggan Terhubung -->
    <div class="app-card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Pelanggan Terhubung</p>
                <h2 class="text-base font-semibold text-gray-900">Daftar pelanggan yang menggunakan ODP ini</h2>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                {{ $pelanggans->total() }} pelanggan
            </span>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-purple-500 to-purple-600">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">PPPoE</th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Paket</th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Penagih</th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-5 py-3 text-center text-[11px] font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pelanggans as $pelanggan)
                    <tr class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-sm font-semibold text-gray-900">{{ $pelanggan->nama }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-600">{{ $pelanggan->pppoe }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-600">{{ $pelanggan->paket->nama_paket ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-600">{{ $pelanggan->penagih->nama ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-semibold {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                {{ ucfirst($pelanggan->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('pelanggans.show', $pelanggan) }}"
                               class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                            <i class="fas fa-users text-4xl mb-3"></i>
                            <p class="text-sm font-semibold">Tidak ada pelanggan terhubung</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden space-y-3">
            @forelse($pelanggans as $pelanggan)
            <div class="mobile-card">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 mb-1">{{ $pelanggan->nama }}</p>
                        <p class="text-xs text-gray-500 mb-2">{{ $pelanggan->pppoe }}</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="text-xs text-gray-600">{{ $pelanggan->paket->nama_paket ?? '-' }}</span>
                            <span class="text-xs text-gray-600">•</span>
                            <span class="text-xs text-gray-600">{{ $pelanggan->penagih->nama ?? '-' }}</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-semibold mt-2 {{ $pelanggan->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($pelanggan->status) }}
                        </span>
                    </div>
                    <a href="{{ route('pelanggans.show', $pelanggan) }}"
                       class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-gray-400">
                <i class="fas fa-users text-4xl mb-3"></i>
                <p class="text-sm font-semibold">Tidak ada pelanggan terhubung</p>
            </div>
            @endforelse
        </div>

        @if($pelanggans->hasPages())
        <div class="mt-4 pt-4 border-t border-gray-100">
            {{ $pelanggans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map;
    let currentTileLayer = null;
    let satelliteMode = false;
    const odpLat = {{ $odp->latitude }};
    const odpLng = {{ $odp->longitude }};

    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri',
        maxZoom: 19
    });

    // Initialize map
    function initMap() {
        map = L.map('odp-map').setView([odpLat, odpLng], 15);

        // Add default tile layer
        currentTileLayer = osmLayer;
        currentTileLayer.addTo(map);

        // Add satellite toggle checkbox
        const satelliteControl = L.control({ position: 'topright' });
        satelliteControl.onAdd = function() {
            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.style.background = 'transparent';
            div.style.border = 'none';
            div.style.boxShadow = 'none';
            div.innerHTML = '<label class="flex items-center gap-2 bg-white px-3 py-2 rounded shadow text-xs font-semibold cursor-pointer hover:bg-gray-50 transition" style="margin: 0; user-select: none;"><input type="checkbox" id="toggle-satellite-odp" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" style="margin: 0; cursor: pointer;"><span style="cursor: pointer;">🛰️ Satelit</span></label>';
            L.DomEvent.disableClickPropagation(div);
            return div;
        };
        satelliteControl.addTo(map);

        const checkbox = document.getElementById('toggle-satellite-odp');
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

        // Add ODP marker
        const odpIcon = L.divIcon({
            className: 'odp-marker',
            html: '<div style="background: #9333ea; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const odpMarker = L.marker([odpLat, odpLng], { icon: odpIcon }).addTo(map);
        odpMarker.bindPopup(`
            <div class="text-sm">
                <h3 class="font-bold text-purple-600 mb-1">${@json($odp->kode_odp)}</h3>
                <p class="text-gray-700">${@json($odp->nama)}</p>
                <a href="https://www.google.com/maps/search/?api=1&query=${odpLat},${odpLng}" target="_blank" class="text-blue-600 hover:underline text-xs mt-2 inline-block">
                    <i class="fas fa-external-link-alt mr-1"></i>Buka di Google Maps
                </a>
            </div>
        `);

        // Add pelanggan markers
        @foreach($odp->pelanggans->whereNotNull('latitude')->whereNotNull('longitude') as $pelanggan)
        const pelangganIcon{{ $pelanggan->id }} = L.divIcon({
            className: 'pelanggan-marker',
            html: '<div style="background: #10b981; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        L.marker([{{ $pelanggan->latitude }}, {{ $pelanggan->longitude }}], { icon: pelangganIcon{{ $pelanggan->id }} })
            .addTo(map)
            .bindPopup(`
                <div class="text-sm">
                    <h3 class="font-bold text-green-600 mb-1">${@json($pelanggan->nama)}</h3>
                    <p class="text-gray-600 text-xs">${@json($pelanggan->pppoe)}</p>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $pelanggan->latitude }},{{ $pelanggan->longitude }}" target="_blank" class="text-blue-600 hover:underline text-xs mt-2 inline-block">
                        <i class="fas fa-external-link-alt mr-1"></i>Buka di Google Maps
                    </a>
                </div>
            `);
        @endforeach
    }

    document.addEventListener('DOMContentLoaded', initMap);
</script>
@endpush

