@extends('layouts.app')

@section('title', 'Tambah OLT')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-plus"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tambah OLT</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Daftarkan perangkat OLT baru ke sistem monitoring</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" x-data x-on:click="$dispatch('open-guide-olt-create')" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-book-open mr-2"></i>Panduan
            </button>
            <a href="{{ route('olts.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm" x-data="{ connectionType: '{{ old('connection_type', 'snmp') }}' }">
        <form method="POST" action="{{ route('olts.store') }}" class="space-y-6">
            @csrf
            
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-3"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-800 mb-2">Terdapat kesalahan:</p>
                        <ul class="text-xs text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            @include('olts.partials.form')

            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('olts.index') }}" class="px-5 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition text-center">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Simpan & Tes Koneksi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Guide Modal -->
<div x-data="{ show: false }" 
     x-show="show" 
     @open-guide-olt-create.window="show = true" 
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
                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Panduan Konfigurasi OLT (ZTE C320/C300)</h3>
                            
                            <div class="mt-4 text-sm text-gray-600 space-y-6 text-left max-h-[60vh] overflow-y-auto pr-2">
                                
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                                    <p class="text-xs text-blue-800">
                                        <strong>Info:</strong> Panduan ini berfokus pada konfigurasi CLI untuk OLT ZTE C320/C300 agar dapat dimonitor oleh aplikasi.
                                    </p>
                                </div>

                                <!-- Step 1: SNMP -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs mr-2">1</span>
                                        Konfigurasi SNMP (Wajib)
                                    </h4>
                                    <p class="mb-2 text-xs">Untuk mengambil data status, trafik, dan sinyal ONU secara real-time.</p>
                                    <div class="bg-slate-900 rounded-lg p-3 text-xs font-mono text-green-400 overflow-x-auto shadow-inner">
                                        <div class="text-gray-500 italic mb-1"># Masuk mode konfigurasi</div>
                                        <div class="mb-1">conf t</div>
                                        <div class="text-gray-500 italic mb-1"># Setup Community String (Ganti 'public' dengan kata sandi rahasia Anda)</div>
                                        <div class="mb-1">snmp-agent community public view AllView rw</div>
                                        <div class="text-gray-500 italic mb-1"># Setup Versi & View</div>
                                        <div class="mb-1">snmp-agent view AllView 1.3.6.1 included</div>
                                        <div class="mb-1">snmp-agent sys-info version v2c</div>
                                    </div>
                                    <p class="mt-2 text-xs text-red-600">
                                        *Penting: Pastikan community string di aplikasi SAMA dengan yang di-setting di OLT.
                                    </p>
                                </div>

                                <!-- Step 2: Telnet -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs mr-2">2</span>
                                        Konfigurasi User Telnet (Wajib)
                                    </h4>
                                    <p class="mb-2 text-xs">Diperlukan untuk fitur Register ONU, Reboot, dan konfigurasi lainnya.</p>
                                    <div class="bg-slate-900 rounded-lg p-3 text-xs font-mono text-green-400 overflow-x-auto shadow-inner">
                                        <div class="text-gray-500 italic mb-1"># Buat user baru (contoh: bcm_admin)</div>
                                        <div class="mb-1">username bcm_admin password bcm_rahasia privilege 15 max-sessions 16</div>
                                        <div class="text-gray-500 italic mb-1"># Aktifkan layanan Telnet</div>
                                        <div class="mb-1">telnet server enable</div>
                                    </div>
                                </div>

                                <!-- Step 3: IP Route -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs mr-2">3</span>
                                        Pastikan Akses Jaringan
                                    </h4>
                                    <p class="mb-2 text-xs">Pastikan OLT memiliki rute (gateway) yang benar agar bisa diakses server aplikasi.</p>
                                    <div class="bg-slate-900 rounded-lg p-3 text-xs font-mono text-green-400 overflow-x-auto shadow-inner">
                                        <div class="text-gray-500 italic mb-1"># Cek IP OLT</div>
                                        <div class="mb-1">show ip-route</div>
                                        <div class="text-gray-500 italic mb-1"># Tambah default gateway (jika perlu)</div>
                                        <div class="mb-1">ip route 0.0.0.0 0.0.0.0 192.168.1.1</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                                    <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg">
                                        <h5 class="text-xs font-bold text-yellow-800 mb-1">Port Standar</h5>
                                        <ul class="text-xs text-yellow-700 list-disc ml-4">
                                            <li>SNMP: 161 (UDP)</li>
                                            <li>Telnet: 23 (TCP)</li>
                                        </ul>
                                    </div>
                                    <div class="bg-green-50 border border-green-200 p-3 rounded-lg">
                                        <h5 class="text-xs font-bold text-green-800 mb-1">Kompatibilitas</h5>
                                        <ul class="text-xs text-green-700 list-disc ml-4">
                                            <li>ZTE C320 (GTGO/GTGH)</li>
                                            <li>ZTE C300</li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" @click="show = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Tutup</button>
                    <a href="https://github.com/minaot/minaot" target="_blank" class="hidden sm:inline-flex mr-3 mt-3 w-full justify-center rounded-xl bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm ring-1 ring-inset ring-blue-200 hover:bg-blue-100 sm:mt-0 sm:w-auto items-center">
                        <i class="fas fa-external-link-alt mr-2"></i> Ref. Hardwar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
