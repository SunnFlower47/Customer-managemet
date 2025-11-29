@extends('layouts.app')

@section('title', 'Detail OLT')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-server"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $olt->nama }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">
                    <span class="font-mono">{{ $olt->ip_address }}</span>
                    @if($olt->port)
                    <span class="mx-2">·</span>
                    <span>Port: {{ $olt->port }}</span>
                    @endif
                    @if($olt->vendor || $olt->model)
                    <span class="mx-2">·</span>
                    <span>{{ $olt->vendor ?? '' }} {{ $olt->model ?? '' }}</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <form action="{{ route('olts.test-connection', $olt) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-satellite-dish mr-2"></i>Test Koneksi
                </button>
            </form>
            @can('sync-olt')
            <form action="{{ route('olts.sync', $olt) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-sync mr-2"></i>Sinkron
                </button>
            </form>
            @endcan
            @can('manage-olt')
            <a href="{{ route('olts.edit', $olt) }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            @endcan
        </div>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status OLT</p>
                <div class="flex items-center gap-3">
                    @if($olt->status === 'online')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                        <i class="fas fa-circle text-[8px] mr-2"></i>Online
                    </span>
                    @elseif($olt->status === 'offline')
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                        <i class="fas fa-circle text-[8px] mr-2"></i>Offline
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                        <i class="fas fa-circle text-[8px] mr-2"></i>Error
                    </span>
                    @endif
                    @if($olt->last_checked_at)
                    <span class="text-xs text-gray-500">
                        <i class="fas fa-clock mr-1"></i>Terakhir: {{ $olt->last_checked_at->diffForHumans() }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Kode OLT</p>
                <p class="text-lg font-bold text-gray-900">{{ $olt->kode_olt }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi OLT -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi OLT</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">IP Address</p>
                        <p class="text-sm font-semibold text-gray-900 font-mono">{{ $olt->ip_address }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Port</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $olt->port ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Vendor</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $olt->vendor ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Model</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $olt->model ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Tipe Koneksi</p>
                        <p class="text-sm font-semibold text-gray-900 uppercase">{{ $olt->connection_type ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">SNMP Community</p>
                        <p class="text-sm font-semibold text-gray-900 font-mono">{{ $olt->snmp_community ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Port</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $olt->ports_terpakai ?? 0 }}/{{ $olt->total_ports ?? 0 }} Port
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">ONU Terhubung</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $olt->onus_count ?? $olt->onu_terhubung ?? 0 }} ONU</p>
                    </div>
                </div>
                @if($olt->alamat)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Alamat/Lokasi</p>
                    <p class="text-sm text-gray-700">{{ $olt->alamat }}</p>
                </div>
                @endif
                @if($olt->description)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Deskripsi</p>
                    <p class="text-sm text-gray-700">{{ $olt->description }}</p>
                </div>
                @endif
                @if($olt->latitude && $olt->longitude)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Koordinat</p>
                    <p class="text-sm text-gray-700 font-mono">{{ $olt->latitude }}, {{ $olt->longitude }}</p>
                </div>
                @endif
            </div>

            <!-- Port PON -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Port PON</h2>
                    <span class="text-xs text-gray-500">{{ $olt->ponPorts->count() }} Port</span>
                </div>
                @if($olt->ponPorts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Card/Port</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">ONU</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Nama</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($olt->ponPorts as $port)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-semibold text-gray-900">{{ $port->port_number }}</td>
                                <td class="px-3 py-2">
                                    @if($port->status === 'up')
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-800">Up</span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-800">Down</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-gray-900">{{ $port->onu_count ?? 0 }} ONU</td>
                                <td class="px-3 py-2 text-gray-600">{{ $port->name ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-plug text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada data port PON</p>
                </div>
                @endif
            </div>

            <!-- Daftar ONU -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Daftar ONU</h2>
                    <a href="{{ route('onus.index', ['olt_id' => $olt->id]) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                        Lihat semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @if($olt->onus->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Serial Number</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Nama</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">RX Power</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($olt->onus->take(10) as $onu)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-mono text-xs text-gray-900">{{ $onu->serial_number }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ $onu->nama ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @if($onu->status === 'online')
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-800">Online</span>
                                    @elseif($onu->status === 'offline')
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-800">Offline</span>
                                    @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-800">{{ ucfirst($onu->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-gray-900">
                                    @if($onu->rx_power)
                                        {{ number_format($onu->rx_power, 2) }} dBm
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <a href="{{ route('onus.show', $onu) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-sitemap text-4xl mb-2"></i>
                    <p class="text-sm">Belum ada ONU terdaftar</p>
                    @can('manage-onu')
                    <a href="{{ route('onus.register', ['olt_id' => $olt->id]) }}" class="inline-block mt-3 px-4 py-2 text-xs font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Register ONU
                    </a>
                    @endcan
                </div>
                @endif
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Monitoring Info -->
            @if(isset($monitoringData) && !empty($monitoringData))
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Monitoring</h2>
                <div class="space-y-3 text-sm">
                    @if(isset($monitoringData['system_info']))
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">System Info</p>
                        <p class="text-gray-900">{{ $monitoringData['system_info']['description'] ?? '-' }}</p>
                    </div>
                    @endif
                    @if(isset($monitoringData['statistics']))
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Uptime</p>
                        <p class="text-gray-900">{{ $monitoringData['statistics']['uptime'] ?? '-' }}</p>
                    </div>
                    @endif
                    @if(isset($monitoringData['alarms']))
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Alarms</p>
                        <p class="text-gray-900">{{ count($monitoringData['alarms']) }} alarm</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Sync Logs -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Log Sinkronisasi</h2>
                <div class="space-y-3 max-h-[500px] overflow-y-auto">
                    @forelse($olt->syncLogs->take(10) as $log)
                    <div class="border border-gray-200 rounded-xl p-3 bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-900">#{{ $log->id }}</span>
                            <span class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $log->status === 'completed' ? 'bg-green-100 text-green-800' : ($log->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                            <span class="text-xs text-gray-600 uppercase">{{ $log->sync_type }}</span>
                        </div>
                        @if($log->message)
                        <p class="text-xs text-gray-600 mt-1">{{ Str::limit($log->message, 100) }}</p>
                        @endif
                        @if($log->error_message)
                        <p class="text-xs text-red-600 mt-1">{{ Str::limit($log->error_message, 100) }}</p>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-6 text-gray-400">
                        <i class="fas fa-history text-3xl mb-2"></i>
                        <p class="text-xs">Belum ada log sinkronisasi</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
