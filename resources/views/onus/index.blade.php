@extends('layouts.app')

@section('title', 'Daftar ONU')

@section('content')
@php
    $inputClass = 'w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
    $labelClass = 'block text-xs font-semibold text-gray-600 mb-2';
    $thresholdGood = $thresholds['good'];
    $thresholdWarning = $thresholds['warning'];
    $totalSignals = max($totalOnus, 1);
    $signalCards = [
        'good' => ['label' => 'Good', 'color' => 'text-green-500', 'bg' => 'bg-green-50', 'value' => $signalStats['good'], 'range' => "≥ {$thresholdGood} dBm", 'icon' => 'fas fa-signal'],
        'warning' => ['label' => 'Warning', 'color' => 'text-yellow-500', 'bg' => 'bg-yellow-50', 'value' => $signalStats['warning'], 'range' => "{$thresholdWarning} dBm s/d {$thresholdGood} dBm", 'icon' => 'fas fa-exclamation-triangle'],
        'critical' => ['label' => 'Critical', 'color' => 'text-red-500', 'bg' => 'bg-red-50', 'value' => $signalStats['critical'], 'range' => "< {$thresholdWarning} dBm", 'icon' => 'fas fa-radiation'],
        'los' => ['label' => 'LOS', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50', 'value' => $signalStats['los'], 'range' => 'Loss of signal', 'icon' => 'fas fa-bolt'],
    ];

    $baseQuery = request()->except(['page']);
@endphp

<div class="space-y-6 lg:space-y-8" x-data="{ showThresholdModal: false, warning: {{ $thresholdWarning }}, good: {{ $thresholdGood }} }">
    <!-- Info Alert - Show if no ONUs -->
    @if($onus->isEmpty() && !request()->has('search') && !request()->has('olt_id'))
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-xl">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-blue-900 mb-1">ONU Belum Terdeteksi</p>
                <p class="text-xs text-blue-800 mb-2">
                    ONU <strong>tidak otomatis muncul</strong> di menu ini. Anda perlu melakukan <strong>sinkronisasi (sync)</strong> dari OLT terlebih dahulu untuk mengambil data ONU dari OLT.
                </p>
                <div class="text-xs text-blue-700 space-y-1">
                    <p><strong>📋 Cara mendeteksi ONU:</strong></p>
                    <ol class="list-decimal ml-4 space-y-1">
                        <li>Buka halaman detail OLT (Menu: <strong>OLT Monitoring → OLTs</strong>)</li>
                        <li>Klik tombol <strong>"Sinkron"</strong> di header halaman detail OLT</li>
                        <li>Tunggu proses selesai (progress bar akan menampilkan persentase)</li>
                        <li>ONU yang terdeteksi akan muncul di menu ini</li>
                    </ol>
                    <p class="mt-2"><strong>💡 Tips:</strong> Sistem juga memiliki auto-sync yang berjalan setiap 5 menit untuk update otomatis.</p>
                </div>
                <div class="mt-3">
                    <a href="{{ route('olts.index') }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-server mr-2"></i>Ke Halaman OLTs untuk Sync
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-sitemap"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">All ONUs</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Pantau status, kualitas sinyal, dan lakukan filter cepat.</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-wrap gap-2">
            <button type="button" @click="showThresholdModal = true" class="btn-secondary">
                <i class="fas fa-wave-square mr-2"></i>Atur Batas Signal
            </button>
            @can('manage-onu')
            <a href="{{ route('onus.register') }}" class="btn-primary"><i class="fas fa-plus mr-2"></i>Register ONU</a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="app-card">
            <p class="text-xs text-gray-500">Total ONU</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="app-card">
            <p class="text-xs text-gray-500">Online</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['online'] }}</p>
        </div>
        <div class="app-card">
            <p class="text-xs text-gray-500">Offline</p>
            <p class="text-2xl font-bold text-gray-600">{{ $stats['offline'] }}</p>
        </div>
        <div class="app-card">
            <p class="text-xs text-gray-500">Terdaftar</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['registered'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach($signalCards as $key => $card)
        <div class="app-card {{ $card['bg'] }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">{{ $card['label'] }}</p>
                    <p class="text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
                </div>
                <i class="{{ $card['icon'] }} text-lg {{ $card['color'] }}"></i>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ $card['range'] }}</p>
            <p class="text-[10px] text-gray-400 mt-1">{{ number_format(($card['value'] / $totalSignals) * 100, 1) }}%</p>
        </div>
        @endforeach
    </div>

    <div class="app-card app-card--soft space-y-4">
        <form method="GET" action="{{ route('onus.index') }}" class="grid grid-cols-1 lg:grid-cols-5 gap-4">
            <input type="hidden" name="signal_good" value="{{ $thresholdGood }}">
            <input type="hidden" name="signal_warning" value="{{ $thresholdWarning }}">
            <div>
                <label class="{{ $labelClass }}">OLT</label>
                <select name="olt_id" class="{{ $inputClass }}">
                    <option value="">Semua OLT</option>
                    @foreach($olts as $item)
                    <option value="{{ $item->id }}" {{ request('olt_id') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Status</label>
                <select name="status" class="{{ $inputClass }}">
                    <option value="">Semua</option>
                    @foreach(['online','offline','los','dying_gasp'] as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Card / Port</label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" name="card" value="{{ request('card') }}" class="{{ $inputClass }}" placeholder="Card">
                    <input type="number" name="port" value="{{ request('port') }}" class="{{ $inputClass }}" placeholder="Port">
                </div>
            </div>
            <div>
                <label class="{{ $labelClass }}">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="{{ $inputClass }}" placeholder="Serial / nama / MAC">
            </div>
            <div class="flex items-end gap-2">
                <button class="btn-primary flex-1"><i class="fas fa-search mr-2"></i>Filter</button>
                <a href="{{ route('onus.index', ['signal_good' => $thresholdGood, 'signal_warning' => $thresholdWarning]) }}" class="btn-secondary px-3"><i class="fas fa-redo"></i></a>
            </div>
        </form>

        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
            <span class="text-gray-400 uppercase tracking-wide">Quick Filter:</span>
            <a href="{{ route('onus.index', array_merge($baseQuery, ['status' => 'online'])) }}" class="inline-flex items-center px-3 py-1.5 rounded-xl border text-xs font-semibold {{ request('status') === 'online' ? 'border-green-500 text-green-600 bg-green-50' : 'border-gray-200 text-gray-500 hover:border-green-200' }}">
                <i class="fas fa-circle text-green-500 mr-1"></i>Online
            </a>
            <a href="{{ route('onus.index', array_merge($baseQuery, ['status' => 'offline'])) }}" class="inline-flex items-center px-3 py-1.5 rounded-xl border text-xs font-semibold {{ request('status') === 'offline' ? 'border-gray-500 text-gray-700 bg-gray-50' : 'border-gray-200 text-gray-500 hover:border-gray-300' }}">
                <i class="fas fa-circle text-gray-400 mr-1"></i>Offline
            </a>
            <a href="{{ route('onus.index', array_merge($baseQuery, ['status' => 'los'])) }}" class="inline-flex items-center px-3 py-1.5 rounded-xl border text-xs font-semibold {{ request('status') === 'los' ? 'border-rose-500 text-rose-600 bg-rose-50' : 'border-gray-200 text-gray-500 hover:border-rose-200' }}">
                <i class="fas fa-bolt text-rose-500 mr-1"></i>LOS
            </a>
            <a href="{{ route('onus.index', array_merge($baseQuery, ['status' => 'dying_gasp'])) }}" class="inline-flex items-center px-3 py-1.5 rounded-xl border text-xs font-semibold {{ request('status') === 'dying_gasp' ? 'border-yellow-500 text-yellow-600 bg-yellow-50' : 'border-gray-200 text-gray-500 hover:border-yellow-200' }}">
                <i class="fas fa-heartbeat text-yellow-500 mr-1"></i>Dying Gasp
            </a>
        </div>
    </div>

    <div class="app-card overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wide">
                    <th class="px-4 py-3">OLT / Port</th>
                    <th class="px-4 py-3">Nama & Pelanggan</th>
                    <th class="px-4 py-3">PPPoE / Serial</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Signal</th>
                    <th class="px-4 py-3">Terakhir Online</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($onus as $onu)
                @php
                    $signalLabel = 'Critical';
                    $signalClass = 'text-red-600 bg-red-50 border border-red-100';
                    if (!is_null($onu->rx_power) && $onu->rx_power >= $thresholdGood) {
                        $signalLabel = 'Good';
                        $signalClass = 'text-green-600 bg-green-50 border border-green-100';
                    } elseif (!is_null($onu->rx_power) && $onu->rx_power >= $thresholdWarning) {
                        $signalLabel = 'Warning';
                        $signalClass = 'text-yellow-600 bg-yellow-50 border border-yellow-100';
                    }
                @endphp
                <tr class="hover:bg-blue-50 transition">
                    <td class="px-4 py-3 text-xs text-gray-600">
                        <p class="font-semibold text-gray-900">{{ $onu->olt?->nama ?? '-' }}</p>
                        <p>Card {{ $onu->card ?? '-' }} / Port {{ $onu->port ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-gray-900">{{ $onu->nama ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $onu->pelanggan->nama ?? '-' }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        <p>PPPoE: {{ $onu->services->first()->pppoe_username ?? '-' }}</p>
                        <p class="font-mono text-[11px] text-gray-500">{{ $onu->serial_number }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $onu->status === 'online' ? 'badge-success' : ($onu->status === 'offline' ? 'badge-muted' : 'badge-danger') }}">
                            {{ ucfirst(str_replace('_',' ', $onu->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold {{ $signalClass }}">
                            <i class="fas fa-signal"></i>
                            <span>{{ $onu->rx_power ?? 'N/A' }} dBm</span>
                            <span class="text-[10px] uppercase">{{ $signalLabel }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ optional($onu->last_online_at)->diffForHumans() ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('onus.show', $onu) }}" class="btn-secondary px-3 py-1.5 text-xs"><i class="fas fa-cog mr-1"></i>Setting</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-400">Tidak ada data ONU.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $onus->links() }}
        </div>
    </div>

    <!-- Signal Threshold Modal -->
    <div x-show="showThresholdModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
        <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">Signal Filter</p>
                    <h3 class="text-base font-semibold text-gray-900">Atur Batas dBm</h3>
                </div>
                <button @click="showThresholdModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <form method="GET" action="{{ route('onus.index') }}" class="px-5 py-4 space-y-5">
                @foreach(request()->except(['signal_good','signal_warning','page']) as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach
                <div class="space-y-3">
                    <label class="text-xs font-semibold text-gray-600">Critical / Warning Threshold</label>
                    <input type="range" min="-35" max="-20" step="0.5" x-model="warning" class="w-full">
                    <p class="text-sm text-gray-500">Warning dimulai dari <span class="font-semibold text-yellow-600" x-text="warning + ' dBm'"></span></p>
                </div>
                <div class="space-y-3">
                    <label class="text-xs font-semibold text-gray-600">Warning / Good Threshold</label>
                    <input type="range" min="-30" max="-15" step="0.5" x-model="good" class="w-full">
                    <p class="text-sm text-gray-500">Good dimulai dari <span class="font-semibold text-green-600" x-text="good + ' dBm'"></span></p>
                </div>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="border rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 uppercase">Good</p>
                        <p class="text-green-600 font-semibold" x-text="'≥ ' + good + ' dBm'"></p>
                    </div>
                    <div class="border rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 uppercase">Warning</p>
                        <p class="text-yellow-500 font-semibold" x-text="warning + ' - ' + good + ' dBm'"></p>
                    </div>
                    <div class="border rounded-xl p-3 text-center">
                        <p class="text-xs text-gray-400 uppercase">Critical</p>
                        <p class="text-red-500 font-semibold" x-text="'< ' + warning + ' dBm'"></p>
                    </div>
                </div>
                <input type="hidden" name="signal_warning" :value="warning">
                <input type="hidden" name="signal_good" :value="good">
                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                    <button type="button" class="text-xs text-gray-500 hover:text-gray-700" @click="warning = {{ $thresholdWarning }}; good = {{ $thresholdGood }}">Reset Default</button>
                    <div class="flex gap-2">
                        <button type="button" class="btn-secondary" @click="showThresholdModal = false">Batal</button>
                        <button type="submit" class="btn-primary">Apply Threshold</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

