@php
$inputClass = 'w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
$labelClass = 'block text-xs font-semibold text-gray-700 mb-2';

// Get config from service or auto-detect from OLT
$onuConfig = $onuConfig ?? [];
$autoDetectConfig = $onuConfig['config'] ?? [];

// WiFi config: use service config, or auto-detect from OLT, or empty
$wifiConfig = old('wifi', $service->wifi_config ?? $autoDetectConfig['wifi'] ?? []);

// LAN config: use service config, or auto-detect from OLT, or empty
$lanConfig = old('lan_ports', $service->lan_port_config ?? $autoDetectConfig['lan_ports'] ?? []);

// LAN ports: auto-detect from OLT if available, otherwise use default
$detectedLanPorts = $autoDetectConfig['lan_ports'] ?? [];
$lanPorts = ['lan1' => 'LAN 1', 'lan2' => 'LAN 2', 'lan3' => 'LAN 3', 'lan4' => 'LAN 4'];

// If LAN ports detected from OLT, show only detected ports
if (!empty($detectedLanPorts)) {
    $lanPorts = array_intersect_key($lanPorts, $detectedLanPorts);
    // Merge detected ports with default to ensure all 4 ports are available
    $lanPorts = array_merge(['lan1' => 'LAN 1', 'lan2' => 'LAN 2', 'lan3' => 'LAN 3', 'lan4' => 'LAN 4'], $lanPorts);
}
@endphp

