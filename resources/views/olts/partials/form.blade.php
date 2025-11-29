@php($currentOlt = $olt ?? null)
@php($inputClass = 'w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500')
@php($labelClass = 'block text-xs font-semibold text-gray-700 mb-2')

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="{{ $labelClass }}">Kode OLT</label>
        <input type="text" name="kode_olt" value="{{ old('kode_olt', $currentOlt?->kode_olt) }}" class="{{ $inputClass }}" required>
    </div>
    <div>
        <label class="{{ $labelClass }}">Nama</label>
        <input type="text" name="nama" value="{{ old('nama', $currentOlt?->nama) }}" class="{{ $inputClass }}" required>
    </div>
    <div>
        <label class="{{ $labelClass }}">IP Address</label>
        <input type="text" name="ip_address" value="{{ old('ip_address', $currentOlt?->ip_address) }}" class="{{ $inputClass }}" required>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="{{ $labelClass }}">Port</label>
            <input type="number" name="port" value="{{ old('port', $currentOlt?->port ?? 161) }}" class="{{ $inputClass }}" min="1" max="65535">
        </div>
        <div>
            <label class="{{ $labelClass }}">SNMP Community</label>
            <input type="text" name="snmp_community" value="{{ old('snmp_community', $currentOlt?->snmp_community ?? 'public') }}" class="{{ $inputClass }}">
        </div>
    </div>
    
    <!-- SNMP Version - Only show if SNMP connection type -->
    <div x-show="connectionType === 'snmp'" x-cloak>
        <label class="{{ $labelClass }}">SNMP Version</label>
        <select name="snmp_version" class="{{ $inputClass }}">
            <option value="1" {{ old('snmp_version', $currentOlt?->snmp_version ?? '2c') === '1' ? 'selected' : '' }}>SNMPv1 (Legacy)</option>
            <option value="2c" {{ old('snmp_version', $currentOlt?->snmp_version ?? '2c') === '2c' ? 'selected' : '' }}>SNMPv2c (Recommended)</option>
            <option value="3" {{ old('snmp_version', $currentOlt?->snmp_version ?? '2c') === '3' ? 'selected' : '' }}>SNMPv3 (Secure, but requires additional config)</option>
        </select>
        <div class="mt-1 space-y-1">
            <p class="text-xs text-gray-500">
                <strong>SNMPv1:</strong> Versi awal, sederhana, pakai community string (mirip password).<br>
                <strong>SNMPv2c:</strong> Versi paling umum, lebih baik dari v1, masih pakai community string. <span class="text-green-600 font-semibold">✅ Recommended untuk OLT modern.</span><br>
                <strong>SNMPv3:</strong> Versi paling aman, pakai username/password + enkripsi. (Masih dalam pengembangan - akan fallback ke v2c)
            </p>
        </div>
    </div>
    <div>
        <label class="{{ $labelClass }}">Vendor</label>
        <select name="vendor" class="{{ $inputClass }}">
            <option value="">Pilih vendor</option>
            @foreach($supportedVendors as $vendor => $info)
            <option value="{{ $vendor }}" {{ old('vendor', $currentOlt?->vendor) === $vendor ? 'selected' : '' }}>
                {{ strtoupper($vendor) }}
            </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="{{ $labelClass }}">Model</label>
        <input type="text" name="model" value="{{ old('model', $currentOlt?->model) }}" class="{{ $inputClass }}">
    </div>
    <div>
        <label class="{{ $labelClass }}">Tipe Koneksi</label>
        <select name="connection_type" x-model="connectionType" class="{{ $inputClass }}" required>
            @foreach(['snmp','telnet','ssh','api'] as $type)
            <option value="{{ $type }}" {{ old('connection_type', $currentOlt?->connection_type) === $type ? 'selected' : '' }}>
                {{ strtoupper($type) }}
            </option>
            @endforeach
        </select>
    </div>
    
    <!-- Username & Password - Only show if NOT SNMP -->
    <div x-show="connectionType !== 'snmp'" x-cloak class="grid grid-cols-2 gap-4">
        <div>
            <label class="{{ $labelClass }}">Username <span class="text-red-500" x-show="connectionType !== 'snmp'">*</span></label>
            <input type="text" name="username" value="{{ old('username', $currentOlt?->username) }}" class="{{ $inputClass }}" :required="connectionType !== 'snmp'">
        </div>
        <div>
            <label class="{{ $labelClass }}">Password <span class="text-red-500" x-show="connectionType !== 'snmp'">*</span></label>
            <input type="password" name="password" class="{{ $inputClass }}" placeholder="{{ $currentOlt ? 'Kosongkan jika tidak berubah' : '' }}" :required="connectionType !== 'snmp'">
        </div>
    </div>
    
    <!-- API Endpoint - Only show if API -->
    <div x-show="connectionType === 'api'" x-cloak>
        <label class="{{ $labelClass }}">API Endpoint <span class="text-red-500">*</span></label>
        <input type="text" name="api_endpoint" value="{{ old('api_endpoint', $currentOlt?->api_endpoint) }}" class="{{ $inputClass }}" required>
        <p class="text-xs text-gray-500 mt-1">Format: http://192.168.1.100/api atau https://olt.example.com/api</p>
    </div>
    <div>
        <label class="{{ $labelClass }}">Total Port</label>
        <input type="number" name="total_ports" value="{{ old('total_ports', $currentOlt?->total_ports) }}" class="{{ $inputClass }}" min="0">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="{{ $labelClass }}">Latitude</label>
            <input type="text" name="latitude" value="{{ old('latitude', $currentOlt?->latitude) }}" class="{{ $inputClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}">Longitude</label>
            <input type="text" name="longitude" value="{{ old('longitude', $currentOlt?->longitude) }}" class="{{ $inputClass }}">
        </div>
    </div>
</div>

<div>
    <label class="{{ $labelClass }}">Alamat/Lokasi</label>
    <textarea name="alamat" class="{{ $inputClass }}" rows="3">{{ old('alamat', $currentOlt?->alamat) }}</textarea>
</div>
<div>
    <label class="{{ $labelClass }}">Deskripsi</label>
    <textarea name="description" class="{{ $inputClass }}" rows="3">{{ old('description', $currentOlt?->description) }}</textarea>

