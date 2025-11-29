@extends('layouts.app')

@section('title', 'Detail ONU')

@section('content')
<div x-data="{
    trafficChart: null,
    currentDownload: 0,
    currentUpload: 0,
    loadingTraffic: false,
    showAddServiceModal: false,
    showEditServiceModal: false,
    showRemoteAccessModal: false,
    editingServiceId: null,
    showInfoModal: false,
    pelangganOdpMap: @js($pelangganOdpMap),
    handlePelangganChange(event) {
        const pelangganId = event.target.value;
        const odpId = this.pelangganOdpMap[pelangganId] ?? '';
        if (this.$refs.infoOdpSelect) {
            this.$refs.infoOdpSelect.value = odpId;
        }
    },
    init() {
        this.loadTraffic();
        setInterval(() => this.loadTraffic(), 30000); // Refresh every 30 seconds
    },
    editService(serviceId) {
        this.editingServiceId = serviceId;
        this.showEditServiceModal = true;
    },
    async loadTraffic() {
        this.loadingTraffic = true;
        try {
            const response = await fetch('/api/onus/{{ $onu->id }}/traffic', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            const result = await response.json();
            
            if (result.success && result.data) {
                this.currentDownload = result.data.current.download_mbps || 0;
                this.currentUpload = result.data.current.upload_mbps || 0;
                this.updateChart(result.data.history || []);
            }
        } catch (error) {
            console.error('Error loading traffic:', error);
        } finally {
            this.loadingTraffic = false;
        }
    },
    refreshTraffic() {
        this.loadTraffic();
    },
    updateChart(history) {
        const ctx = this.$refs.trafficChart?.getContext('2d');
        if (!ctx) return;

        const labels = history.map(h => {
            const date = new Date(h.timestamp);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        });
        
        const downloadData = history.map(h => (h.download / 1000).toFixed(2));
        const uploadData = history.map(h => (h.upload / 1000).toFixed(2));

        if (this.trafficChart) {
            this.trafficChart.destroy();
        }

        this.trafficChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Download (Mbps)',
                        data: downloadData,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'Upload (Mbps)',
                        data: uploadData,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Mbps'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Waktu'
                        }
                    }
                }
            }
        });
    }
}">
@php
$infoItems = [
    ['label' => 'OLT', 'value' => $onu->olt?->nama ?? '-'],
    ['label' => 'GPON Port', 'value' => $onu->card && $onu->port ? "{$onu->card}/{$onu->port}" : '-'],
    ['label' => 'Serial Number', 'value' => $onu->serial_number],
    ['label' => 'ONU Type', 'value' => $onu->ont_type ?? '-'],
    ['label' => 'Vendor/Model', 'value' => trim(($onu->vendor ?? '') . ' ' . ($onu->model ?? '')) ?: '-'],
];
$pelangganSearchOptionsModal = $pelanggans->map(function ($pelanggan) {
    return [
        'value' => (string) $pelanggan->id,
        'label' => $pelanggan->nama,
        'subtitle' => $pelanggan->pppoe ?? '-',
        'meta' => $pelanggan->no_hp ?? '',
        'odp_id' => $pelanggan->odp_id,
    ];
})->values();
@endphp