<div class="space-y-4" x-data="{ wanMode: '{{ old('wan_mode', $service->wan_mode ?? 'pppoe') }}', vlanSource: '{{ old('vlan_id') ? 'database' : 'manual' }}', wifiEnabled: {{ old('wifi.enabled', data_get($wifiConfig, 'enabled', true)) ? 'true' : 'false' }} }">
    <!-- Service ID (only for create) -->
    @if(!isset($service))
    <div>
        <label class="{{ $labelClass }}">Service ID <span class="text-red-500">*</span></label>
        <select name="service_id" class="{{ $inputClass }}" required>
            @foreach($availableServiceIds ?? [1,2,3,4] as $sid)
            <option value="{{ $sid }}" {{ old('service_id') == $sid ? 'selected' : '' }}>Service {{ $sid }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <!-- Mode WAN Selection -->
    <div>
        <label class="{{ $labelClass }}">Mode WAN <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <label class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all {{ old('wan_mode', $service->wan_mode ?? 'pppoe') === 'pppoe' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                <input type="radio" name="wan_mode" value="pppoe" x-model="wanMode" {{ old('wan_mode', $service->wan_mode ?? 'pppoe') === 'pppoe' ? 'checked' : '' }} class="sr-only">
                <i class="fas fa-network-wired"></i>
                <span class="text-sm font-semibold">PPPoE</span>
            </label>
            <label class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all {{ old('wan_mode', $service->wan_mode ?? '') === 'dhcp' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                <input type="radio" name="wan_mode" value="dhcp" x-model="wanMode" {{ old('wan_mode', $service->wan_mode ?? '') === 'dhcp' ? 'checked' : '' }} class="sr-only">
                <i class="fas fa-globe"></i>
                <span class="text-sm font-semibold">DHCP</span>
            </label>
            <label class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all {{ old('wan_mode', $service->wan_mode ?? '') === 'static' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                <input type="radio" name="wan_mode" value="static" x-model="wanMode" {{ old('wan_mode', $service->wan_mode ?? '') === 'static' ? 'checked' : '' }} class="sr-only">
                <i class="fas fa-server"></i>
                <span class="text-sm font-semibold">Static IP</span>
            </label>
            <label class="inline-flex items-center gap-2 rounded-xl border-2 px-4 py-3 cursor-pointer transition-all {{ old('wan_mode', $service->wan_mode ?? '') === 'bridge' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                <input type="radio" name="wan_mode" value="bridge" x-model="wanMode" {{ old('wan_mode', $service->wan_mode ?? '') === 'bridge' ? 'checked' : '' }} class="sr-only">
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
                    <option value="{{ $vlan->vlan_id }}" {{ old('vlan_id', $service->vlan_id ?? '') == $vlan->vlan_id ? 'selected' : '' }}>
                        {{ $vlan->vlan_id }} - {{ $vlan->nama }} {{ $vlan->purpose ? '(' . $vlan->purpose . ')' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div x-show="vlanSource === 'manual'">
                <input type="number" name="vlan_id_manual" value="{{ old('vlan_id_manual', $service->vlan_id ?? '') }}" class="{{ $inputClass }}" placeholder="VLAN ID (1-4096)" min="1" max="4096">
            </div>
        </div>
        <div>
            <label class="{{ $labelClass }}">Speed Profile</label>
            <select name="speed_profile_id" class="{{ $inputClass }}">
                <option value="">Default (tanpa limit)</option>
                @foreach($speedProfiles as $profile)
                <option value="{{ $profile->id }}" {{ old('speed_profile_id', $service->speed_profile_id ?? '') == $profile->id ? 'selected' : '' }}>
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
                <input type="text" name="pppoe_username" value="{{ old('pppoe_username', $service->pppoe_username ?? '') }}" class="{{ $inputClass }}" placeholder="Contoh: pelanggan001">
            </div>
            <div>
                <label class="{{ $labelClass }}">PPPoE Password <span class="text-red-500">*</span></label>
                <input type="text" name="pppoe_password" value="{{ old('pppoe_password', $service->pppoe_password ?? '') }}" class="{{ $inputClass }}" placeholder="Password PPPoE">
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
                <input type="text" name="static_ip" value="{{ old('static_ip', $service->static_ip ?? '') }}" class="{{ $inputClass }}" placeholder="192.168.1.100" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
            </div>
            <div>
                <label class="{{ $labelClass }}">Subnet Mask <span class="text-red-500">*</span></label>
                <input type="text" name="static_subnet" value="{{ old('static_subnet', $service->static_subnet ?? '') }}" class="{{ $inputClass }}" placeholder="255.255.255.0" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
            </div>
            <div>
                <label class="{{ $labelClass }}">Gateway <span class="text-red-500">*</span></label>
                <input type="text" name="static_gateway" value="{{ old('static_gateway', $service->static_gateway ?? '') }}" class="{{ $inputClass }}" placeholder="192.168.1.1" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
            </div>
            <div>
                <label class="{{ $labelClass }}">DNS 1</label>
                <input type="text" name="static_dns1" value="{{ old('static_dns1', $service->static_dns1 ?? '8.8.8.8') }}" class="{{ $inputClass }}" placeholder="8.8.8.8" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
            </div>
            <div>
                <label class="{{ $labelClass }}">DNS 2</label>
                <input type="text" name="static_dns2" value="{{ old('static_dns2', $service->static_dns2 ?? '8.8.4.4') }}" class="{{ $inputClass }}" placeholder="8.8.4.4" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
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

    <!-- WiFi Settings -->
    <div class="border border-gray-100 rounded-xl p-4 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">WiFi Settings</h3>
                <p class="text-xs text-gray-500">
                    Atur SSID, security, dan band.
                    @if(!empty($autoDetectConfig['wifi']))
                        <span class="text-green-600 font-semibold">
                            <i class="fas fa-check-circle"></i> Auto-detect dari OLT
                        </span>
                    @endif
                </p>
            </div>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <span class="text-xs font-semibold text-gray-600">Aktif</span>
                <input type="checkbox" name="wifi[enabled]" value="1" x-model="wifiEnabled" class="sr-only">
                <span class="w-10 h-5 bg-gray-200 rounded-full relative" :class="wifiEnabled ? 'bg-blue-500' : 'bg-gray-200'">
                    <span class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition" :class="wifiEnabled ? 'translate-x-5' : ''"></span>
                </span>
            </label>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="{{ $labelClass }}">SSID</label>
                <input type="text" name="wifi[ssid]" value="{{ old('wifi.ssid', $wifiConfig['ssid'] ?? '') }}" class="{{ $inputClass }}" placeholder="Nama WiFi">
            </div>
            <div>
                <label class="{{ $labelClass }}">Password</label>
                <input type="text" name="wifi[password]" value="{{ old('wifi.password', $wifiConfig['password'] ?? '') }}" class="{{ $inputClass }}" placeholder="Minimal 8 karakter">
            </div>
            <div>
                <label class="{{ $labelClass }}">Security</label>
                <select name="wifi[security]" class="{{ $inputClass }}">
                    @foreach(['WPA2','WPA3','WPA2/WPA3','Open'] as $security)
                    <option value="{{ $security }}" {{ old('wifi.security', $wifiConfig['security'] ?? '') === $security ? 'selected' : '' }}>{{ $security }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Band</label>
                <select name="wifi[band]" class="{{ $inputClass }}">
                    @foreach(['2.4GHz','5GHz','Dual'] as $band)
                    <option value="{{ $band }}" {{ old('wifi.band', $wifiConfig['band'] ?? '') === $band ? 'selected' : '' }}>{{ $band }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}">Channel</label>
                <input type="number" name="wifi[channel]" value="{{ old('wifi.channel', $wifiConfig['channel'] ?? '') }}" class="{{ $inputClass }}" placeholder="1-165" min="1" max="165">
            </div>
        </div>
    </div>

    <!-- Ethernet / LAN Ports -->
    <div class="border border-gray-100 rounded-xl p-4 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Ethernet / LAN Ports</h3>
                <p class="text-xs text-gray-500">Mapping port LAN untuk layanan.</p>
            </div>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($lanPorts as $key => $label)
            @php
                $portConfig = $lanConfig[$key] ?? [];
                $detectedPort = $autoDetectConfig['lan_ports'][$key] ?? null;
                $isDetected = !empty($detectedPort);
            @endphp
            <div class="border border-gray-200 rounded-xl p-3 space-y-3 {{ $isDetected ? 'bg-green-50 border-green-200' : '' }}">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-gray-700">{{ $label }}</span>
                    <div class="flex items-center gap-2">
                        @if($isDetected)
                        <span class="text-green-600 text-[10px] font-semibold">
                            <i class="fas fa-check-circle"></i> Terdeteksi
                        </span>
                        @endif
                        <span class="text-gray-400">{{ strtoupper($key) }}</span>
                    </div>
                </div>
                <div>
                    <label class="{{ $labelClass }}">Mode</label>
                    <select name="lan_ports[{{ $key }}][mode]" class="{{ $inputClass }}">
                        <option value="">Pilih Mode</option>
                        @foreach(['internet' => 'Internet', 'iptv' => 'IPTV', 'voip' => 'VoIP', 'bridge' => 'Bridge'] as $modeValue => $modeLabel)
                        <option value="{{ $modeValue }}" {{ old("lan_ports.$key.mode", $portConfig['mode'] ?? $detectedPort['mode'] ?? '') === $modeValue ? 'selected' : '' }}>
                            {{ $modeLabel }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}">VLAN</label>
                    <input type="number" name="lan_ports[{{ $key }}][vlan]" value="{{ old("lan_ports.$key.vlan", $portConfig['vlan'] ?? $detectedPort['vlan'] ?? '') }}" class="{{ $inputClass }}" placeholder="Opsional" min="1" max="4096">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Catatan</label>
                    <input type="text" name="lan_ports[{{ $key }}][description]" value="{{ old("lan_ports.$key.description", $portConfig['description'] ?? '') }}" class="{{ $inputClass }}" placeholder="Opsional">
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Active Toggle -->
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
        <label for="is_active" class="text-sm text-gray-700">Aktifkan Service</label>
    </div>
</div>

