@extends('layouts.app')

@section('title', 'Detail ODC - WiFi Billing Management')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #odc-map {
        height: 380px;
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
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">{{ $odc->kode_odc }} - {{ $odc->nama }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Detail ODC dan daftar ODP yang tersambung</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            @php
                $statusClass = match($odc->status) {
                    'aktif' => 'bg-green-50 text-green-600 border border-green-100',
                    'penuh' => 'bg-yellow-50 text-yellow-600 border border-yellow-100',
                    'rusak' => 'bg-red-50 text-red-600 border border-red-100',
                    default => 'bg-gray-50 text-gray-600 border border-gray-100',
                };
            @endphp
            <span class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold {{ $statusClass }} rounded-xl">
                <i class="fas fa-circle mr-2 text-[8px]"></i>{{ ucfirst($odc->status) }}
            </span>
            @can('manage-odp')
            <a href="{{ route('odcs.edit', $odc) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-edit mr-2 text-xs sm:text-sm"></i>Edit
            </a>
            @endcan
            <a href="{{ route('odcs.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <!-- Informasi ODC -->
            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Informasi ODC</p>
                    <h2 class="text-base font-semibold text-gray-900">Detail & status</h2>
                </div>
                @php
                    $usedPorts = $usedPorts ?? 0;
                    $capacity = max(0, $odc->kapasitas_port);
                    $usagePercent = $capacity > 0 ? min(100, ($usedPorts / $capacity) * 100) : 0;
                    $computedStatus = $capacity > 0 && $usedPorts >= $capacity ? 'penuh' : $odc->status;
                @endphp
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Kode ODC</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $odc->kode_odc }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $odc->nama }}</dd>
                    </div>
                    <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 space-y-1">
                        <dt class="text-xs font-semibold text-indigo-700 uppercase tracking-wide mb-1">Port Terpakai</dt>
                        <dd class="text-lg font-bold text-indigo-900">{{ $usedPorts }}/{{ $capacity }} Port</dd>
                        <div class="w-full bg-indigo-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-1.5 rounded-full @if($usagePercent >= 100) bg-red-500 @elseif($usagePercent >= 80) bg-amber-500 @else bg-indigo-500 @endif" style="width: {{ $usagePercent }}%"></div>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-blue-700 uppercase tracking-wide mb-1">Jumlah ODP Tersambung</dt>
                        <dd class="text-lg font-bold text-blue-900">{{ $odc->odps->count() }} ODP</dd>
                    </div>
                </dl>
                @if($odc->alamat)
                <div class="bg-white border border-gray-100 rounded-xl px-4 py-3">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Alamat</dt>
                    <dd class="text-sm text-gray-700 leading-relaxed">{{ $odc->alamat }}</dd>
                </div>
                @endif
                @if($odc->keterangan)
                <div class="bg-white border border-gray-100 rounded-xl px-4 py-3">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Keterangan</dt>
                    <dd class="text-sm text-gray-700 leading-relaxed">{{ $odc->keterangan }}</dd>
                </div>
                @endif
                @if($odc->latitude && $odc->longitude)
                <div class="bg-white border border-gray-100 rounded-xl px-4 py-3">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Koordinat</dt>
                    <dd class="text-sm text-gray-700">
                        <div class="flex items-center gap-4">
                            <span><i class="fas fa-latitude mr-1"></i>{{ number_format($odc->latitude, 8) }}</span>
                            <span><i class="fas fa-longitude mr-1"></i>{{ number_format($odc->longitude, 8) }}</span>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $odc->latitude }},{{ $odc->longitude }}"
                               target="_blank"
                               class="ml-auto inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                <i class="fas fa-external-link-alt mr-1"></i>Buka di Google Maps
                            </a>
                        </div>
                    </dd>
                </div>
                @endif
            </div>

            @if($odc->latitude && $odc->longitude)
            <!-- Map -->
            <div class="app-card">
                <div>
                    <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold mb-2">Peta Lokasi</p>
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Lokasi ODC dan ODP yang tersambung</h2>
                </div>
                <div id="odc-map"></div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <div class="app-card space-y-4">
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Ringkasan</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-plug text-indigo-500"></i>Port Terpakai</span>
                        <span class="text-lg font-bold text-gray-900">{{ $usedPorts }}/{{ $capacity }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center gap-2"><i class="fas fa-project-diagram text-blue-500"></i>Jumlah ODP</span>
                        <span class="text-lg font-bold text-gray-900">{{ $odc->odps->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ODP Terhubung -->
    <div class="app-card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">ODP Tersambung</p>
                <h2 class="text-base font-semibold text-gray-900">Daftar ODP yang terhubung ke ODC ini</h2>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                {{ $odc->odps->count() }} ODP
            </span>
        </div>

        @if($odc->odps->isEmpty())
            <div class="py-10 text-center text-gray-400">
                <i class="fas fa-map-marker-alt text-4xl mb-3"></i>
                <p class="text-sm font-semibold">Belum ada ODP yang terhubung ke ODC ini</p>
            </div>
        @else
        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Kode ODP</th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Kapasitas</th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Pelanggan</th>
                        <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-5 py-3 text-center text-[11px] font-bold text-white uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($odc->odps as $odp)
                    <tr class="hover:bg-indigo-50 transition">
                        <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            {{ $odp->kode_odp }}
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $odp->nama }}</p>
                            @if($odp->alamat)
                            <p class="text-xs text-gray-500 mt-1 truncate">{{ Str::limit($odp->alamat, 50) }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $odp->port_terpakai }}/{{ $odp->kapasitas }} port
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $odp->pelanggans->count() }} pelanggan
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-xl text-[11px] font-semibold {{ $odp->status === 'aktif' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                <i class="fas fa-circle mr-1 text-[9px]"></i>{{ ucfirst($odp->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <a href="{{ route('odps.show', $odp) }}"
                               class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="lg:hidden space-y-3">
            @foreach($odc->odps as $odp)
            <div class="mobile-card">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 mb-1">{{ $odp->kode_odp }} - {{ $odp->nama }}</p>
                        <p class="text-xs text-gray-500 mb-2">{{ $odp->pelanggans->count() }} pelanggan</p>
                        <div class="flex flex-wrap gap-2 text-[11px] text-gray-600">
                            <span>Kapasitas: {{ $odp->port_terpakai }}/{{ $odp->kapasitas }} port</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-semibold mt-2 {{ $odp->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($odp->status) }}
                        </span>
                    </div>
                    <a href="{{ route('odps.show', $odp) }}"
                       class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($odc->latitude && $odc->longitude)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const odcLat = {{ $odc->latitude }};
    const odcLng = {{ $odc->longitude }};

    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '© Esri',
        maxZoom: 19
    });

    let map;
    let currentTileLayer = null;
    let satelliteMode = false;

    function initOdcMap() {
        map = L.map('odc-map').setView([odcLat, odcLng], 14);

        currentTileLayer = osmLayer;
        currentTileLayer.addTo(map);

        // Satellite toggle
        const satelliteControl = L.control({ position: 'topright' });
        satelliteControl.onAdd = function() {
            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.style.background = 'transparent';
            div.style.border = 'none';
            div.style.boxShadow = 'none';
            div.innerHTML = '<label class=\"flex items-center gap-2 bg-white px-3 py-2 rounded shadow text-xs font-semibold cursor-pointer hover:bg-gray-50 transition\" style=\"margin: 0; user-select: none;\"><input type=\"checkbox\" id=\"toggle-satellite-odc\" class=\"rounded border-gray-300 text-blue-600 focus:ring-blue-500\" style=\"margin: 0; cursor: pointer;\"><span style=\"cursor: pointer;\">🛰️ Satelit</span></label>';
            L.DomEvent.disableClickPropagation(div);
            return div;
        };
        satelliteControl.addTo(map);

        const checkbox = document.getElementById('toggle-satellite-odc');
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

        const odcIcon = L.divIcon({
            className: 'odc-marker',
            html: '<div style=\"background: #4f46e5; width: 32px; height: 32px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);\"></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        const odcMarker = L.marker([odcLat, odcLng], { icon: odcIcon }).addTo(map);
        odcMarker.bindPopup(`
            <div class="text-sm">
                <h3 class="font-bold text-indigo-600 mb-1">{{ $odc->kode_odc }}</h3>
                <p class="text-gray-700">{{ $odc->nama }}</p>
            </div>
        `);

        @foreach($odc->odps->whereNotNull('latitude')->whereNotNull('longitude') as $odp)
        const odpIcon{{ $odp->id }} = L.divIcon({
            className: 'odp-marker',
            html: '<div style=\"background: #0ea5e9; width: 22px; height: 22px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);\"></div>',
            iconSize: [22, 22],
            iconAnchor: [11, 11]
        });

        L.marker([{{ $odp->latitude }}, {{ $odp->longitude }}], { icon: odpIcon{{ $odp->id }} })
            .addTo(map)
            .bindPopup(`
                <div class="text-sm">
                    <h3 class="font-bold text-sky-600 mb-1">{{ $odp->kode_odp }}</h3>
                    <p class="text-gray-700 text-xs">{{ $odp->nama }}</p>
                    <a href="{{ route('odps.show', $odp) }}" class="text-indigo-600 hover:underline text-xs mt-2 inline-block">
                        <i class="fas fa-eye mr-1"></i>Lihat detail ODP
                    </a>
                </div>
            `);
        @endforeach
    }

    document.addEventListener('DOMContentLoaded', initOdcMap);
</script>
@endif
@endpush


