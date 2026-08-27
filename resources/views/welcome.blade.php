<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="WiFi Customer">

    <title>{{ ($companyProfile->nama_perusahaan ?? 'BCM') }} - WiFi Management System</title>

    <!-- Favicon -->
    @php
        $companyProfile = \App\Models\CompanyProfile::first();
        $faviconUrl = $companyProfile && $companyProfile->logo_url ? $companyProfile->logo_url : asset('icon-192x192.png');
    @endphp
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { font-family: 'Inter', sans-serif; }

        .hero-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 55%, #1e40af 100%);
        }
        .grid-pattern {
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .glass-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .feature-card { transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(37,99,235,0.35);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(37,99,235,0.45); }

        .pulse-dot { animation: pulse-ring 2s ease infinite; }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(74,222,128,0.4); }
            70%  { box-shadow: 0 0 0 10px rgba(74,222,128,0); }
            100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); }
        }
        .float-anim { animation: float 4s ease-in-out infinite; }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }
        .fade-up { opacity: 0; transform: translateY(24px); transition: all 0.6s ease; }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body class="antialiased bg-white">

    <!-- NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    @if($companyProfile && $companyProfile->logo_path)
                        <img src="{{ $companyProfile->logo_url }}"
                             alt="{{ $companyProfile->nama_perusahaan }}"
                             class="h-8 w-8 object-contain rounded-lg">
                    @else
                        <div class="h-8 w-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wifi text-white text-sm"></i>
                        </div>
                    @endif
                    <span class="text-base font-bold text-gray-900">{{ $companyProfile->nama_perusahaan ?? 'BCM' }}</span>
                </div>
                <a href="{{ route('login') }}"
                   class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl">
                    <i class="fas fa-sign-in-alt text-xs"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="grid-pattern relative min-h-screen flex items-center pt-16"
             style="background: linear-gradient(135deg, #0b1329 0%, #1e3a8a 55%, #172554 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- Left: Text -->
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-5">
                        Kelola WiFi<br>
                        <span class="text-sky-300">
                            Lebih Mudah
                        </span>
                    </h1>
                    <p class="text-blue-200 text-base sm:text-lg leading-relaxed mb-8 max-w-lg mx-auto lg:mx-0">
                        Sistem manajemen pelanggan WiFi yang lengkap — dari tagihan, pembayaran, hingga monitoring jaringan, semua dalam satu platform.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                        <a href="{{ route('login') }}"
                           class="btn-primary inline-flex items-center justify-center gap-2 px-7 py-3.5 text-base font-semibold text-white rounded-xl">
                            <i class="fas fa-sign-in-alt"></i>Masuk ke Dashboard
                        </a>
                        <a href="#fitur"
                           class="inline-flex items-center justify-center gap-2 px-7 py-3.5 text-base font-semibold text-blue-200 glass-card rounded-xl hover:bg-white/15 transition-all">
                            <i class="fas fa-th-large text-sm"></i>Lihat Fitur
                        </a>
                    </div>
                    <!-- Stats -->
                    @php
                        $totalPelanggan = \Illuminate\Support\Facades\Cache::remember('welcome_total_pelanggan', 3600, function () {
                            return \App\Models\Pelanggan::count();
                        });
                        $totalPaket = \Illuminate\Support\Facades\Cache::remember('welcome_total_paket', 3600, function () {
                            return \App\Models\Paket::count();
                        });
                    @endphp
                    <div class="flex flex-wrap gap-6 mt-10 justify-center lg:justify-start">
                        <div class="text-center">
                            <div class="text-2xl sm:text-3xl font-extrabold text-white">{{ number_format($totalPelanggan) }}</div>
                            <div class="text-xs text-blue-300 mt-0.5 font-medium">Pelanggan Terdaftar</div>
                        </div>
                        <div class="w-px bg-white/20 self-stretch"></div>
                        <div class="text-center">
                            <div class="text-2xl sm:text-3xl font-extrabold text-white">{{ number_format($totalPaket) }}</div>
                            <div class="text-xs text-blue-300 mt-0.5 font-medium">Pilihan Paket</div>
                        </div>
                        <div class="w-px bg-white/20 self-stretch"></div>
                        <div class="text-center">
                            <div class="text-2xl sm:text-3xl font-extrabold text-white">24/7</div>
                            <div class="text-xs text-blue-300 mt-0.5 font-medium">Online & Cepat</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Static visual card (no DB query - public page) -->
                <div class="hidden lg:flex justify-center items-center">
                    <div class="float-anim relative">
                        <div class="glass-card rounded-3xl p-6 w-80 shadow-2xl">
                            <div class="flex items-center justify-between mb-5">
                                <div>
                                    <p class="text-xs text-blue-300">WiFi Management</p>
                                    <p class="text-white font-bold text-sm">{{ $companyProfile->nama_perusahaan ?? 'BCM' }}</p>
                                </div>
                                <div class="h-9 w-9 bg-blue-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-wifi text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 bg-white/10 rounded-xl px-3 py-2.5">
                                    <div class="h-7 w-7 bg-blue-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-users text-blue-300 text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-2 bg-white/30 rounded-full w-3/4"></div>
                                        <div class="h-1.5 bg-white/15 rounded-full w-1/2 mt-1.5"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 bg-white/10 rounded-xl px-3 py-2.5">
                                    <div class="h-7 w-7 bg-green-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-300 text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-2 bg-white/30 rounded-full w-2/3"></div>
                                        <div class="h-1.5 bg-white/15 rounded-full w-2/5 mt-1.5"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 bg-white/10 rounded-xl px-3 py-2.5">
                                    <div class="h-7 w-7 bg-yellow-500/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-chart-line text-yellow-300 text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-2 bg-white/30 rounded-full w-4/5"></div>
                                        <div class="h-1.5 bg-white/15 rounded-full w-1/3 mt-1.5"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex justify-between text-xs text-blue-300 mb-1.5">
                                    <span>Uptime Sistem</span><span>99.9%</span>
                                </div>
                                <div class="h-1.5 bg-white/10 rounded-full">
                                    <div class="h-1.5 bg-gradient-to-r from-blue-400 to-green-400 rounded-full" style="width:99.9%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Floating badge -->
                        <div class="absolute -top-4 -right-4 glass-card rounded-2xl px-3 py-2 shadow-lg">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></span>
                                <span class="text-white text-xs font-medium">Online</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Wave divider -->
        <div class="absolute bottom-0 left-0 right-0 overflow-hidden leading-none">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" class="w-full h-12">
                <path d="M0 60L60 50C120 40 240 20 360 15C480 10 600 20 720 28C840 36 960 41 1080 38C1200 35 1320 24 1380 18L1440 12V60H0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- FEATURES -->
    <section id="fitur" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-14 fade-up">
                <span class="inline-block bg-blue-50 text-blue-600 text-xs font-semibold px-4 py-1.5 rounded-full mb-4 tracking-wide uppercase">Fitur Lengkap</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Semua yang Kamu Butuhkan</h2>
                <p class="text-gray-500 max-w-lg mx-auto">Dari manajemen pelanggan hingga monitoring jaringan, semuanya terintegrasi dalam satu sistem.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php
                $features = [
                    ['icon'=>'fas fa-users',         'color'=>'blue',   'title'=>'Manajemen Pelanggan', 'desc'=>'Kelola data pelanggan, paket, dan status. Pantau semua informasi dalam satu tampilan yang rapi.'],
                    ['icon'=>'fas fa-money-bill-wave','color'=>'green',  'title'=>'Pembayaran & Tagihan','desc'=>'Catat pembayaran, generate tagihan otomatis, dan pantau tunggakan dengan mudah.'],
                    ['icon'=>'fas fa-map-marked-alt', 'color'=>'purple', 'title'=>'Peta & Jaringan',    'desc'=>'Visualisasi ODP, ODC, dan lokasi pelanggan di peta interaktif secara real-time.'],
                    ['icon'=>'fas fa-server',         'color'=>'orange', 'title'=>'Integrasi MikroTik', 'desc'=>'Monitor status koneksi PPPoE langsung dari router MikroTik tanpa perlu login manual.'],
                    ['icon'=>'fas fa-chart-line',     'color'=>'indigo', 'title'=>'Laporan Keuangan',   'desc'=>'Laporan pendapatan harian, bulanan, dan tahunan yang siap dicetak atau diekspor.'],
                    ['icon'=>'fas fa-shield-alt',     'color'=>'red',    'title'=>'Keamanan & Audit',   'desc'=>'Sistem hak akses berlapis dengan audit trail untuk melacak setiap perubahan data.'],
                ];
                $colors = [
                    'blue'   => 'bg-blue-100 text-blue-600',
                    'green'  => 'bg-green-100 text-green-600',
                    'purple' => 'bg-purple-100 text-purple-600',
                    'orange' => 'bg-orange-100 text-orange-600',
                    'indigo' => 'bg-indigo-100 text-indigo-600',
                    'red'    => 'bg-red-100 text-red-600',
                ];
                @endphp
                @foreach($features as $f)
                <div class="feature-card bg-white border border-gray-100 rounded-2xl p-6 shadow-sm fade-up">
                    <div class="h-12 w-12 {{ $colors[$f['color']] }} rounded-xl flex items-center justify-center mb-4">
                        <i class="{{ $f['icon'] }} text-xl"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">{{ $f['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-2xl mx-auto text-center fade-up">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">Siap Mulai Sekarang?</h2>
            <p class="text-gray-500 mb-7">Login ke dashboard dan kelola bisnis WiFi Anda lebih efisien.</p>
            <a href="{{ route('login') }}"
               class="btn-primary inline-flex items-center gap-2 px-8 py-3.5 text-base font-semibold text-white rounded-xl">
                <i class="fas fa-sign-in-alt"></i>Masuk ke Dashboard
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white border-t border-gray-100 py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-sm text-gray-400">© {{ date('Y') }} {{ $companyProfile->nama_perusahaan ?? 'BCM' }}. All rights reserved.</p>
            <p class="text-sm text-gray-400">WiFi Billing Management System <span class="text-blue-500 font-semibold">v2.0</span></p>
        </div>
    </footer>

    <script>
        // Fade-up on scroll
        const fadeEls = document.querySelectorAll('.fade-up');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((e, i) => {
                if (e.isIntersecting) {
                    setTimeout(() => e.target.classList.add('visible'), i * 80);
                }
            });
        }, { threshold: 0.1 });
        fadeEls.forEach(el => observer.observe(el));
    </script>
</body>
</html>

