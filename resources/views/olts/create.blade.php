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

<x-guide-panel key="olt-create" title="Panduan Lengkap Registrasi OLT">
    <div class="space-y-4 text-sm text-gray-600">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
            <p class="font-semibold text-blue-900 mb-1"><i class="fas fa-info-circle mr-2"></i>Persiapan Sebelum Registrasi</p>
            <ul class="text-xs text-blue-800 space-y-1 ml-4 list-disc">
                <li>Pastikan OLT sudah terhubung ke jaringan dan dapat diakses dari server</li>
                <li>Siapkan kredensial akses (SNMP community, username/password untuk Telnet/SSH)</li>
                <li>Ketahui IP address manajemen OLT</li>
                <li>Pastikan firewall mengizinkan koneksi dari server ke OLT</li>
            </ul>
        </div>

        <div>
            <p class="font-semibold text-gray-900 mb-2 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">1</span>
                Informasi Dasar OLT
            </p>
            <ul class="text-xs text-gray-700 space-y-1 ml-8">
                <li><strong>Kode OLT:</strong> Kode unik untuk identifikasi (contoh: OLT-JKT-001). Harus unik dan tidak boleh duplikat.</li>
                <li><strong>Nama:</strong> Nama deskriptif OLT (contoh: OLT Jakarta Pusat).</li>
                <li><strong>IP Address:</strong> IP address manajemen OLT yang dapat diakses dari server. Format: IPv4 (contoh: 192.168.1.100).</li>
                <li><strong>Port:</strong> Port untuk koneksi (default: 161 untuk SNMP, 23 untuk Telnet, 22 untuk SSH).</li>
            </ul>
        </div>

        <div>
            <p class="font-semibold text-gray-900 mb-2 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">2</span>
                Tipe Koneksi & Kredensial
            </p>
            <div class="ml-8 space-y-2">
                <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">SNMP (Simple Network Management Protocol):</p>
                    <ul class="text-xs text-gray-700 space-y-1 ml-4 list-disc">
                        <li>Digunakan untuk monitoring dan konfigurasi via SNMP</li>
                        <li><strong>SNMP Community:</strong> Community string untuk read (default: "public") atau write (default: "private")</li>
                        <li><strong>⚠️ Security:</strong> Jangan gunakan default "public"/"private" di production! Gunakan community string yang unik dan aman.</li>
                        <li><strong>SNMP Version:</strong> Pilih v1, v2c (recommended), atau v3 (lebih aman)</li>
                        <li><strong>Port:</strong> Default 161 UDP (pastikan firewall mengizinkan)</li>
                        <li>Untuk operasi write (register ONU, reboot, dll), pastikan ada write community string di OLT</li>
                        <li>Cocok untuk: ZTE C300, ZTE C320, Huawei, Fiberhome, dan OLT modern lainnya</li>
                    </ul>
                    <div class="mt-2 p-2 bg-blue-50 rounded text-xs text-blue-800">
                        <strong>💡 Tips Testing:</strong> Sebelum menambahkan OLT, test koneksi SNMP dulu dari command line:<br>
                        <code class="bg-blue-100 px-1 rounded">snmpwalk -v2c -c public &lt;IP_OLT&gt;</code><br>
                        Jika berhasil, akan muncul banyak OID (Object ID) yang mewakili status OLT.
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">Telnet:</p>
                    <ul class="text-xs text-gray-700 space-y-1 ml-4 list-disc">
                        <li>Koneksi via command line interface</li>
                        <li>Perlu <strong>Username</strong> dan <strong>Password</strong></li>
                        <li>Cocok untuk: ZTE C320, devices dengan CLI access</li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">SSH:</p>
                    <ul class="text-xs text-gray-700 space-y-1 ml-4 list-disc">
                        <li>Koneksi secure via SSH</li>
                        <li>Perlu <strong>Username</strong> dan <strong>Password</strong></li>
                        <li>Lebih aman daripada Telnet</li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-800 mb-1">API:</p>
                    <ul class="text-xs text-gray-700 space-y-1 ml-4 list-disc">
                        <li>Koneksi via REST API</li>
                        <li>Perlu <strong>API Endpoint URL</strong>, <strong>Username</strong>, dan <strong>Password</strong></li>
                        <li>Cocok untuk: Modern OLT dengan REST API support</li>
                    </ul>
                </div>
            </div>
        </div>

        <div>
            <p class="font-semibold text-gray-900 mb-2 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">3</span>
                Vendor & Model
            </p>
            <ul class="text-xs text-gray-700 space-y-1 ml-8">
                <li><strong>Vendor:</strong> Pilih vendor OLT (ZTE, Huawei, Fiberhome, dll)</li>
                <li><strong>Model:</strong> Model spesifik OLT (contoh: C300, C320 untuk ZTE)</li>
                <li>Sistem akan otomatis menggunakan driver yang sesuai berdasarkan vendor dan model</li>
                <li>Jika vendor/model tidak didukung, gunakan "Generic" dengan connection type SNMP</li>
            </ul>
        </div>

        <div>
            <p class="font-semibold text-gray-900 mb-2 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">4</span>
                Lokasi (Opsional)
            </p>
            <ul class="text-xs text-gray-700 space-y-1 ml-8">
                <li><strong>Latitude:</strong> Koordinat lintang (-90 sampai 90)</li>
                <li><strong>Longitude:</strong> Koordinat bujur (-180 sampai 180)</li>
                <li><strong>Alamat:</strong> Alamat fisik lokasi OLT</li>
                <li>Digunakan untuk pemetaan di halaman Mapping</li>
            </ul>
        </div>

        <div>
            <p class="font-semibold text-gray-900 mb-2 flex items-center">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">5</span>
                Verifikasi & Testing
            </p>
            <ul class="text-xs text-gray-700 space-y-1 ml-8">
                <li>Setelah menyimpan, sistem akan otomatis melakukan <strong>Test Koneksi</strong></li>
                <li>Jika koneksi berhasil, OLT akan muncul di daftar dengan status "Online"</li>
                <li>Jika gagal, periksa:
                    <ul class="ml-4 mt-1 space-y-1 list-disc">
                        <li>IP address benar dan dapat diakses</li>
                        <li>Port tidak terblokir firewall</li>
                        <li>Kredensial (community/username/password) benar</li>
                        <li>Jenis koneksi sesuai dengan konfigurasi OLT</li>
                    </ul>
                </li>
                <li>Setelah berhasil, klik tombol <strong>"Sinkron"</strong> untuk mengambil data PON ports dan ONUs dari OLT</li>
            </ul>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 rounded mt-4">
            <p class="font-semibold text-yellow-900 mb-1"><i class="fas fa-exclamation-triangle mr-2"></i>Tips Penting</p>
            <ul class="text-xs text-yellow-800 space-y-1 ml-4 list-disc">
                <li>Untuk testing tanpa OLT fisik, gunakan IP <strong>127.0.0.1</strong> atau <strong>localhost</strong> (akan menggunakan mock driver)</li>
                <li>Pastikan PHP SNMP extension terinstall jika menggunakan SNMP: <code class="bg-yellow-100 px-1 rounded">php -m | grep snmp</code></li>
                <li><strong>Security:</strong> Jangan gunakan default community string ("public"/"private") di production!</li>
                <li>Untuk operasi write (register ONU, reboot, dll), pastikan write community string dikonfigurasi di OLT</li>
                <li>Pastikan port 161 UDP terbuka dari server ke OLT (cek firewall)</li>
                <li>Jangan poll terlalu cepat - gunakan rate limiting untuk menghindari overload OLT</li>
                <li>Simpan kredensial dengan aman - password akan dienkripsi di database</li>
            </ul>
        </div>
        
        <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded mt-4">
            <p class="font-semibold text-blue-900 mb-1"><i class="fas fa-book mr-2"></i>Dokumentasi Lengkap</p>
            <p class="text-xs text-blue-800">
                Untuk panduan lengkap tentang SNMP, OID, troubleshooting, dan best practices, lihat file <strong>SNMP_GUIDE.md</strong> di root project.
            </p>
        </div>
    </div>
</x-guide-panel>
@endsection