<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-boxes"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">{{ $onu->serial_number }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Terhubung ke {{ $onu->olt?->nama ?? '-' }} · Card {{ $onu->card ?? '-' }}/Port {{ $onu->port ?? '-' }}</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-wrap gap-2">
            <button type="button" x-data x-on:click="$dispatch('open-guide-onu-guide')" class="btn-secondary"><i class="fas fa-book mr-2"></i>Panduan</button>
            @can('reboot-onu')
            <form action="{{ route('onus.reboot', $onu) }}" method="POST"
                class="onu-action-form"
                data-confirm-title="Reboot ONU"
                data-confirm-text="Kirim perintah reboot ke {{ $onu->serial_number }}?"
                data-confirm-icon="warning"
                data-confirm-button="Ya, Reboot">
                @csrf
                <button class="btn-secondary"><i class="fas fa-power-off mr-2"></i>Reboot</button>
            </form>
            <form action="{{ route('onus.reset', $onu) }}" method="POST"
                class="onu-action-form"
                data-confirm-title="Reset ONU"
                data-confirm-text="Reset konfigurasi ONU {{ $onu->serial_number }}?"
                data-confirm-icon="warning"
                data-confirm-button="Ya, Reset">
                @csrf
                <button class="btn-secondary"><i class="fas fa-undo mr-2"></i>Reset</button>
            </form>
            <form action="{{ route('onus.disable', $onu) }}" method="POST"
                class="onu-action-form"
                data-confirm-title="Disable ONU"
                data-confirm-text="Disable koneksi ONU {{ $onu->serial_number }}?"
                data-confirm-icon="warning"
                data-confirm-button="Ya, Disable">
                @csrf
                <button class="btn-secondary"><i class="fas fa-ban mr-2"></i>Disable</button>
            </form>
            <form action="{{ route('onus.enable', $onu) }}" method="POST"
                class="onu-action-form"
                data-confirm-title="Enable ONU"
                data-confirm-text="Aktifkan kembali ONU {{ $onu->serial_number }}?"
                data-confirm-icon="info"
                data-confirm-button="Ya, Enable">
                @csrf
                <button class="btn-secondary"><i class="fas fa-check mr-2"></i>Enable</button>
            </form>
            @endcan
            <a href="{{ route('onus.index') }}" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="space-y-6 xl:col-span-2">
            <div class="app-card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="section-title">Informasi ONU</h2>
                    @can('manage-onu')
                    <button type="button" class="btn-secondary px-3 py-1.5 text-xs" @click="showInfoModal = true">
                        <i class="fas fa-pen mr-1"></i>Edit Info
                    </button>
                    @endcan
                </div>
                <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                    @foreach($infoItems as $item)
                    <div class="flex-1 min-w-[120px]">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">{{ $item['label'] }}</p>
                        <p class="font-semibold text-gray-900">{{ $item['value'] }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs text-gray-500">
                    <div>
                        <p class="uppercase tracking-wide">Status</p>
                        <p><span class="badge {{ $onu->status === 'online' ? 'badge-success' : ($onu->status === 'offline' ? 'badge-muted' : 'badge-danger') }}">{{ ucfirst($onu->status) }}</span></p>
                    </div>
                    <div>
                        <p class="uppercase tracking-wide">Online Duration</p>
                        <p class="font-semibold text-gray-900">{{ gmdate('H\h i\m', $onu->duration_online) }}</p>
                    </div>
                    <div>
                        <p class="uppercase tracking-wide">Olt / ONU RX</p>
                        <p>{{ $onu->olt_rx_power ?? '-' }} / {{ $onu->rx_power ?? '-' }} dBm</p>
                    </div>
                    <div>
                        <p class="uppercase tracking-wide">Last Event</p>
                        <p>{{ optional($onu->last_online_at)->diffForHumans() ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="app-card space-y-6" x-data="{ activeTab: 'service' }">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="section-title">Koneksi & Service</h2>
                        <p class="text-xs text-gray-500">Kelola layanan PPPoE, VLAN, dan profil bandwidth.</p>
                    </div>
                    <div class="flex border border-gray-200 rounded-xl overflow-hidden text-xs font-semibold">
                        <button type="button" class="px-4 py-2" :class="{ 'bg-blue-600 text-white': activeTab === 'service', 'text-gray-500': activeTab !== 'service' }" @click="activeTab = 'service'">Service</button>
                        <button type="button" class="px-4 py-2 border-l border-gray-200" :class="{ 'bg-blue-600 text-white': activeTab === 'wifi', 'text-gray-500': activeTab !== 'wifi' }" @click="activeTab = 'wifi'">WiFi & LAN</button>
                        <button type="button" class="px-4 py-2 border-l border-gray-200" :class="{ 'bg-blue-600 text-white': activeTab === 'remote', 'text-gray-500': activeTab !== 'remote' }" @click="activeTab = 'remote'">Remote Access</button>
                        <button type="button" class="px-4 py-2 border-l border-gray-200" :class="{ 'bg-blue-600 text-white': activeTab === 'veip', 'text-gray-500': activeTab !== 'veip' }" @click="activeTab = 'veip'">VEIP</button>
                        <button type="button" class="px-4 py-2 border-l border-gray-200" :class="{ 'bg-blue-600 text-white': activeTab === 'tr069', 'text-gray-500': activeTab !== 'tr069' }" @click="activeTab = 'tr069'">TR-069</button>
                    </div>
                </div>

                <div x-show="activeTab === 'service'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-gray-500">Total: {{ $onu->services->count() }} service</p>
                        @can('manage-onu')
                        @if(!empty($availableServiceIds))
                        <button @click="showAddServiceModal = true" class="text-xs text-blue-600 hover:text-blue-700 font-semibold">
                            <i class="fas fa-plus mr-1"></i>Tambah Service
                        </button>
                        @endif
                        @endcan
                    </div>
                    @forelse($onu->services as $service)
                    <div class="border border-gray-100 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Service {{ $service->service_id }}</h3>
                                <p class="text-xs text-gray-500">{{ strtoupper($service->wan_mode) }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($service->is_active)
                                <span class="badge badge-success text-[10px]">Aktif</span>
                                @else
                                <span class="badge badge-muted text-[10px]">Nonaktif</span>
                                @endif
                                @can('manage-onu')
                                <button @click="editService({{ $service->id }})" class="text-xs text-blue-600 hover:underline">
                                    <i class="fas fa-pen mr-1"></i>Edit
                                </button>
                                <form action="{{ route('onus.services.destroy', [$onu, $service]) }}" method="POST" onsubmit="return confirm('Yakin hapus service ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">
                                        <i class="fas fa-trash mr-1"></i>Hapus
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 text-xs text-gray-600">
                            <div><p class="uppercase text-gray-400">VLAN</p><p class="font-semibold text-gray-900">{{ $service->vlan_id ?? '-' }}</p></div>
                            <div><p class="uppercase text-gray-400">Download</p><p>{{ $service->download_speed ? number_format($service->download_speed/1000, 1) . ' Mbps' : '-' }}</p></div>
                            <div><p class="uppercase text-gray-400">Upload</p><p>{{ $service->upload_speed ? number_format($service->upload_speed/1000, 1) . ' Mbps' : '-' }}</p></div>
                            <div><p class="uppercase text-gray-400">PPPoE</p><p>{{ $service->pppoe_username ?? '-' }}</p></div>
                        </div>
                        @if($service->wifi_config)
                        <div class="mt-4 border border-blue-100 rounded-xl p-3 bg-blue-50/40 text-xs text-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-blue-700">WiFi</span>
                                <span class="badge {{ data_get($service->wifi_config, 'enabled', true) ? 'badge-success' : 'badge-muted' }}">
                                    {{ data_get($service->wifi_config, 'enabled', true) ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mt-2">
                                <p>SSID: <span class="font-semibold">{{ data_get($service->wifi_config, 'ssid', '-') }}</span></p>
                                <p>Band: {{ data_get($service->wifi_config, 'band', '-') }}</p>
                                <p>Security: {{ data_get($service->wifi_config, 'security', '-') }}</p>
                                <p>Channel: {{ data_get($service->wifi_config, 'channel', '-') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($service->lan_port_config)
                        <div class="mt-4 border border-gray-100 rounded-xl p-3 text-xs text-gray-700">
                            <p class="font-semibold text-gray-900 mb-2">LAN Ports</p>
                            <div class="grid gap-2 md:grid-cols-2">
                                @foreach($service->lan_port_config as $port => $config)
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg px-2 py-1.5">
                                    <div>
                                        <p class="text-[11px] uppercase text-gray-400">{{ strtoupper($port) }}</p>
                                        <p class="font-semibold text-gray-800">{{ ucfirst($config['mode'] ?? '-') }}</p>
                                    </div>
                                    <div class="text-[11px] text-gray-500 text-right">
                                        VLAN: {{ $config['vlan'] ?? '-' }}<br>
                                        {{ $config['description'] ?? '' }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="border border-gray-100 rounded-xl p-8 text-center">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p class="text-sm text-gray-400">Belum ada service.</p>
                        @can('manage-onu')
                        @if(!empty($availableServiceIds))
                        <button @click="showAddServiceModal = true" class="mt-3 text-sm text-blue-600 hover:text-blue-700 font-semibold">
                            <i class="fas fa-plus mr-1"></i>Tambah Service Pertama
                        </button>
                        @endif
                        @endcan
                    </div>
                    @endforelse
                </div>

                <div x-show="activeTab === 'wifi'" class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">WiFi & Ethernet Summary</h3>
                            <p class="text-xs text-gray-500">Menampilkan konfigurasi WiFi dan port LAN per service.</p>
                        </div>
                    </div>
                    @forelse($onu->services as $service)
                    @php
                        $wifiConfig = $service->wifi_config ?? null;
                        $lanConfig = $service->lan_port_config ?? null;
                    @endphp
                    <div class="border border-gray-100 rounded-xl p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-400 uppercase">Service {{ $service->service_id }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ strtoupper($service->wan_mode) }}</p>
                            </div>
                            @can('manage-onu')
                            <button @click="editService({{ $service->id }}); activeTab = 'service';" class="text-xs text-blue-600 hover:underline">
                                <i class="fas fa-pen mr-1"></i>Edit Service
                            </button>
                            @endcan
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="border border-blue-100 rounded-xl p-3 bg-blue-50/40 text-xs text-gray-700">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-blue-700">WiFi</span>
                                    <span class="badge {{ data_get($wifiConfig, 'enabled', true) ? 'badge-success' : 'badge-muted' }}">
                                        {{ data_get($wifiConfig, 'enabled', true) ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                                @if($wifiConfig)
                                <div class="grid grid-cols-2 gap-3 mt-2">
                                    <p>SSID: <span class="font-semibold">{{ data_get($wifiConfig, 'ssid', '-') }}</span></p>
                                    <p>Security: {{ data_get($wifiConfig, 'security', '-') }}</p>
                                    <p>Band: {{ data_get($wifiConfig, 'band', '-') }}</p>
                                    <p>Channel: {{ data_get($wifiConfig, 'channel', '-') }}</p>
                                </div>
                                @else
                                <p class="text-gray-400 mt-2">Belum dikonfigurasi.</p>
                                @endif
                            </div>
                            <div class="border border-gray-100 rounded-xl p-3 text-xs text-gray-700">
                                <p class="font-semibold text-gray-900 mb-2">LAN Ports</p>
                                @if($lanConfig)
                                <div class="grid gap-2 md:grid-cols-2">
                                    @foreach($lanConfig as $port => $config)
                                    <div class="flex items-center justify-between bg-gray-50 rounded-lg px-2 py-1.5">
                                        <div>
                                            <p class="text-[11px] uppercase text-gray-400">{{ strtoupper($port) }}</p>
                                            <p class="font-semibold text-gray-800">{{ ucfirst($config['mode'] ?? '-') }}</p>
                                        </div>
                                        <div class="text-[11px] text-gray-500 text-right">
                                            VLAN: {{ $config['vlan'] ?? '-' }}<br>
                                            {{ $config['description'] ?? '' }}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-gray-400">Belum ada mapping port LAN.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="border border-gray-100 rounded-xl p-6 text-center text-sm text-gray-400">
                        Belum ada service yang dikonfigurasi. Tambahkan service terlebih dahulu.
                    </div>
                    @endforelse
                </div>

                <div x-show="activeTab === 'remote'" class="space-y-3">
                    @php
                        $primaryService = $onu->services->first();
                        $remoteRules = $primaryService?->remote_access_rules ?? [];
                    @endphp
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Remote Access Rules</h3>
                            <p class="text-xs text-gray-500">Untuk Service {{ $primaryService?->service_id ?? 1 }}</p>
                        </div>
                        @can('manage-onu')
                        @if($primaryService)
                        <button @click="showRemoteAccessModal = true" class="text-xs text-blue-600 hover:underline">
                            <i class="fas fa-plus mr-1"></i>Tambah Rule
                        </button>
                        @else
                        <p class="text-xs text-gray-400">Buat service terlebih dahulu</p>
                        @endif
                        @endcan
                    </div>
                    @if($primaryService)
                        @forelse($remoteRules as $index => $rule)
                        <div class="border border-gray-100 rounded-xl p-4">
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $rule['name'] ?? "Rule " . ($index + 1) }}</p>
                                    <p class="text-xs text-gray-500">Ingress: {{ $rule['ingress'] ?? 'WAN' }} · Service: {{ $rule['service'] ?? 'web' }}</p>
                                    @if(isset($rule['source_ip']) || isset($rule['destination_ip']))
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $rule['source_ip'] ?? '0.0.0.0' }} → {{ $rule['destination_ip'] ?? '0.0.0.0' }}
                                    </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge {{ ($rule['action'] ?? 'allow') === 'allow' ? 'badge-success' : 'badge-danger' }}">
                                        {{ ucfirst($rule['action'] ?? 'allow') }}
                                    </span>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only" {{ ($rule['enabled'] ?? true) ? 'checked' : '' }} disabled>
                                        <span class="w-9 h-5 {{ ($rule['enabled'] ?? true) ? 'bg-blue-200' : 'bg-gray-200' }} rounded-full relative">
                                            <span class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition {{ ($rule['enabled'] ?? true) ? 'translate-x-4' : '' }}"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="border border-gray-100 rounded-xl p-8 text-center">
                            <i class="fas fa-shield-alt text-4xl text-gray-300 mb-3"></i>
                            <p class="text-sm text-gray-400">Belum ada remote access rule.</p>
                        </div>
                        @endforelse
                    @else
                    <div class="border border-gray-100 rounded-xl p-8 text-center">
                        <i class="fas fa-exclamation-circle text-4xl text-gray-300 mb-3"></i>
                        <p class="text-sm text-gray-400">Buat service terlebih dahulu untuk mengatur remote access.</p>
                    </div>
                    @endif
                </div>

                <div x-show="activeTab === 'veip'" class="space-y-3">
                    <div class="border border-gray-100 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">VEIP 1</p>
                                <p class="text-xs text-gray-500">Mode: {{ $onu->veip_mode ?? 'N/A' }}</p>
                            </div>
                            <span class="badge badge-muted">{{ $onu->veip_status ?? 'Down' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs text-gray-500 mt-3">
                            <p>Access VLAN: {{ $onu->veip_access_vlan ?? '-' }}</p>
                            <p>Trunk VLANs: {{ $onu->veip_trunk_vlans ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'tr069'" class="space-y-3">
                    <div class="border border-gray-100 rounded-xl p-4">
                        <p class="text-sm font-semibold text-gray-900">TR-069 Settings</p>
                        <div class="grid grid-cols-2 gap-3 text-xs text-gray-500 mt-3">
                            <p>ACS URL: {{ $onu->tr069_acs_url ?? 'N/A' }}</p>
                            <p>Username: {{ $onu->tr069_username ?? 'N/A' }}</p>
                            <p>Password: {{ $onu->tr069_password ?? 'N/A' }}</p>
                            <p>VLAN: {{ $onu->tr069_vlan ?? 'untagged' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Traffic Monitoring -->
            <div class="app-card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="section-title">Traffic Monitoring</h2>
                        <p class="text-xs text-gray-500">Upload & Download Bandwidth</p>
                    </div>
                    <button type="button" @click="refreshTraffic()" class="text-xs text-blue-600 hover:text-blue-700">
                        <i class="fas fa-sync" :class="{ 'fa-spin': loadingTraffic }"></i> Refresh
                    </button>
                </div>
                
                <!-- Current Speed -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <p class="text-xs text-blue-600 uppercase tracking-wide mb-1">Download</p>
                        <p class="text-2xl font-bold text-blue-700" x-text="currentDownload + ' Mbps'">0 Mbps</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <p class="text-xs text-green-600 uppercase tracking-wide mb-1">Upload</p>
                        <p class="text-2xl font-bold text-green-700" x-text="currentUpload + ' Mbps'">0 Mbps</p>
                    </div>
                </div>

                <!-- Chart -->
                <div class="relative" style="height: 250px;">
                    <canvas id="trafficChart" x-ref="trafficChart"></canvas>
                </div>
            </div>

            <div class="app-card">
                <h2 class="section-title">Log Monitoring</h2>
                <div class="mt-3 space-y-2 text-xs text-gray-600">
                    <p>Uptime: {{ $onu->uptime_formatted }}</p>
                    <p>Durasi Online: {{ gmdate('H:i:s', $onu->duration_online) }}</p>
                </div>
                <div class="mt-4 space-y-3 text-xs text-gray-500">
                    @foreach(range(1,6) as $i)
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $i % 2 ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                        <span>{{ now()->subMinutes($i * 5)->format('Y-m-d H:i') }}</span>
                        <span>{{ $i % 2 ? 'Online' : 'DyingGasp' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="app-card">
                <h2 class="section-title">Aksi Cepat</h2>
                <div class="space-y-2 text-sm">
                    <button class="btn-secondary w-full"><i class="fas fa-sync mr-2"></i>Sync Data Lokal</button>
                    <button class="btn-secondary w-full"><i class="fas fa-file-export mr-2"></i>Export Config</button>
                    @can('manage-onu')
                    <form action="{{ route('onus.destroy', $onu) }}" method="POST" onsubmit="return confirm('Yakin hapus ONU ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger w-full"><i class="fas fa-trash mr-2"></i>Hapus ONU</button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<x-guide-panel key="onu-guide">
    <x-slot name="title">Panduan Konfigurasi ONU</x-slot>
    <ol class="list-decimal ml-5 text-sm text-gray-600 space-y-2">
        <li>Pilih OLT dan port GPON sesuai instalasi fisik.</li>
        <li>Isi serial number dan VLAN asli pelanggan.</li>
        <li>Pilih mode WAN (PPPoE NAT atau Bridge) sesuai kebutuhan.</li>
        <li>Masukkan PPPoE username/password atau credential TR-069 bila diperlukan.</li>
        <li>Tekan "Simpan & Provisioning", lalu cek status di panel ini.</li>
    </ol>
</x-guide-panel>

@can('manage-onu')
<!-- Modal Edit Info ONU -->
<div x-show="showInfoModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Update Informasi</p>
                <h3 class="text-base font-semibold text-gray-900">Hubungkan ke Pelanggan / ODP</h3>
            </div>
            <button @click="showInfoModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('onus.update', $onu) }}" class="px-5 py-4 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Nama/Internal ID</label>
                    <input type="text" name="nama" value="{{ old('nama', $onu->nama) }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Tipe ONT/ONU</label>
                    <input type="text" name="ont_type" value="{{ old('ont_type', $onu->ont_type) }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div data-select-search
                     data-options='@json($pelangganSearchOptionsModal)'
                     data-odp-select="#modal-odp-select">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Pelanggan</label>
                    <div class="relative">
                        <input type="text" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik nama / PPPoE" data-select-search-input>
                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400"><i class="fas fa-search text-xs"></i></span>
                    </div>
                    <input type="hidden" name="pelanggan_id" value="{{ old('pelanggan_id', $onu->pelanggan_id) }}" data-select-search-value x-ref="infoPelangganSelect" @change="handlePelangganChange($event)">
                    <div data-select-search-results class="space-y-2 max-h-44 overflow-y-auto mt-2 hidden"></div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">ODP</label>
                    <select name="odp_id" id="modal-odp-select" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" x-ref="infoOdpSelect">
                        <option value="">Tidak dihubungkan</option>
                        @foreach($odps as $odp)
                        <option value="{{ $odp->id }}" {{ old('odp_id', $onu->odp_id) == $odp->id ? 'selected' : '' }}>
                            {{ $odp->kode_odp ?? ('ODP ' . $odp->id) }} - {{ $odp->nama ?? '-' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Catatan</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $onu->description) }}</textarea>
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                <button type="button" class="btn-secondary" @click="showInfoModal = false">Batal</button>
                <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endcan

<!-- Modal Tambah Service -->
<div x-show="showAddServiceModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Service Management</p>
                <h3 class="text-base font-semibold text-gray-900">Tambah Service Baru</h3>
            </div>
            <button @click="showAddServiceModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('onus.services.store', $onu) }}" class="px-5 py-4">
            @csrf
            @include('onus.partials.service-form', ['service' => null])
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
                <button type="button" @click="showAddServiceModal = false" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>Simpan Service</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Service -->
@foreach($onu->services as $service)
<div x-show="showEditServiceModal && editingServiceId == {{ $service->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Service Management</p>
                <h3 class="text-base font-semibold text-gray-900">Edit Service {{ $service->service_id }}</h3>
            </div>
            <button @click="showEditServiceModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('onus.services.update', [$onu, $service]) }}" class="px-5 py-4">
            @csrf @method('PUT')
            @include('onus.partials.service-form', ['service' => $service])
            <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
                <button type="button" @click="showEditServiceModal = false" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>Update Service</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal Remote Access -->
<div x-show="showRemoteAccessModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Remote Access</p>
                <h3 class="text-base font-semibold text-gray-900">Tambah Remote Access Rule</h3>
            </div>
            <button @click="showRemoteAccessModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        @php
            $primaryService = $onu->services->first();
            $currentRules = $primaryService?->remote_access_rules ?? [];
        @endphp
        @if($primaryService)
        <form method="POST" action="{{ route('onus.services.remote-access', [$onu, $primaryService]) }}" 
              class="px-5 py-4 space-y-4"
              x-data="{ 
                remoteRules: @js($currentRules), 
                newRule: { name: '', ingress: 'WAN', service: 'web', action: 'allow', enabled: true, source_ip: '', destination_ip: '' },
                addRule() {
                    if (!this.newRule.name || this.newRule.name.trim() === '') {
                        alert('Nama rule wajib diisi!');
                        return;
                    }
                    // Clone object untuk menghindari reference
                    const ruleToAdd = {
                        name: this.newRule.name.trim(),
                        ingress: this.newRule.ingress,
                        service: this.newRule.service,
                        action: this.newRule.action,
                        enabled: this.newRule.enabled === true || this.newRule.enabled === 'true',
                        source_ip: this.newRule.source_ip || '',
                        destination_ip: this.newRule.destination_ip || ''
                    };
                    this.remoteRules.push(ruleToAdd);
                    // Reset form
                    this.newRule = { name: '', ingress: 'WAN', service: 'web', action: 'allow', enabled: true, source_ip: '', destination_ip: '' };
                }
              }"
              @submit.prevent="
                if (remoteRules.length === 0) {
                    alert('Tambahkan minimal 1 rule sebelum menyimpan');
                    return false;
                }
                // Update hidden input dengan data terbaru
                $refs.rulesInput.value = JSON.stringify(remoteRules);
                console.log('Submitting rules:', remoteRules);
                console.log('JSON string:', $refs.rulesInput.value);
                // Submit form
                $el.submit();
              ">
            @csrf @method('PUT')
            <div>
                <!-- Existing Rules -->
                <div class="space-y-2 mb-4 max-h-40 overflow-y-auto border border-gray-200 rounded-xl p-3">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Rules yang akan disimpan:</p>
                    <template x-if="remoteRules.length === 0">
                        <p class="text-xs text-gray-400 text-center py-2">Belum ada rule. Tambahkan rule baru di bawah.</p>
                    </template>
                    <template x-for="(rule, index) in remoteRules" :key="index">
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg text-xs mb-1">
                            <div class="flex-1">
                                <span class="font-semibold" x-text="rule.name || ('Rule ' + (index + 1))"></span>
                                <span class="text-gray-500 ml-2" x-text="rule.ingress + ' · ' + rule.service + ' · ' + rule.action"></span>
                            </div>
                            <button type="button" @click="remoteRules.splice(index, 1)" class="text-red-600 hover:text-red-700 ml-2">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <!-- Form Tambah Rule Baru -->
                <div class="space-y-4 border-t border-gray-200 pt-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Nama Rule</label>
                        <input type="text" x-model="newRule.name" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl" placeholder="Contoh: Web Access">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Ingress</label>
                            <select x-model="newRule.ingress" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl">
                                <option value="WAN">WAN</option>
                                <option value="LAN">LAN</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Service</label>
                            <select x-model="newRule.service" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl">
                                <option value="web">Web</option>
                                <option value="ssh">SSH</option>
                                <option value="telnet">Telnet</option>
                                <option value="http">HTTP</option>
                                <option value="https">HTTPS</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Action</label>
                        <div class="flex gap-3">
                            <label class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-2 cursor-pointer" :class="newRule.action === 'allow' ? 'border-green-500 bg-green-50' : 'border-gray-200'">
                                <input type="radio" x-model="newRule.action" value="allow" class="sr-only">
                                <span class="text-sm font-semibold">Allow</span>
                            </label>
                            <label class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-2 cursor-pointer" :class="newRule.action === 'deny' ? 'border-red-500 bg-red-50' : 'border-gray-200'">
                                <input type="radio" x-model="newRule.action" value="deny" class="sr-only">
                                <span class="text-sm font-semibold">Deny</span>
                            </label>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Source IP</label>
                            <input type="text" x-model="newRule.source_ip" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl" placeholder="0.0.0.0">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Destination IP</label>
                            <input type="text" x-model="newRule.destination_ip" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl" placeholder="0.0.0.0">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" x-model="newRule.enabled" id="rule_enabled" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                        <label for="rule_enabled" class="text-sm text-gray-700">Aktifkan Rule</label>
                    </div>
                    <button type="button" @click="addRule()" class="btn-secondary w-full">
                        <i class="fas fa-plus mr-2"></i>Tambah ke List
                    </button>
                </div>

                <!-- Hidden input untuk rules -->
                <input type="hidden" name="rules" x-ref="rulesInput" :value="JSON.stringify(remoteRules)">
                
                <!-- Debug info -->
                <div class="text-xs text-gray-400 mb-2" x-show="remoteRules.length > 0">
                    <i class="fas fa-info-circle mr-1"></i>
                    <span x-text="remoteRules.length + ' rule(s) akan disimpan'"></span>
                </div>
                <div class="text-xs text-red-500 mb-2" x-show="remoteRules.length === 0">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Tambahkan minimal 1 rule sebelum menyimpan
                </div>
                
                <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="showRemoteAccessModal = false" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary" :disabled="remoteRules.length === 0" :class="{ 'opacity-50 cursor-not-allowed': remoteRules.length === 0 }">
                        <i class="fas fa-save mr-2"></i>Simpan Rules (<span x-text="remoteRules.length"></span>)
                    </button>
                </div>
            </div>
        </form>
        @else
        <div class="px-5 py-4 text-center">
            <p class="text-sm text-gray-400">Buat service terlebih dahulu untuk mengatur remote access.</p>
            <button @click="showRemoteAccessModal = false" class="btn-secondary mt-4">Tutup</button>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const initSelectSearch = (wrapper) => {
            const input = wrapper.querySelector('[data-select-search-input]');
            const hiddenInput = wrapper.querySelector('[data-select-search-value]');
            const resultsContainer = wrapper.querySelector('[data-select-search-results]');
            const optionsData = wrapper.dataset.options ? JSON.parse(wrapper.dataset.options) : [];

        if (!input || !hiddenInput || !resultsContainer || optionsData.length === 0) {
            return;
        }

        resultsContainer.classList.add('hidden');

            const maxItems = parseInt(wrapper.dataset.maxItems || '6', 10);

            const setDisplayValue = (option) => {
                if (!option) return;
                const subtitle = option.subtitle ? ` • ${option.subtitle}` : '';
                input.value = `${option.label}${subtitle}`;
            };

        const selectOption = (option) => {
            hiddenInput.value = option.value;
            hiddenInput.dispatchEvent(new Event('change'));
            setDisplayValue(option);
            resultsContainer.innerHTML = '';
            resultsContainer.classList.add('hidden');
        };

        const renderResults = (term = '') => {
            const normalized = (term || '').toLowerCase();

            if (normalized.length < 2) {
                resultsContainer.innerHTML = '';
                resultsContainer.classList.add('hidden');
                return;
            }

            const filtered = optionsData.filter(option => {
                return [option.label, option.subtitle, option.meta]
                    .filter(Boolean)
                    .some(text => text.toLowerCase().includes(normalized));
            }).slice(0, maxItems);

            resultsContainer.innerHTML = '';
            resultsContainer.classList.remove('hidden');

            if (filtered.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'p-2 text-xs text-gray-400';
                empty.textContent = 'Tidak ada hasil';
                resultsContainer.appendChild(empty);
                return;
            }

            filtered.forEach(option => {
                const card = document.createElement('div');
                card.className = 'p-2 text-xs bg-white border border-gray-200 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition';

                const title = document.createElement('p');
                title.className = 'font-semibold text-gray-900';
                title.textContent = option.label;
                card.appendChild(title);

                if (option.subtitle) {
                    const subtitle = document.createElement('p');
                    subtitle.className = 'text-gray-500';
                    subtitle.textContent = option.subtitle;
                    card.appendChild(subtitle);
                }

                if (option.meta) {
                    const meta = document.createElement('p');
                    meta.className = 'text-gray-400 text-[11px] mt-1';
                    meta.textContent = option.meta;
                    card.appendChild(meta);
                }

                card.addEventListener('click', () => selectOption(option));
                resultsContainer.appendChild(card);
            });
        };

            const debounce = (fn, delay = 250) => {
                let timeout;
                return (...args) => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => fn.apply(null, args), delay);
                };
            };

            input.addEventListener('input', debounce((event) => {
                renderResults(event.target.value.trim());
            }, 250));

            if (hiddenInput.value) {
                const selectedOption = optionsData.find(option => option.value === hiddenInput.value);
                if (selectedOption) {
                    setDisplayValue(selectedOption);
                }
            }

            renderResults();
        };

        document.querySelectorAll('[data-select-search]').forEach(initSelectSearch);

        document.querySelectorAll('.onu-action-form').forEach((form) => {
            const handler = async function(event) {
                event.preventDefault();
                const title = form.dataset.confirmTitle || 'Konfirmasi';
                const text = form.dataset.confirmText || 'Lanjutkan aksi ini?';
                const icon = form.dataset.confirmIcon || 'warning';
                const confirmButtonText = form.dataset.confirmButton || 'Ya, lanjutkan';

                const result = await Swal.fire({
                    title,
                    text,
                    icon,
                    showCancelButton: true,
                    confirmButtonText,
                    cancelButtonText: 'Batal',
                    focusCancel: true,
                });

                if (result.isConfirmed) {
                    form.removeEventListener('submit', handler);
                    form.submit();
                }
            };

            form.addEventListener('submit', handler);
        });
    });
</script>
@endpush

