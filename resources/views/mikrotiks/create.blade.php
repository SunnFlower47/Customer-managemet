@extends('layouts.app')

@section('title', 'Tambah MikroTik')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-server"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-indigo-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Tambah MikroTik</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Tambahkan router MikroTik baru ke sistem</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('mikrotiks.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Panduan Koneksi -->
    <div class="app-card bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 border-2 border-blue-200" x-data="{ open: false, activeTab: 'quick' }">
        <button @click="open = !open" class="w-full flex items-center justify-between gap-2 sm:gap-4 text-left hover:opacity-90 transition py-2">
            <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-md flex-shrink-0">
                    <i class="fas fa-book-open text-base sm:text-xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 leading-tight">Panduan Koneksi & Pengamanan MikroTik</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800">IP Public / Local</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-0.5">Petunjuk integrasi API, konfigurasi firewall, dan pengamanan akses</p>
                </div>
            </div>
            <div class="flex-shrink-0 ml-2">
                <i class="fas fa-chevron-down text-blue-600 text-sm sm:text-base transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
            </div>
        </button>

        <div x-show="open" x-cloak x-transition class="mt-4 pt-4 border-t border-blue-200/80 space-y-4">
            <!-- Tabs -->
            <div class="flex flex-wrap gap-2 border-b border-blue-200 pb-2">
                <button type="button" @click="activeTab = 'quick'" :class="activeTab === 'quick' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-blue-100'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition">
                    <i class="fas fa-bolt mr-1"></i> Quick Setup
                </button>
                <button type="button" @click="activeTab = 'security'" :class="activeTab === 'security' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-blue-100'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition">
                    <i class="fas fa-shield-alt mr-1"></i> Keamanan IP Public
                </button>
                <button type="button" @click="activeTab = 'nat'" :class="activeTab === 'nat' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-blue-100'" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition">
                    <i class="fas fa-network-wired mr-1"></i> Port Forwarding / NAT
                </button>
            </div>

            <!-- Tab 1: Quick Setup -->
            <div x-show="activeTab === 'quick'" class="space-y-3 text-xs sm:text-sm text-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-white rounded-xl p-3.5 border border-blue-100 shadow-sm space-y-2">
                        <p class="font-bold text-gray-900 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs flex items-center justify-center font-bold">1</span>
                            Aktifkan Layanan API RouterOS
                        </p>
                        <p class="text-xs text-gray-600">Jalankan di Terminal MikroTik untuk mengaktifkan port API (default 8728):</p>
                        <div class="bg-slate-900 text-green-400 p-2.5 rounded-lg font-mono text-xs overflow-x-auto select-all">
                            /ip service set api disabled=no port=8728
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-3.5 border border-blue-100 shadow-sm space-y-2">
                        <p class="font-bold text-gray-900 flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs flex items-center justify-center font-bold">2</span>
                            Buat User Khusus API Billing
                        </p>
                        <p class="text-xs text-gray-600">Buat group khusus dengan hak akses terbatas & user baru:</p>
                        <div class="bg-slate-900 text-green-400 p-2.5 rounded-lg font-mono text-xs overflow-x-auto select-all">
                            /user group add name=api-group policy=api,read,write,test,password<br>
                            /user add name=api-billing password="GantiPasswordIni" group=api-group
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Keamanan IP Public -->
            <div x-show="activeTab === 'security'" class="space-y-3 text-xs sm:text-sm text-gray-700">
                <div class="bg-white rounded-xl p-4 border border-blue-100 shadow-sm space-y-3">
                    <div class="flex items-start gap-2">
                        <div class="p-2 bg-emerald-100 text-emerald-700 rounded-lg flex-shrink-0 mt-0.5">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Batasi Akses API Hanya dari IP Server Hosting / Web</h4>
                            <p class="text-xs text-gray-600 mt-1">
                                Jika MikroTik menggunakan IP Publik, <strong>sangat disarankan</strong> membatasi parameter <code class="text-blue-600 font-bold font-mono">address</code> di <code class="text-gray-800 font-mono">/ip service api</code> agar port API tidak bisa di-scan atau diakses dari sembarang IP di internet.
                            </p>
                        </div>
                    </div>

                    <div class="bg-slate-900 text-green-400 p-3 rounded-lg font-mono text-xs overflow-x-auto space-y-1 select-all">
                        <p class="text-gray-400"># Contoh membatasi akses API hanya untuk IP Server Web Anda:</p>
                        <p>/ip service set api address=IP_SERVER_HOSTING_ANDA</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 text-xs">
                        <div class="bg-blue-50 p-2.5 rounded-lg border border-blue-200">
                            <p class="font-bold text-blue-900 mb-1"><i class="fas fa-shield-alt mr-1"></i> Firewall Filter</p>
                            <p class="text-gray-600 text-[11px]">Pastikan chain input tidak memblokir koneksi dari IP hosting ke port 8728.</p>
                        </div>
                        <div class="bg-indigo-50 p-2.5 rounded-lg border border-indigo-200">
                            <p class="font-bold text-indigo-900 mb-1"><i class="fas fa-key mr-1"></i> Password Kuat</p>
                            <p class="text-gray-600 text-[11px]">Gunakan kombinasi password unik minimal 12 karakter untuk user API.</p>
                        </div>
                        <div class="bg-purple-50 p-2.5 rounded-lg border border-purple-200">
                            <p class="font-bold text-purple-900 mb-1"><i class="fas fa-random mr-1"></i> Port Custom</p>
                            <p class="text-gray-600 text-[11px]">Anda juga dapat mengganti port default 8728 ke port lain (misal: 8799).</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Port Forwarding / NAT -->
            <div x-show="activeTab === 'nat'" class="space-y-3 text-xs sm:text-sm text-gray-700">
                <div class="bg-white rounded-xl p-4 border border-blue-100 shadow-sm space-y-2">
                    <p class="font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Jika MikroTik Berada di Belakang Modem ISP / Router Lain (NAT)
                    </p>
                    <p class="text-xs text-gray-600">
                        Jika IP Public berada di Modem ONT/GPON utama dan MikroTik mendapat IP Lokal (misal 192.168.1.2):
                    </p>
                    <ol class="list-decimal list-inside ml-2 space-y-1 text-xs text-gray-700">
                        <li>Buka menu <strong>Port Forwarding / Virtual Server / DMZ</strong> pada modem utama ISP.</li>
                        <li>Forward port <strong>8728</strong> (TCP) ke IP lokal MikroTik (contoh: <code class="bg-gray-100 px-1 py-0.5 rounded font-mono text-[11px]">192.168.1.2</code>).</li>
                        <li>Gunakan <strong>IP Publik Modem</strong> pada form isian IP Address di bawah.</li>
                    </ol>
                </div>
            </div>

            <!-- Catatan Keamanan Aplikasi -->
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-xs text-emerald-900 flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-base flex-shrink-0"></i>
                <p>
                    <strong>Keamanan Aplikasi:</strong> Password MikroTik yang Anda simpan di sistem ini dienkripsi secara otomatis menggunakan algoritma <strong>AES-256</strong> dan koneksi diuji secara otomatis saat data disimpan.
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('mikrotiks.store') }}" method="POST" class="app-card space-y-6">
        @csrf

        <!-- Informasi Dasar -->
        <div>
            <h3 class="text-base font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Router <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama') border-red-500 @enderror"
                           placeholder="Contoh: Router Utama">
                    @error('nama')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">
                        Lokasi
                    </label>
                    <input type="text"
                           name="location"
                           id="location"
                           value="{{ old('location') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror"
                           placeholder="Contoh: Kantor Pusat">
                    @error('location')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Koneksi -->
        <div>
            <h3 class="text-base font-semibold text-gray-900 mb-4">Koneksi</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="ip_address" class="block text-sm font-semibold text-gray-700 mb-2">
                        IP Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="ip_address"
                           id="ip_address"
                           value="{{ old('ip_address') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono @error('ip_address') border-red-500 @enderror"
                           placeholder="192.168.1.1">
                    <p class="mt-1 text-xs text-gray-500">IP Address router MikroTik yang dapat diakses dari server</p>
                    @error('ip_address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="port" class="block text-sm font-semibold text-gray-700 mb-2">
                        Port <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="port"
                           id="port"
                           value="{{ old('port', 8728) }}"
                           required
                           min="1"
                           max="65535"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('port') border-red-500 @enderror"
                           placeholder="8728">
                    <p class="mt-1 text-xs text-gray-500">Port API RouterOS (default: 8728 untuk v6/v7)</p>
                    @error('port')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="routeros_version" class="block text-sm font-semibold text-gray-700 mb-2">
                        RouterOS Version <span class="text-red-500">*</span>
                    </label>
                    <select name="routeros_version"
                            id="routeros_version"
                            required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('routeros_version') border-red-500 @enderror">
                        <option value="v7" {{ old('routeros_version', 'v7') === 'v7' ? 'selected' : '' }}>v7</option>
                        <option value="v7.1+" {{ old('routeros_version') === 'v7.1+' ? 'selected' : '' }}>v7.1+</option>
                        <option value="v6" {{ old('routeros_version') === 'v6' ? 'selected' : '' }}>v6</option>
                    </select>
                    @error('routeros_version')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Autentikasi -->
        <div>
            <h3 class="text-base font-semibold text-gray-900 mb-4">Autentikasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="username"
                           id="username"
                           value="{{ old('username') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('username') border-red-500 @enderror"
                           placeholder="admin">
                    <p class="mt-1 text-xs text-gray-500">Username yang memiliki akses API (biasanya "admin" atau user dengan group "full")</p>
                    @error('username')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           placeholder="Masukkan password">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Deskripsi -->
        <div>
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                Deskripsi
            </label>
            <textarea name="description"
                      id="description"
                      rows="3"
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                      placeholder="Deskripsi router (opsional)">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('mikrotiks.index') }}"
               class="px-6 py-3 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition text-center">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-3 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Simpan & Test Koneksi
            </button>
        </div>
    </form>
</div>
@endsection

