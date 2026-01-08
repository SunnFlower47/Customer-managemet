@extends('layouts.app')

@section('title', 'Register ONU')

@section('content')
@php
    $inputClass = 'w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
    $labelClass = 'block text-xs font-semibold text-gray-700 mb-2';
    $selectedOltId = old('olt_id', $prefill['olt_id'] ?? null);
    $selectedOlt = $selectedOltId ? $olts->firstWhere('id', $selectedOltId) : null;
@endphp
@php
$pelangganSearchOptions = $pelanggans->map(function ($pelanggan) {
    return [
        'value' => (string) $pelanggan->id,
        'label' => $pelanggan->nama,
        'subtitle' => $pelanggan->pppoe ?? '-',
        'meta' => $pelanggan->no_hp ?? '',
        'odp_id' => $pelanggan->odp_id,
    ];
})->values();
@endphp

<div class="space-y-6 lg:space-y-8" x-data="{ wanMode: '{{ old('wan_mode', 'pppoe') }}', vlanSource: '{{ old('vlan_id') ? 'database' : 'manual' }}' }">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-plug"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Register ONU</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Pilih OLT, masukkan detail ONU, lalu atur profil layanan sebelum provisioning.</p>
            </div>
        </div>
        <div class="page-header__actions flex items-center gap-2">
            <button type="button" x-data x-on:click="$dispatch('open-guide-onus-register')" class="btn-secondary">
                <i class="fas fa-book-open mr-2"></i>Panduan
            </button>
            <a href="{{ route('onus.index') }}" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Kembali</a>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 rounded-xl p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-3 flex-shrink-0"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-800 mb-2">Terdapat kesalahan pada form:</p>
                <ul class="text-xs text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 rounded-xl p-4 mb-6">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-red-600 mt-0.5 mr-3 flex-shrink-0"></i>
            <div class="flex-1">
                <p class="text-sm font-semibold text-red-800 mb-1">Registrasi Gagal</p>
                <p class="text-xs text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('onus.register.store') }}">
        @csrf
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="app-card space-y-6">
                    <div>
                        <h2 class="section-title">Informasi ONU</h2>
                        <p class="text-xs text-gray-500">Identitas dasar ONU dan relasinya dengan pelanggan/ODP.</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}">OLT</label>
                            <select name="olt_id" class="{{ $inputClass }}" required>
                                <option value="">Pilih OLT</option>
                                @foreach($olts as $item)
                                <option value="{{ $item->id }}" {{ $selectedOltId == $item->id ? 'selected' : '' }}>{{ $item->nama }} ({{ $item->kode_olt }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Serial Number</label>
                            <input type="text" name="serial_number" value="{{ old('serial_number', $prefill['serial_number'] ?? '') }}" class="{{ $inputClass }}" required>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Nama/Internal ID</label>
                            <input type="text" name="nama" value="{{ old('nama', $prefill['nama'] ?? '') }}" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">MAC Address</label>
                            <input type="text" name="mac_address" value="{{ old('mac_address', $prefill['mac_address'] ?? '') }}" class="{{ $inputClass }}">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="{{ $labelClass }}">Card</label>
                                <input type="number" name="card" value="{{ old('card', $prefill['card'] ?? '') }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Port</label>
                                <input type="number" name="port" value="{{ old('port', $prefill['port'] ?? '') }}" class="{{ $inputClass }}">
                            </div>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Tipe ONT/ONU</label>
                            <input type="text" name="ont_type" value="{{ old('ont_type') }}" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Vendor</label>
                            <input type="text" name="vendor" value="{{ old('vendor', $prefill['vendor'] ?? '') }}" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Model</label>
                            <input type="text" name="model" value="{{ old('model', $prefill['model'] ?? '') }}" class="{{ $inputClass }}">
                        </div>
                        <div data-select-search
                             data-options='@json($pelangganSearchOptions)'
                             data-odp-select="#register-odp-select">
                            <label class="{{ $labelClass }}">Pelanggan</label>
                            <div class="relative">
                                <input type="text" class="{{ $inputClass }}" placeholder="Ketik nama / PPPoE" data-select-search-input>
                                <span class="absolute inset-y-0 right-3 flex items-center text-gray-400"><i class="fas fa-search text-xs"></i></span>
                            </div>
                            <input type="hidden" name="pelanggan_id" value="{{ old('pelanggan_id') }}" data-select-search-value>
                            <div data-select-search-results class="space-y-2 max-h-60 overflow-y-auto mt-2 hidden">
                                @foreach($pelanggans as $pelanggan)
                                <div class="p-2 text-xs bg-white border border-gray-200 rounded-lg shadow-sm cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition" data-value="{{ $pelanggan->id }}">
                                    <p class="font-semibold text-gray-900">{{ $pelanggan->nama }}</p>
                                    <p class="text-gray-500">{{ $pelanggan->pppoe ?? '-' }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">ODP</label>
                            <select name="odp_id" id="register-odp-select" class="{{ $inputClass }}">
                                <option value="">Tidak dihubungkan</option>
                                @foreach($odps as $odp)
                                <option value="{{ $odp->id }}" {{ old('odp_id') == $odp->id ? 'selected' : '' }}>
                                    {{ $odp->kode_odp ?? ('ODP ' . $odp->id) }} - {{ $odp->nama ?? '-' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Catatan internal</label>
                        <textarea name="description" rows="3" class="{{ $inputClass }}">{{ old('description') }}</textarea>
                    </div>
                </div>

                <!-- Service 1 Configuration -->
                <div class="app-card space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="section-title">Service 1 - Konfigurasi WAN</h2>
                            <p class="text-xs text-gray-500">Pilih mode WAN dan atur konfigurasi sesuai mode yang dipilih.</p>
                        </div>
                        <span class="text-xs text-gray-500"><i class="fas fa-info-circle mr-1"></i>Service tambahan bisa ditambah manual di detail ONU</span>
                    </div>

                    <!-- Mode WAN Selection -->
                    <div>
                        <label class="{{ $labelClass }}">Mode WAN <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all {{ old('wan_mode', 'pppoe') === 'pppoe' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <input type="radio" name="wan_mode" value="pppoe" x-model="wanMode" {{ old('wan_mode', 'pppoe') === 'pppoe' ? 'checked' : '' }} class="sr-only">
                                <i class="fas fa-network-wired"></i>
                                <span class="text-sm font-semibold">PPPoE</span>
                            </label>
                            <label class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all {{ old('wan_mode') === 'dhcp' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <input type="radio" name="wan_mode" value="dhcp" x-model="wanMode" {{ old('wan_mode') === 'dhcp' ? 'checked' : '' }} class="sr-only">
                                <i class="fas fa-globe"></i>
                                <span class="text-sm font-semibold">DHCP</span>
                            </label>
                            <label class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all {{ old('wan_mode') === 'static' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <input type="radio" name="wan_mode" value="static" x-model="wanMode" {{ old('wan_mode') === 'static' ? 'checked' : '' }} class="sr-only">
                                <i class="fas fa-server"></i>
                                <span class="text-sm font-semibold">Static IP</span>
                            </label>
                            <label class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all {{ old('wan_mode') === 'bridge' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                <input type="radio" name="wan_mode" value="bridge" x-model="wanMode" {{ old('wan_mode') === 'bridge' ? 'checked' : '' }} class="sr-only">
                                <i class="fas fa-bridge"></i>
                                <span class="text-sm font-semibold">Bridge</span>
                            </label>
                        </div>
                    </div>

                    <!-- Common Fields: VLAN & Speed Profile -->
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}">VLAN Profile</label>
                            <select x-model="vlanSource" class="{{ $inputClass }} mb-2">
                                <option value="database">Pilih dari Database</option>
                                <option value="manual">Masukkan Manual</option>
                            </select>
                            <div x-show="vlanSource === 'database'">
                                <select name="vlan_id" class="{{ $inputClass }}">
                                    <option value="">Pilih VLAN</option>
                                    @foreach($vlans as $vlan)
                                    <option value="{{ $vlan->vlan_id }}" {{ old('vlan_id') == $vlan->vlan_id ? 'selected' : '' }}>
                                        {{ $vlan->vlan_id }} - {{ $vlan->nama }} {{ $vlan->purpose ? '(' . $vlan->purpose . ')' : '' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="vlanSource === 'manual'">
                                <input type="number" name="vlan_id_manual" value="{{ old('vlan_id') }}" class="{{ $inputClass }}" placeholder="VLAN ID (1-4096)" min="1" max="4096">
                            </div>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Speed Profile</label>
                            <select name="speed_profile_id" class="{{ $inputClass }}">
                                <option value="">Default (tanpa limit)</option>
                                @foreach($speedProfiles as $profile)
                                <option value="{{ $profile->id }}" {{ old('speed_profile_id') == $profile->id ? 'selected' : '' }}>
                                    {{ $profile->nama }} ({{ number_format($profile->download_speed/1000, 1) }} / {{ number_format($profile->upload_speed/1000, 1) }} Mbps)
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- PPPoE Mode Fields -->
                    <div x-show="wanMode === 'pppoe'" x-transition class="space-y-4 border border-blue-100 rounded-xl p-4 bg-blue-50/50">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-network-wired text-blue-600"></i>
                            <h3 class="text-sm font-semibold text-gray-900">Konfigurasi PPPoE</h3>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="{{ $labelClass }}">PPPoE Username <span class="text-red-500">*</span></label>
                                <input type="text" name="pppoe_username" value="{{ old('pppoe_username') }}" class="{{ $inputClass }}" placeholder="Contoh: pelanggan001">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">PPPoE Password <span class="text-red-500">*</span></label>
                                <input type="text" name="pppoe_password" value="{{ old('pppoe_password') }}" class="{{ $inputClass }}" placeholder="Password PPPoE">
                            </div>
                        </div>
                    </div>

                    <!-- DHCP Mode Fields -->
                    <div x-show="wanMode === 'dhcp'" x-transition class="space-y-4 border border-green-100 rounded-xl p-4 bg-green-50/50">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-globe text-green-600"></i>
                            <h3 class="text-sm font-semibold text-gray-900">Konfigurasi DHCP</h3>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                            <p class="text-xs text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Mode DHCP akan otomatis mendapatkan IP dari server DHCP. Pastikan VLAN dan Speed Profile sudah diatur dengan benar.
                            </p>
                        </div>
                    </div>

                    <!-- Static IP Mode Fields -->
                    <div x-show="wanMode === 'static'" x-transition class="space-y-4 border border-purple-100 rounded-xl p-4 bg-purple-50/50">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-server text-purple-600"></i>
                            <h3 class="text-sm font-semibold text-gray-900">Konfigurasi Static IP</h3>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="{{ $labelClass }}">IP Address <span class="text-red-500">*</span></label>
                                <input type="text" name="static_ip" value="{{ old('static_ip') }}" class="{{ $inputClass }}" placeholder="192.168.1.100" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Subnet Mask <span class="text-red-500">*</span></label>
                                <input type="text" name="static_subnet" value="{{ old('static_subnet') }}" class="{{ $inputClass }}" placeholder="255.255.255.0" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Gateway <span class="text-red-500">*</span></label>
                                <input type="text" name="static_gateway" value="{{ old('static_gateway') }}" class="{{ $inputClass }}" placeholder="192.168.1.1" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">DNS 1</label>
                                <input type="text" name="static_dns1" value="{{ old('static_dns1', '8.8.8.8') }}" class="{{ $inputClass }}" placeholder="8.8.8.8" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">DNS 2</label>
                                <input type="text" name="static_dns2" value="{{ old('static_dns2', '8.8.4.4') }}" class="{{ $inputClass }}" placeholder="8.8.4.4" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
                            </div>
                        </div>
                    </div>

                    <!-- Bridge Mode Fields -->
                    <div x-show="wanMode === 'bridge'" x-transition class="space-y-4 border border-gray-100 rounded-xl p-4 bg-gray-50/50">
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fas fa-bridge text-gray-600"></i>
                            <h3 class="text-sm font-semibold text-gray-900">Konfigurasi Bridge</h3>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                            <p class="text-xs text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Mode Bridge akan meneruskan traffic langsung tanpa NAT. Pastikan VLAN sudah diatur dengan benar.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('onus.index') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>Simpan & Provisioning</button>
                </div>
            </div>

            <div class="space-y-6">
                <div class="app-card">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Ringkasan OLT</h3>
                    @if($selectedOlt)
                    <dl class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between"><dt>Nama</dt><dd class="font-semibold text-gray-900">{{ $selectedOlt->nama }}</dd></div>
                        <div class="flex justify-between"><dt>IP Address</dt><dd class="font-mono">{{ $selectedOlt->ip_address }}</dd></div>
                        <div class="flex justify-between"><dt>Vendor</dt><dd>{{ $selectedOlt->vendor ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt>Status</dt><dd><span class="badge {{ $selectedOlt->status === 'online' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($selectedOlt->status) }}</span></dd></div>
                        <div class="flex justify-between"><dt>ONU Aktif</dt><dd>{{ $selectedOlt->onu_terhubung }} / {{ $selectedOlt->total_ports }} port</dd></div>
                    </dl>
                    @else
                    <p class="text-xs text-gray-500">Pilih OLT terlebih dahulu untuk melihat detail.</p>
                    @endif
                </div>

                <div class="app-card space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800">Auto Detect</h3>
                        <form method="GET" action="{{ route('onus.register') }}">
                            <button class="btn-secondary px-3 py-1 text-xs"><i class="fas fa-sync mr-1"></i>Refresh</button>
                        </form>
                    </div>
                    @if(!empty($unconfiguredOnus))
                    <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
                        @foreach($unconfiguredOnus as $item)
                        <div class="border border-gray-100 rounded-xl p-3">
                            <div class="flex items-center justify-between text-sm font-semibold text-gray-900">
                                <span>{{ $item['serial_number'] }}</span>
                                <span class="text-xs text-gray-400">{{ $item['card'] ?? '-' }}/{{ $item['port'] ?? '-' }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $item['olt_name'] }} · {{ $item['vendor'] ?? 'Unknown' }}</p>
                            <div class="mt-3 flex justify-between text-xs text-gray-500">
                                <span>Signal: {{ $item['signal'] ?? '-' }}</span>
                                <a href="{{ route('onus.register', array_filter([
                                    'olt_id' => $item['olt_id'],
                                    'serial_number' => $item['serial_number'],
                                    'card' => $item['card'] ?? null,
                                    'port' => $item['port'] ?? null,
                                    'vendor' => $item['vendor'] ?? null,
                                    'model' => $item['model'] ?? null,
                                ])) }}" class="text-blue-600 font-semibold hover:underline">Gunakan</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-gray-500">Belum ada ONU baru yang terdeteksi. Klik refresh setelah perangkat dinyalakan.</p>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Guide Modal -->
<div x-data="{ show: false }" 
     x-show="show" 
     @open-guide-onus-register.window="show = true" 
     @keydown.escape.window="show = false"
     class="relative z-50" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    
    <div x-show="show" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="show" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 @click.away="show = false"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-book text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Panduan Registrasi ONU</h3>
                            
                            <div class="mt-4 text-sm text-gray-600 space-y-6 text-left max-h-[60vh] overflow-y-auto pr-2">
                                
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                                    <p class="text-xs text-blue-800">
                                        <strong>Pro Tip:</strong> Gunakan fitur <strong>"Auto Detect"</strong> di panel kanan untuk mendeteksi ONU yang baru dipasang secara otomatis tanpa mengetik Serial Number manual.
                                    </p>
                                </div>

                                <!-- Step 1: Identifikasi -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs mr-2">1</span>
                                        Identifikasi Perangkat
                                    </h4>
                                    <ul class="text-xs text-gray-700 space-y-2 ml-8 list-disc">
                                        <li><strong>OLT:</strong> Pilih OLT tempat ONU terhubung.</li>
                                        <li><strong>Serial Number (SN):</strong> ID unik perangkat. 
                                            <ul class="ml-4 mt-1 text-gray-500">
                                                <li>ZTE: Format `ZTEGCxxxxx` atau `ZTEG12345678`</li>
                                                <li>Huawei: Format `48575443xxxxx` (HEX) atau `HWTCxxxxx`</li>
                                            </ul>
                                        </li>
                                        <li><strong>Interface:</strong> Pastikan memilih Card dan Port yang benar (sesuai kabel fisik).</li>
                                    </ul>
                                </div>

                                <!-- Step 2: Konfigurasi Layanan -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs mr-2">2</span>
                                        Konfigurasi Layanan (Service Profile)
                                    </h4>
                                    <div class="grid gap-3 ml-8">
                                        <div class="bg-gray-50 p-2 rounded border border-gray-200">
                                            <p class="text-xs font-bold text-gray-800 mb-1">Mode PPPoE (Recommended)</p>
                                            <p class="text-xs text-gray-600">ONU akan melakukan dial-up ke MikroTik/Router.</p>
                                            <ul class="list-disc ml-4 mt-1 text-xs text-gray-500">
                                                <li>Wajib isi <strong>VLAN ID</strong> (sesuai setting MikroTik)</li>
                                                <li>Wajib isi <strong>Username & Password</strong> PPPoE</li>
                                            </ul>
                                        </div>
                                        <div class="bg-gray-50 p-2 rounded border border-gray-200">
                                            <p class="text-xs font-bold text-gray-800 mb-1">Mode Bridge</p>
                                            <p class="text-xs text-gray-600">ONU hanya sebagai media converter. Dial-up dilakukan di Router user.</p>
                                            <ul class="list-disc ml-4 mt-1 text-xs text-gray-500">
                                                <li>Cukup isi <strong>VLAN ID</strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Troubleshooting -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs mr-2">3</span>
                                        Troubleshooting Gagal Registrasi
                                    </h4>
                                    <ul class="text-xs text-red-700 space-y-1 ml-8 list-disc">
                                        <li><strong>Error "ONU already exists":</strong> SN sudah terdaftar. Cek menu "Data Master > ONU".</li>
                                        <li><strong>Error "LOS":</strong> Cek redaman kabel (idealnya -18dBm s/d -25dBm).</li>
                                        <li><strong>Status "Offline":</strong> Pastikan ONU menyala dan kabel fiber terpasang benar.</li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" @click="show = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Tutup</button>
                    <!-- <a href="https://github.com/minaot/minaot" target="_blank" class="hidden sm:inline-flex mr-3 mt-3 w-full justify-center rounded-xl bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm ring-1 ring-inset ring-blue-200 hover:bg-blue-100 sm:mt-0 sm:w-auto items-center">
                        <i class="fas fa-external-link-alt mr-2"></i> Ref. Hardwar
                    </a> -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pelangganOdpMap = @json($pelanggans->pluck('odp_id', 'id'));
        window.pelangganOdpMapRegister = pelangganOdpMap;

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
            const odpSelectTarget = wrapper.dataset.odpSelect ? document.querySelector(wrapper.dataset.odpSelect) : null;

            const setInputDisplay = (option) => {
                if (!option) return;
                const subtitle = option.subtitle ? ` • ${option.subtitle}` : '';
                input.value = `${option.label}${subtitle}`;
            };

            const selectOption = (option) => {
                hiddenInput.value = option.value;
                hiddenInput.dispatchEvent(new Event('change'));
                setInputDisplay(option);

                if (odpSelectTarget) {
                    const mappedOdp = pelangganOdpMap[option.value] ?? option.odp_id ?? '';
                    odpSelectTarget.value = mappedOdp;
                    odpSelectTarget.dispatchEvent(new Event('change'));
                }

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
                    setInputDisplay(selectedOption);
                }
            }
        };

        document.querySelectorAll('[data-select-search]').forEach(initSelectSearch);

        const pelangganHiddenInput = document.querySelector('[data-select-search-value][name="pelanggan_id"]');
        const odpSelect = document.querySelector('#register-odp-select');

        const setOdpByPelanggan = (pelangganId) => {
            if (!odpSelect) return;
            if (!pelangganId) {
                odpSelect.value = '';
                return;
            }
            const mapped = pelangganOdpMap[pelangganId];
            odpSelect.value = mapped ?? '';
        };

        if (pelangganHiddenInput && odpSelect) {
            pelangganHiddenInput.addEventListener('change', () => setOdpByPelanggan(pelangganHiddenInput.value));
            setOdpByPelanggan(pelangganHiddenInput.value);
        }
    });
</script>
@endpush
