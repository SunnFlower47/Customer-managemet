@extends('layouts.app')

@section('title', 'Tambah MikroTik')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Tambah Router</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">Hubungkan router MikroTik baru</p>
            </div>
        </div>
        <div class="page-header__actions flex gap-2">
            <button type="button" 
                    @click="$dispatch('open-modal', 'guide-modal')"
                    class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-blue-200 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition">
                <i class="fas fa-book mr-2"></i>Panduan Koneksi
            </button>
            <a href="{{ route('mikrotik.index') }}" 
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-300 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="app-card">
        <div class="border-b border-gray-100 pb-4 mb-6">
            <h3 class="text-base font-semibold text-gray-900">Informasi Koneksi</h3>
            <p class="text-sm text-gray-500 mt-1">Masukkan detail kredensial router Anda</p>
        </div>

        <form action="{{ route('mikrotik.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Identifikasi
                    </label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium"
                           placeholder="Contoh: Router Utama">
                    @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- IP & Port -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        IP Address
                    </label>
                    <input type="text" name="ip_address" value="{{ old('ip_address') }}" required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium"
                           placeholder="192.168.88.1">
                    @error('ip_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Port API
                    </label>
                    <input type="number" name="port" value="{{ old('port', 8728) }}" required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium">
                    <p class="text-xs text-gray-500 mt-1">Default: 8728</p>
                    @error('port') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Auth -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Username
                    </label>
                    <input type="text" name="username" value="{{ old('username', 'admin') }}" required 
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium">
                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'" name="password" required 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold rounded-xl hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i>Simpan & Test Koneksi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Guide Modal -->
<div x-data="{ show: false }" 
     x-show="show" 
     @open-modal.window="if ($event.detail === 'guide-modal') show = true" 
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
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-book text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Panduan Koneksi MikroTik</h3>
                            
                            <div class="mt-4 text-sm text-gray-600 space-y-6 text-left max-h-[60vh] overflow-y-auto pr-2">
                                <!-- Step 1 -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs mr-2">1</span>
                                        Aktifkan Service API
                                    </h4>
                                    <p class="mb-2 text-xs">Pastikan service API MikroTik aktif dan berjalan di port yang default (8728).</p>
                                    <div class="bg-slate-900 rounded-lg p-3 text-xs font-mono text-green-400 overflow-x-auto shadow-inner">
                                        <div class="text-gray-500 italic mb-1"># Cek status service</div>
                                        <div class="mb-2">/ip service print where name=api</div>
                                        <div class="text-gray-500 italic mb-1"># Aktifkan jika disabled</div>
                                        <div>/ip service enable api</div>
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs mr-2">2</span>
                                        Buat User Khusus API
                                    </h4>
                                    <p class="mb-2 text-xs">Disarankan membuat user khusus dengan hak akses terbatas hanya untuk integrasi ini.</p>
                                    <div class="bg-slate-900 rounded-lg p-3 text-xs font-mono text-green-400 overflow-x-auto shadow-inner">
                                        <div class="text-gray-500 italic mb-1"># Buat grup khusus (opsional)</div>
                                        <div class="mb-2">/user group add name=api_api-web policy=read,write,api,test,password</div>
                                        <div class="text-gray-500 italic mb-1"># Buat user baru (Ganti password!)</div>
                                        <div>/user add name=api-web_bot group=api_api-web password="Rahasia123!"</div>
                                    </div>
                                </div>

                                <!-- Step 3 -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs mr-2">3</span>
                                        Pengaturan IP Address (Opsional)
                                    </h4>
                                    <p class="mb-2 text-xs">Untuk keamanan tambahan, batasi akses user hanya dari IP Server aplikasi ini.</p>
                                    <div class="bg-slate-900 rounded-lg p-3 text-xs font-mono text-green-400 overflow-x-auto shadow-inner">
                                        <div class="text-gray-500 italic mb-1"># Set allowed address (contoh IP Server: 192.168.1.100)</div>
                                        <div>/user set api-web_bot address=192.168.1.100/32</div>
                                    </div>
                                </div>

                                <!-- Step 4 -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs mr-2">4</span>
                                        Cek Firewall (NAT/Filter)
                                    </h4>
                                    <p class="mb-2 text-xs">Jika aplikasi gagal terhubung, pastikan port 8728 tidak terblokir oleh Firewall Filter Rules.</p>
                                    <div class="bg-slate-900 rounded-lg p-3 text-xs font-mono text-green-400 overflow-x-auto shadow-inner">
                                        <div class="text-gray-500 italic mb-1"># Izinkan akses API di input chain (paling atas)</div>
                                        <div>/ip firewall filter add chain=input protocol=tcp dst-port=8728 action=accept place-before=1 comment="Allow API"</div>
                                    </div>
                                </div>

                                <div class="bg-indigo-50 border-l-4 border-indigo-400 p-3 rounded-r-lg">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-lightbulb text-indigo-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-xs text-indigo-700">
                                                <strong>Tips:</strong> Gunakan tombol "Test Koneksi" setelah mengisi form untuk memastikan konfigurasi sudah benar sebelum menyimpan.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" @click="show = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

