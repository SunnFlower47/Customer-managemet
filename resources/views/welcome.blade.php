<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="WiFi Customer Management">

    @php
        $companyProfile = \App\Models\CompanyProfile::first();
        $companyName = $companyProfile->nama_perusahaan ?? 'BCM';
        $faviconUrl = $companyProfile && $companyProfile->logo_url ? $companyProfile->logo_url : asset('icon-192x192.png');
        
        // Cache data agregat (1 jam) agar performa tetap kencang dan aman
        $totalPelanggan = \Illuminate\Support\Facades\Cache::remember('welcome_total_pelanggan', 3600, function () {
            return \App\Models\Pelanggan::count();
        });
        $totalPaket = \Illuminate\Support\Facades\Cache::remember('welcome_total_paket', 3600, function () {
            return \App\Models\Paket::count();
        });
    @endphp

    <title>{{ $companyName }} - WiFi Billing & Management System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .hero-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#cbd5e1 0.75px, transparent 0.75px), radial-gradient(#cbd5e1 0.75px, #f8fafc 0.75px);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
        }
    </style>
</head>
<body class="antialiased text-slate-800 bg-white selection:bg-blue-600 selection:text-white">

    <!-- NAVBAR -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    @if($companyProfile && $companyProfile->logo_path)
                        <img src="{{ $companyProfile->logo_url }}"
                             alt="{{ $companyName }}"
                             class="h-9 w-9 object-contain rounded-lg">
                    @else
                        <div class="h-9 w-9 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold text-sm shadow-sm group-hover:bg-blue-700 transition">
                            <i class="fas fa-wifi"></i>
                        </div>
                    @endif
                    <div>
                        <span class="text-base font-bold text-slate-900 leading-tight block tracking-tight">{{ $companyName }}</span>
                        <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider block">WiFi Management</span>
                    </div>
                </a>

                <!-- Nav Menu & Actions -->
                <div class="flex items-center gap-4">
                    <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                        <a href="#fitur" class="hover:text-blue-600 transition-colors">Fitur</a>
                        <a href="#statistik" class="hover:text-blue-600 transition-colors">Statistik</a>
                    </nav>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm hover:shadow transition-all duration-200 active:scale-[0.98]">
                        <i class="fas fa-sign-in-alt text-xs"></i>
                        <span>Login Admin</span>
                    </a>
                </div>

            </div>
        </div>
    </header>

    <main>
        <!-- HERO SECTION -->
        <section class="hero-pattern relative border-b border-slate-200/80 py-16 sm:py-24 lg:py-28 overflow-hidden">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                
                <!-- Status Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-semibold mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Sistem Billing & Jaringan Aktif</span>
                </div>

                <!-- Brand / Logo Hero -->
                @if($companyProfile && $companyProfile->logo_path)
                    <div class="mb-6 flex justify-center">
                        <img src="{{ $companyProfile->logo_url }}"
                             alt="{{ $companyName }}"
                             class="h-20 w-20 sm:h-24 sm:w-24 object-contain drop-shadow-sm">
                    </div>
                @endif

                <!-- Heading -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight mb-5">
                    Kelola Operasional WiFi<br class="hidden sm:block">
                    <span class="text-blue-600">Lebih Cepat, Rapi & Terintegrasi</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed mb-10">
                    Platform terpadu untuk administrasi data pelanggan, pembuatan invoice otomatis, monitoring pembayaran, hingga pemetaan infrastruktur jaringan {{ $companyName }}.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                    <a href="{{ route('login') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3 text-sm sm:text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-sign-in-alt text-sm"></i>
                        <span>Masuk ke Dashboard</span>
                    </a>
                    <a href="#fitur"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 text-sm sm:text-base font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl transition-all duration-200">
                        <i class="fas fa-layer-group text-slate-400 text-sm"></i>
                        <span>Lihat Fitur Utama</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- STATS BAR -->
        <section id="statistik" class="bg-white border-b border-slate-100 py-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-slate-100">
                    <div class="pt-4 md:pt-0">
                        <p class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight font-mono">{{ number_format($totalPelanggan) }}</p>
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1">Pelanggan Terdaftar</p>
                    </div>
                    <div class="pt-4 md:pt-0">
                        <p class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight font-mono">{{ number_format($totalPaket) }}</p>
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1">Pilihan Paket Layanan</p>
                    </div>
                    <div class="pt-4 md:pt-0">
                        <p class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight font-mono">24/7</p>
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1">Monitoring Uptime</p>
                    </div>
                    <div class="pt-4 md:pt-0">
                        <p class="text-2xl sm:text-4xl font-extrabold text-blue-600 tracking-tight font-mono">v2.0</p>
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1">Versi Sistem</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES SECTION -->
        <section id="fitur" class="py-16 sm:py-24 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Section Header -->
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-2">Solusi Lengkap</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Fitur Utama Sistem Manajemen</h2>
                    <p class="text-sm sm:text-base text-slate-600 mt-2">Segala kemudahan operasional dan pencatatan dalam satu platform kerja terpusat.</p>
                </div>

                <!-- Feature Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- Card 1: Manajemen Pelanggan -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-base mb-5">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Manajemen Pelanggan</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Pengelolaan data pelanggan lengkap dengan kredensial PPPoE, nomor STB, alamat, serta status langganan aktif.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center text-xs font-semibold text-blue-600">
                            <span>Data terorganisir &bull; Pencarian cepat</span>
                        </div>
                    </div>

                    <!-- Card 2: Pembayaran & Tagihan -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-base mb-5">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Tagihan & Pembayaran</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Otomatisasi generate tagihan bulanan, pencatatan bukti transfer, verifikasi pembayaran, dan cetak invoice resmi.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center text-xs font-semibold text-emerald-600">
                            <span>Kuitansi PDF &bull; Notifikasi pembayaran</span>
                        </div>
                    </div>

                    <!-- Card 3: Mapping & ODP -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-sky-300 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-11 h-11 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-base mb-5">
                                <i class="fas fa-map-location-dot"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Mapping ODP & Distribusi</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Pemetaan persebaran lokasi box ODP, jalur kabel, dan titik letak pelanggan pada peta interaktif digital.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center text-xs font-semibold text-sky-600">
                            <span>Visualisasi spasial akurat</span>
                        </div>
                    </div>

                    <!-- Card 4: Laporan Keuangan -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-300 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-base mb-5">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Laporan Keuangan</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Ringkasan pembukuan berkala, rekap pemasukan tagihan, pengeluaran operasional, dan kalkulasi laba bersih.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center text-xs font-semibold text-amber-600">
                            <span>Export Excel & PDF</span>
                        </div>
                    </div>

                    <!-- Card 5: Integrasi MikroTik & OLT -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-base mb-5">
                                <i class="fas fa-network-wired"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">MikroTik & OLT Sync</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Sinkronisasi langsung ke RouterOS dan pemantauan status ONU/ONT untuk memastikan konektivitas tetap optimal.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center text-xs font-semibold text-blue-600">
                            <span>Router sync &bull; Cek isolir</span>
                        </div>
                    </div>

                    <!-- Card 6: Keamanan & Role Access -->
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-11 h-11 bg-slate-100 text-slate-700 rounded-xl flex items-center justify-center text-base mb-5">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Hak Akses & Audit Trail</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Pembagian wewenang bertingkat (Admin, Operator, Kolektor/Penagih) dilengkapi log jejak audit aktivitas yang aman.
                            </p>
                        </div>
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center text-xs font-semibold text-slate-600">
                            <span>Keamanan akun terproteksi</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <section class="py-14 bg-white border-t border-slate-200/80">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-2xl p-8 sm:p-10 text-center text-white relative overflow-hidden"
                     style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #1d4ed8 100%);">
                    <h2 class="text-2xl sm:text-3xl font-extrabold mb-3 tracking-tight">Siap Memulai Pekerjaan Hari Ini?</h2>
                    <p class="text-sm sm:text-base text-blue-100 max-w-xl mx-auto mb-6">
                        Masuk ke panel kontrol {{ $companyName }} untuk memantau pembayaran, tiket bantuan, dan status jaringan secara real-time.
                    </p>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-900 font-bold text-sm sm:text-base rounded-xl hover:bg-blue-50 transition shadow-sm">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Buka Panel Dashboard</span>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-8 px-4 sm:px-6 lg:px-8 text-slate-500 text-xs sm:text-sm">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>
                &copy; {{ date('Y') }} <span class="font-semibold text-slate-700">{{ $companyName }}</span>. All rights reserved. &bull; <span class="text-slate-400">v2.0</span>
            </p>
            <div class="flex items-center gap-4 text-xs">
                <a href="{{ route('login') }}" class="font-medium text-slate-600 hover:text-blue-600 transition">Portal Login</a>
                <span>&bull;</span>
                <span class="text-slate-400">WiFi Customer Management</span>
            </div>
        </div>
    </footer>

</body>
</html>