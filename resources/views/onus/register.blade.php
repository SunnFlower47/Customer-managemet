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

<x-guide-panel key="onus-register" title="Panduan Lengkap Registrasi ONU">
    <div class="space-y-4 text-sm text-gray-600">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
            <p class="font-semibold text-blue-900 mb-1"><i class="fas fa-info-circle mr-2"></i>Persiapan Sebelum Registrasi</p>
            <ul class="text-xs text-blue-800 space-y-1 ml-4 list-disc">
                <li>Pastikan ONU sudah terhubung secara fisik ke OLT (via ODP dan splitter)</li>
                <li>Siapkan <strong>Serial Number</strong> ONU (biasanya tercetak di stiker ONU)</li>
                <li>Ketahui <strong>Card</strong> dan <strong>Port</strong> OLT tempat ONU terhubung</li>
                <li>Pastikan OLT sudah terdaftar dan dapat diakses</li>
                <li>Siapkan informasi pelanggan dan ODP jika sudah ada</li>
            </ul>
        </div>

        <div>
            <p class="font-semibold text-gray-900 mb-2 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">1</span>
                Informasi Dasar ONU
            </p>
            <ul class="text-xs text-gray-700 space-y-1 ml-8">
                <li><strong>OLT:</strong> Pilih OLT tempat ONU terhubung. Pastikan OLT status "Online".</li>
                <li><strong>Serial Number:</strong> Serial number unik ONU (wajib, tidak boleh duplikat). Format biasanya: ZTEGC12345678 atau sesuai vendor.</li>
                <li><strong>Card & Port:</strong> Lokasi fisik ONU di OLT. Contoh: Card 1, Port 3 berarti ONU terhubung di port 1/1/3.</li>
                <li><strong>Nama/Internal ID:</strong> Nama untuk identifikasi internal (opsional).</li>
                <li><strong>MAC Address:</strong> MAC address ONU (opsional, untuk tracking).</li>
                <li><strong>Tipe ONT/ONU:</strong> Model ONU (contoh: ZTE-F660, Huawei HG8245H).</li>
                <li><strong>Pelanggan & ODP:</strong> Hubungkan dengan pelanggan dan ODP untuk tracking (opsional).</li>
            </ul>
            <div class="ml-8 mt-2 bg-gray-50 p-2 rounded text-xs">
                <p class="font-semibold text-gray-800 mb-1"><i class="fas fa-lightbulb mr-1"></i>Tips:</p>
                <p class="text-gray-700">Gunakan tombol <strong>"Register"</strong> di tabel "Unconfigured ONU" untuk auto-fill informasi ONU yang terdeteksi otomatis oleh OLT.</p>
            </div>
        </div>

        <div>
            <p class="font-semibold text-gray-900 mb-2 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">2</span>
                Mode WAN & Konfigurasi
            </p>
            <div class="ml-8 space-y-3">
                <div class="bg-green-50 border border-green-200 rounded p-2">
                    <p class="text-xs font-semibold text-green-900 mb-1"><i class="fas fa-network-wired mr-1"></i>PPPoE (Point-to-Point Protocol over Ethernet)</p>
                    <ul class="text-xs text-green-800 space-y-1 ml-4 list-disc">
                        <li><strong>Digunakan untuk:</strong> Koneksi internet dengan autentikasi username/password</li>
                        <li><strong>Field yang diperlukan:</strong>
                            <ul class="ml-4 mt-1 space-y-0.5 list-circle">
                                <li>PPPoE Username (wajib)</li>
                                <li>PPPoE Password (wajib)</li>
                                <li>VLAN ID (wajib)</li>
                                <li>Speed Profile (opsional, untuk limit bandwidth)</li>
                            </ul>
                        </li>
                        <li><strong>Contoh:</strong> Username: pelanggan001@isp.com, Password: rahasia123, VLAN: 100</li>
                    </ul>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded p-2">
                    <p class="text-xs font-semibold text-blue-900 mb-1"><i class="fas fa-globe mr-1"></i>DHCP (Dynamic Host Configuration Protocol)</p>
                    <ul class="text-xs text-blue-800 space-y-1 ml-4 list-disc">
                        <li><strong>Digunakan untuk:</strong> Koneksi internet dengan IP otomatis dari DHCP server</li>
                        <li><strong>Field yang diperlukan:</strong>
                            <ul class="ml-4 mt-1 space-y-0.5 list-circle">
                                <li>VLAN ID (wajib)</li>
                                <li>Speed Profile (opsional)</li>
                            </ul>
                        </li>
                        <li><strong>Catatan:</strong> Tidak perlu username/password, IP akan diberikan otomatis</li>
                    </ul>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded p-2">
                    <p class="text-xs font-semibold text-purple-900 mb-1"><i class="fas fa-server mr-1"></i>Static IP</p>
                    <ul class="text-xs text-purple-800 space-y-1 ml-4 list-disc">
                        <li><strong>Digunakan untuk:</strong> Koneksi dengan IP address tetap</li>
                        <li><strong>Field yang diperlukan:</strong>
                            <ul class="ml-4 mt-1 space-y-0.5 list-circle">
                                <li>Static IP Address (wajib, format: 192.168.1.100)</li>
                                <li>Gateway (wajib, format: 192.168.1.1)</li>
                                <li>Subnet Mask (wajib, format: 255.255.255.0 atau /24)</li>
                                <li>DNS 1 (opsional)</li>
                                <li>DNS 2 (opsional)</li>
                                <li>VLAN ID (wajib)</li>
                                <li>Speed Profile (opsional)</li>
                            </ul>
                        </li>
                        <li><strong>Contoh:</strong> IP: 192.168.1.100, Gateway: 192.168.1.1, Subnet: 255.255.255.0</li>
                    </ul>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded p-2">
                    <p class="text-xs font-semibold text-orange-900 mb-1"><i class="fas fa-bridge mr-1"></i>Bridge</p>
                    <ul class="text-xs text-orange-800 space-y-1 ml-4 list-disc">
                        <li><strong>Digunakan untuk:</strong> Mode bridge, traffic diteruskan langsung tanpa NAT</li>
                        <li><strong>Field yang diperlukan:</strong>
                            <ul class="ml-4 mt-1 space-y-0.5 list-circle">
                                <li>VLAN ID (wajib)</li>
                            </ul>
                        </li>
                        <li><strong>Catatan:</strong> Tidak ada konfigurasi IP/PPPoE, hanya VLAN tagging</li>
                    </ul>
                </div>
            </div>
        </div>

        <div>
            <p class="font-semibold text-gray-900 mb-2 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">3</span>
                VLAN & Speed Profile
            </p>
            <ul class="text-xs text-gray-700 space-y-1 ml-8">
                <li><strong>VLAN ID:</strong>
                    <ul class="ml-4 mt-1 space-y-1 list-disc">
                        <li>Pilih dari <strong>Database</strong>: Gunakan VLAN yang sudah terdaftar di sistem</li>
                        <li>Masukkan <strong>Manual</strong>: Ketik VLAN ID langsung (1-4096)</li>
                        <li>VLAN digunakan untuk segmentasi jaringan dan routing traffic</li>
                    </ul>
                </li>
                <li><strong>Speed Profile:</strong>
                    <ul class="ml-4 mt-1 space-y-1 list-disc">
                        <li>Pilih profile untuk membatasi bandwidth download/upload</li>
                        <li>Contoh: 100 Mbps download, 50 Mbps upload</li>
                        <li>Jika tidak dipilih, tidak ada limit bandwidth (unlimited)</li>
                    </ul>
                </li>
            </ul>
        </div>

        <div>
            <p class="font-semibold text-gray-900 mb-2 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">4</span>
                Proses Registrasi
            </p>
            <ol class="text-xs text-gray-700 space-y-1 ml-8 list-decimal">
                <li>Isi semua field yang diperlukan sesuai mode WAN yang dipilih</li>
                <li>Klik tombol <strong>"Simpan & Provisioning"</strong></li>
                <li>Sistem akan:
                    <ul class="ml-4 mt-1 space-y-1 list-disc">
                        <li>Validasi data yang diinput</li>
                        <li>Mengirim perintah registrasi ke OLT</li>
                        <li>Membuat record ONU di database</li>
                        <li>Mengkonfigurasi service (VLAN, PPPoE, dll) di OLT</li>
                    </ul>
                </li>
                <li>Jika berhasil, akan redirect ke halaman detail ONU</li>
                <li>Jika gagal, error message akan ditampilkan dan data tetap tersimpan untuk editing</li>
            </ol>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded mt-4">
            <p class="font-semibold text-yellow-900 mb-1"><i class="fas fa-exclamation-triangle mr-2"></i>Tips & Troubleshooting</p>
            <ul class="text-xs text-yellow-800 space-y-1 ml-4 list-disc">
                <li><strong>Serial Number sudah terdaftar:</strong> ONU mungkin sudah diregistrasi sebelumnya. Cek di menu "All ONUs".</li>
                <li><strong>Gagal registrasi ke OLT:</strong>
                    <ul class="ml-4 mt-1 space-y-0.5 list-circle">
                        <li>Pastikan OLT dapat diakses dan status "Online"</li>
                        <li>Pastikan write community string benar (untuk SNMP) atau username/password benar (untuk Telnet/SSH)</li>
                        <li>Pastikan Card dan Port benar sesuai instalasi fisik</li>
                        <li>Pastikan ONU sudah terhubung secara fisik ke OLT</li>
                    </ul>
                </li>
                <li><strong>ONU tidak muncul setelah registrasi:</strong> Tunggu beberapa detik, lalu refresh halaman atau klik "Sinkron" di halaman OLT.</li>
                <li><strong>Service tidak berfungsi:</strong> Pastikan VLAN ID benar dan sesuai dengan konfigurasi jaringan ISP.</li>
            </ul>
        </div>

        <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded mt-4">
            <p class="font-semibold text-green-900 mb-1"><i class="fas fa-check-circle mr-2"></i>Setelah Registrasi Berhasil</p>
            <ul class="text-xs text-green-800 space-y-1 ml-4 list-disc">
                <li>ONU akan muncul di menu "All ONUs" dengan status sesuai kondisi aktual</li>
                <li>Dapat menambah service tambahan (Service 2, 3, 4) di halaman detail ONU</li>
                <li>Dapat mengkonfigurasi WiFi dan LAN ports di halaman detail ONU</li>
                <li>Dapat melakukan remote actions: Reboot, Reset, Disable, Enable</li>
                <li>Dapat memantau traffic real-time di halaman detail ONU</li>
            </ul>
        </div>
    </div>
</x-guide-panel>
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

            renderResults();
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
