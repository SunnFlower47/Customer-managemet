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

    <title>Welcome - WiFi Customer Management</title>

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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .smooth-transition {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($companyProfile && $companyProfile->logo_path)
                            <img src="{{ $companyProfile->logo_url }}"
                                 alt="{{ $companyProfile->nama_perusahaan }}"
                                 class="h-10 w-10 object-contain">
                        @else
                            <div class="text-xl font-bold text-gray-900">
                                snflr
                            </div>
                        @endif
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">{{ $companyProfile->nama_perusahaan ?? 'BCM' }}</h1>
                            <p class="text-xs text-gray-500">WiFi Management</p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 smooth-transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Hero Section -->
            <section class="py-20 px-4 sm:px-6 lg:px-8">
                <div class="max-w-4xl mx-auto text-center">
                    @if($companyProfile && $companyProfile->logo_path)
                        <img src="{{ $companyProfile->logo_url }}"
                             alt="{{ $companyProfile->nama_perusahaan }}"
                             class="h-32 w-32 object-contain mx-auto mb-8">
                    @else
                        <div class="text-6xl font-bold text-gray-900 mb-8">
                            snflr
                        </div>
                    @endif

                    <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">
                        {{ $companyProfile->nama_perusahaan ?? 'BCM' }}
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        WiFi Billing Management System
                    </p>
                    <p class="text-lg text-gray-500 mb-12 max-w-2xl mx-auto">
                        Sistem manajemen pelanggan WiFi yang lengkap dan terintegrasi untuk mengelola data pelanggan, pembayaran, paket, dan laporan keuangan dengan mudah dan efisien.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center px-8 py-3 bg-blue-600 text-white text-base font-semibold rounded-lg hover:bg-blue-700 smooth-transition shadow-md hover:shadow-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Dashboard
                        </a>
                        <a href="#features"
                           class="inline-flex items-center justify-center px-8 py-3 bg-white text-gray-700 text-base font-semibold rounded-lg border-2 border-gray-300 hover:border-gray-400 smooth-transition">
                            <i class="fas fa-info-circle mr-2"></i>Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Fitur Utama</h2>
                        <p class="text-lg text-gray-600">Kelola bisnis WiFi Anda dengan lebih mudah dan efisien</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature 1 -->
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:shadow-lg smooth-transition">
                            <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Manajemen Pelanggan</h3>
                            <p class="text-gray-600">Kelola data pelanggan, paket, dan status dengan mudah. Pantau semua informasi pelanggan dalam satu tempat.</p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:shadow-lg smooth-transition">
                            <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Pembayaran & Tagihan</h3>
                            <p class="text-gray-600">Kelola pembayaran pelanggan, tagihan bulanan, dan riwayat transaksi dengan sistem yang terintegrasi.</p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:shadow-lg smooth-transition">
                            <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-map text-purple-600 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Mapping & ODP</h3>
                            <p class="text-gray-600">Visualisasi lokasi pelanggan dan ODP pada peta interaktif untuk manajemen infrastruktur yang lebih baik.</p>
                        </div>

                        <!-- Feature 4 -->
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:shadow-lg smooth-transition">
                            <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-chart-bar text-yellow-600 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Laporan Keuangan</h3>
                            <p class="text-gray-600">Generate laporan pendapatan, pengeluaran, dan laba rugi secara otomatis untuk analisis keuangan.</p>
                        </div>

                        <!-- Feature 5 -->
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:shadow-lg smooth-transition">
                            <div class="h-12 w-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-ticket-alt text-red-600 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Customer Portal</h3>
                            <p class="text-gray-600">Portal khusus pelanggan untuk melihat tagihan, riwayat pembayaran, dan membuat tiket support.</p>
                        </div>

                        <!-- Feature 6 -->
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:shadow-lg smooth-transition">
                            <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                                <i class="fas fa-shield-alt text-indigo-600 text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Keamanan & Audit</h3>
                            <p class="text-gray-600">Sistem keamanan yang kuat dengan audit trail untuk melacak semua aktivitas dan perubahan data.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-4 md:mb-0">
                        <p class="text-sm text-gray-600">
                            © {{ date('Y') }} {{ $companyProfile->nama_perusahaan ?? 'BCM' }}. All rights reserved.
                        </p>
                    </div>
                    <div class="flex items-center gap-6">
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-blue-600 smooth-transition">
                            Login
                        </a>
                        <span class="text-gray-300">|</span>
                        <span class="text-sm text-gray-500">WiFi Billing Management System</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>

