<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    @php
        $companyProfile = \App\Models\CompanyProfile::first();
        $companyName = $companyProfile->display_name ?? 'BCM.net';
        $companyShortName = $companyProfile->short_name ?? 'PCM';
        $companyInitials = $companyProfile->initials ?? 'PCM';
        $faviconUrl = $companyProfile && $companyProfile->logo_url ? $companyProfile->logo_url : asset('icon-192x192.png');
    @endphp
    <meta name="application-name" content="{{ $companyName }} WiFi Customer">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ $companyName }} WiFi">
    <meta name="description" content="{{ $companyName }} WiFi Customer Management System - Sistem manajemen pelanggan WiFi dengan fitur PWA lengkap">
    <meta name="keywords" content="wifi billing, manajemen pelanggan, sistem billing, wifi management, billing system">
    <meta name="author" content="{{ $companyName }}">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#2563eb">
    <meta name="msapplication-TileColor" content="#2563eb">
    <meta name="msapplication-tap-highlight" content="no">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $companyName }} WiFi Customer System">
    <meta property="og:description" content="Sistem manajemen pelanggan WiFi dengan fitur PWA lengkap">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $faviconUrl }}">
    <meta property="og:site_name" content="{{ $companyName }}">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $companyName }} WiFi Customer System">
    <meta name="twitter:description" content="Sistem manajemen pelanggan WiFi dengan fitur PWA lengkap">
    <meta name="twitter:image" content="{{ $faviconUrl }}">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ $faviconUrl }}">

    <title>@yield('title', $companyName . ' WiFi Customer Management')</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js x-cloak CSS -->
    <style>
        [x-cloak] {
            display: none !important;
        }
        :root {
            --ui-bg: #f7f8fb;
            --ui-border: rgba(15, 23, 42, 0.06);
            --ui-card-radius: 1.25rem;
            --ui-card-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
        }
        body {
            background-color: var(--ui-bg);
            color: #0f172a;
        }
        .page-shell {
            padding: clamp(1rem, 4vw, 2.75rem);
        }
        .app-card {
            border-radius: var(--ui-card-radius);
            border: 1px solid var(--ui-border);
            background: #ffffff;
            box-shadow: var(--ui-card-shadow);
            padding: clamp(1rem, 2vw, 1.75rem);
        }
        .app-card--soft {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        }
        .page-header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .page-header__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .page-header__title {
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 600;
        }
        .data-table {
            width: 100%;
        }
        .data-table thead th {
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            padding: 0.9rem 1rem;
        }
        .data-table tbody td {
            padding: 1rem;
            font-size: 0.88rem;
        }
        .inline-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .mobile-card {
            border-radius: 1rem;
            border: 1px solid var(--ui-border);
            background: #ffffff;
            box-shadow: 0 14px 30px rgba(15,23,42,0.08);
            padding: clamp(0.85rem, 3vw, 1.1rem);
            gap: 0.5rem;
        }
        .stat-card {
            padding: clamp(0.75rem, 1.5vw, 1rem);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .stat-card__value {
            font-size: clamp(1.1rem, 2vw, 1.4rem);
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        .stat-card__icon {
            width: clamp(32px, 2.5vw, 36px);
            height: clamp(32px, 2.5vw, 36px);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(0.75rem, 1.5vw, 0.875rem);
        }
        @media (min-width: 1024px) {
            .stat-card {
                padding: 0.875rem;
            }
            .stat-card__value {
                font-size: 1.3rem;
            }
            .stat-card__icon {
                width: 34px;
                height: 34px;
                font-size: 0.875rem;
            }
        }
        @media (max-width: 640px) {
            .stat-card {
                padding: 0.875rem;
            }
            .stat-card__value {
                font-size: 1.15rem;
            }
            .stat-card__icon {
                width: 32px;
                height: 32px;
                font-size: 0.75rem;
            }
        }
        /* Ensure desktop sidebar is always visible */
        @media (min-width: 1024px) {
            #sidebar {
                display: flex !important;
            }
            #sidebar[x-cloak] {
                display: flex !important;
            }
            .page-shell {
                padding-top: 2.5rem;
                padding-bottom: 2.5rem;
            }
        }
        /* Hide mobile sidebar by default */
        @media (max-width: 1023px) {
            #sidebar {
                display: none;
            }
        }
        @media (max-width: 640px) {
            .page-shell {
                padding: 1rem;
            }
            .page-header__title {
                font-size: 1.35rem;
            }
            .data-table tbody td {
                padding: 0.75rem;
                font-size: 0.82rem;
            }
            .mobile-card {
                padding: 1rem;
            }
            .inline-actions {
                flex-direction: column;
            }
        }
        /* Sidebar scrollbar styling */
        #sidebar nav::-webkit-scrollbar {
            width: 6px;
        }
        #sidebar nav::-webkit-scrollbar-track {
            background: transparent;
        }
        #sidebar nav::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        #sidebar nav::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        /* Sidebar link hover effect */
        .sidebar-link {
            position: relative;
        }
        .sidebar-link:not(.bg-gradient-to-r):hover {
            transform: translateX(2px);
        }
    </style>

    <!-- Resource Hints for Performance -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Lazy Loading Script -->
    <script>
        // Lazy loading for images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        });

        // Lazy loading for charts
        const chartObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const chartElement = entry.target;
                    if (chartElement.dataset.chart) {
                        // Initialize chart when visible
                        const chartData = JSON.parse(chartElement.dataset.chart);
                        new Chart(chartElement, chartData);
                        chartObserver.unobserve(chartElement);
                    }
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const chartElements = document.querySelectorAll('[data-chart]');
            chartElements.forEach(el => chartObserver.observe(el));
        });
    </script>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                // Try multiple paths for ServiceWorker
                const swPaths = ['/sw.js', './sw.js', 'sw.js'];
                let registrationAttempts = 0;

                function tryRegister(path) {
                    navigator.serviceWorker.register(path)
                        .then(function(registration) {
                            console.log('ServiceWorker registration successful with scope: ', registration.scope);
                        })
                        .catch(function(err) {
                            registrationAttempts++;
                            if (registrationAttempts < swPaths.length) {
                                console.log('ServiceWorker registration failed for ' + path + ', trying next path...');
                                tryRegister(swPaths[registrationAttempts]);
                            } else {
                                console.log('ServiceWorker registration failed for all paths. PWA features disabled.');
                            }
                        });
                }

                tryRegister(swPaths[0]);
            });
        }
    </script>

    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen" x-data="{ sidebarOpen: false }" x-init="sidebarOpen = window.innerWidth >= 1024; window.addEventListener('resize', () => { sidebarOpen = window.innerWidth >= 1024; });">
        <!-- Layout Container -->
        <div class="flex min-h-screen">
            <!-- Desktop Sidebar -->
            <div class="hidden lg:flex lg:w-72 lg:flex-col lg:fixed lg:inset-y-0 lg:z-30 lg:h-screen">
                @include('layouts.sidebar')
            </div>

            <!-- Mobile Sidebar Overlay -->
            <div class="lg:hidden">
                @include('layouts.sidebar')
            </div>

            <!-- Main Content -->
            <div class="flex-1 lg:ml-72 min-w-0 flex flex-col min-h-screen">
            <!-- Top Bar -->
            <div class="sticky top-0 z-40 flex min-h-[64px] items-center gap-x-4 border-b border-gray-100 bg-white/95 px-4 py-3 shadow-sm backdrop-blur sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" type="button" class="-m-2.5 p-2.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition lg:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <i class="fas fa-bars h-5 w-5"></i>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1 items-center">
                        @php
                            $companyProfile = \App\Models\CompanyProfile::first();
                            $companyName = $companyProfile->display_name ?? 'snflr.net';
                        @endphp
                        <h1 class="text-lg font-bold text-gray-900 lg:hidden">{{ $companyName }}</h1>
                    </div>
                    <div class="flex items-center gap-x-3 lg:gap-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="notificationMenu()" x-init="init()" @click.outside="open = false">
                            <button type="button"
                                    class="relative p-2.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all duration-200"
                                    @click="toggle">
                                <span class="sr-only">Lihat notifikasi</span>
                                <i class="fas fa-bell h-5 w-5"></i>
                                <span x-show="total > 0"
                                      x-text="total > 9 ? '9+' : total"
                                      class="absolute -top-1 -right-1 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white shadow-lg border-2 border-white"
                                      x-cloak></span>
                            </button>

                            <div x-show="open"
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                                 class="absolute right-0 z-40 mt-3 w-[calc(100vw-2rem)] max-w-[280px] sm:w-72 md:w-80 origin-top-right rounded-2xl border border-gray-100 bg-white shadow-xl backdrop-blur-sm">
                                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 bg-gradient-to-r from-indigo-50 to-purple-50">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">Notifikasi</p>
                                        <p class="text-xs text-gray-600 mt-0.5" x-text="total > 0 ? total + ' item pending' : 'Tidak ada notifikasi baru'"></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition">Lihat semua</a>
                                        <button class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition p-1.5 hover:bg-white rounded-lg" @click="fetchNotifications">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="max-h-[70vh] overflow-y-auto">
                                    <div x-show="loading" class="p-8 text-center">
                                        <i class="fas fa-spinner fa-spin text-2xl text-indigo-500 mb-2"></i>
                                        <p class="text-sm text-gray-500">Memuat notifikasi...</p>
                                    </div>

                                    <template x-if="!loading && total === 0">
                                        <div class="p-8 text-center">
                                            <i class="fas fa-bell-slash text-3xl text-gray-300 mb-3"></i>
                                            <p class="text-sm font-medium text-gray-500">Belum ada notifikasi baru</p>
                                        </div>
                                    </template>

                                    <template x-if="!loading && tickets.items.length">
                                        <div class="px-4 py-3 space-y-2">
                                            <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wide text-gray-500 mb-2">
                                                <span class="flex items-center gap-1.5">
                                                    <i class="fas fa-ticket-alt text-orange-500"></i>
                                                    Pelaporan Tiket
                                                </span>
                                                <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[10px] font-semibold" x-text="tickets.count + ' baru'"></span>
                                            </div>
                                            <template x-for="ticket in tickets.items" :key="'ticket-'+ticket.id">
                                                <a :href="ticket.url" class="block rounded-xl border border-gray-100 p-3 hover:bg-orange-50 hover:border-orange-200 transition-all duration-200">
                                                    <p class="text-sm font-semibold text-gray-900" x-text="ticket.kode"></p>
                                                    <p class="text-xs text-gray-500 mt-0.5" x-text="ticket.pelanggan + ' • ' + ticket.created_at"></p>
                                                    <div class="mt-2 flex flex-wrap gap-2 text-[11px]">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">
                                                            <i class="fas fa-tag text-[10px]"></i>
                                                            <span x-text="ticket.kategori"></span>
                                                        </span>
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-orange-100 text-orange-700">
                                                            <i class="fas fa-exclamation-triangle text-[10px]"></i>
                                                            <span x-text="ticket.prioritas"></span>
                                                        </span>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="!loading && paymentProofs.items.length">
                                        <div class="px-4 py-3 space-y-2">
                                            <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wide text-gray-500 mb-2">
                                                <span class="flex items-center gap-1.5">
                                                    <i class="fas fa-receipt text-green-500"></i>
                                                    Bukti Pembayaran
                                                </span>
                                                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-semibold" x-text="paymentProofs.count + ' baru'"></span>
                                            </div>
                                            <template x-for="proof in paymentProofs.items" :key="'proof-'+proof.id">
                                                <a :href="proof.url" class="block rounded-xl border border-gray-100 p-3 hover:bg-green-50 hover:border-green-200 transition-all duration-200">
                                                    <p class="text-sm font-semibold text-gray-900" x-text="proof.kode_pembayaran"></p>
                                                    <p class="text-xs text-gray-500 mt-0.5" x-text="proof.pelanggan + ' • ' + proof.created_at"></p>
                                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px]">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">
                                                            <i class="fas fa-upload text-[10px]"></i>
                                                            <span x-text="proof.metode"></span>
                                                        </span>
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">
                                                            <i class="fas fa-money-bill-wave text-[10px]"></i>
                                                            <span x-text="formatCurrency(proof.jumlah)"></span>
                                                        </span>
                                                    </div>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Separator -->
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

                        <!-- Profile dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-3 px-2 py-1.5 rounded-xl hover:bg-gray-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()?->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()?->role ?? 'guest') }}</p>
                                </div>
                                <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center shadow-md ring-2 ring-white">
                                    <span class="text-sm font-bold text-white">{{ substr(auth()->user()?->name ?? 'G', 0, 1) }}</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block"></i>
                            </button>

                            <!-- Dropdown menu -->
                            <div x-show="open"
                                 x-cloak
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                                 x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                                 class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-2xl bg-white py-2 shadow-xl border border-gray-100 backdrop-blur-sm">

                                <!-- Profile Info -->
                                <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()?->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-gray-600 mt-0.5">{{ auth()->user()?->email ?? 'guest@example.com' }}</p>
                                    <span class="inline-flex items-center mt-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-100 text-indigo-700">
                                        {{ ucfirst(auth()->user()?->role ?? 'guest') }}
                                    </span>
                                </div>

                                <!-- Settings -->
                                @can('view-settings')
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors duration-200 mx-2 rounded-xl">
                                    <i class="fas fa-cog w-4 h-4 text-gray-400"></i>
                                    <span>Settings</span>
                                </a>
                                @endcan

                                <!-- Divider -->
                                <div class="border-t border-gray-100 my-1"></div>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors duration-200 mx-2 rounded-xl">
                                        <i class="fas fa-sign-out-alt w-4 h-4"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 page-shell">
                <div class="mx-auto w-full max-w-6xl lg:max-w-7xl xl:max-w-[90rem] space-y-6">
                    @if(session('success'))
                        <div class="mb-4 rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="border-t border-gray-200 mt-auto bg-transparent">
                <div class="mx-auto max-w-6xl lg:max-w-7xl xl:max-w-[90rem] px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-3 md:gap-0 text-sm text-gray-500">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0 text-center sm:text-left">
                            @php
                                $companyProfile = \App\Models\CompanyProfile::first();
                            @endphp
                            {{-- <span>© {{ date('Y') }} {{ $companyProfile->nama_perusahaan ?? 'BCM.net' }}</span> --}}
                            <span class="whitespace-nowrap">© 2025 andrin.net. All rights reserved.</span>
                            <span class="hidden sm:inline">|</span>
                            {{-- <span>WiFi Customer Management System</span>
                            <span class="hidden md:inline">|</span> --}}
                            <span class="whitespace-nowrap"><a href="https://andrin.net" target="_blank">Powered by andrin.net</a></span>
                        </div>
                        <div class="mt-0 md:mt-0 text-center sm:text-right">
                            <span class="whitespace-nowrap">Internal System v1.0</span>
                        </div>
                    </div>
                </div>
            </footer>
            </div>
        </div>
    </div>

    <!-- PWA Install Button -->
    <div x-data="pwaInstall()" x-show="showInstallButton" x-cloak class="fixed bottom-4 right-4 z-50">
        <button @click="installPWA"
                class="flex items-center gap-2 bg-sky-400 hover:bg-sky-500 text-white px-4 py-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 font-medium text-sm">
            <i class="fas fa-download"></i>
            <span class="hidden sm:inline">Install App</span>
            <span class="sm:hidden">Install</span>
        </button>
    </div>

    <script>
        function pwaInstall() {
            return {
                showInstallButton: false,
                deferredPrompt: null,
                init() {
                    // Check if already installed (standalone mode)
                    if (window.matchMedia('(display-mode: standalone)').matches ||
                        window.navigator.standalone === true) {
                        this.showInstallButton = false;
                        return;
                    }

                    // Listen for beforeinstallprompt event
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        this.deferredPrompt = e;
                        this.showInstallButton = true;
                    });

                    // Check if app is already installed
                    window.addEventListener('appinstalled', () => {
                        this.showInstallButton = false;
                        this.deferredPrompt = null;
                    });

                    // For iOS Safari
                    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                    const isInStandaloneMode = ('standalone' in window.navigator) && (window.navigator.standalone);

                    if (isIOS && !isInStandaloneMode) {
                        // Show install button for iOS
                        this.showInstallButton = true;
                    }
                },
                installPWA() {
                    if (this.deferredPrompt) {
                        // Show the install prompt
                        this.deferredPrompt.prompt();

                        // Wait for the user to respond to the prompt
                        this.deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('User accepted the install prompt');
                            } else {
                                console.log('User dismissed the install prompt');
                            }
                            this.deferredPrompt = null;
                            this.showInstallButton = false;
                        });
                    } else {
                        // For iOS or browsers that don't support beforeinstallprompt
                        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                        if (isIOS) {
                            // Show instructions for iOS
                            Swal.fire({
                                title: 'Install App',
                                html: `
                                    <p class="text-left mb-3">Untuk menginstall aplikasi di iOS:</p>
                                    <ol class="text-left list-decimal list-inside space-y-2">
                                        <li>Tap tombol <strong>Share</strong> <i class="fas fa-share"></i> di browser</li>
                                        <li>Pilih <strong>"Add to Home Screen"</strong></li>
                                        <li>Tap <strong>"Add"</strong> untuk menyelesaikan</li>
                                    </ol>
                                `,
                                icon: 'info',
                                confirmButtonText: 'Mengerti'
                            });
                        } else {
                            Swal.fire({
                                title: 'Install App',
                                text: 'Fitur install tidak tersedia di browser ini. Silakan gunakan Chrome, Edge, atau Safari.',
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                }
            }
        }
    </script>

    <script>
        function notificationMenu() {
            return {
                open: false,
                loading: true,
                loadedOnce: false,
                total: 0,
                tickets: { count: 0, items: [] },
                paymentProofs: { count: 0, items: [] },
                interval: null,
                init() {
                    this.fetchNotifications();
                    this.interval = setInterval(() => this.fetchNotifications(), 60000);
                    window.addEventListener('beforeunload', () => {
                        if (this.interval) {
                            clearInterval(this.interval);
                        }
                    });
                },
                toggle() {
                    this.open = !this.open;
                    if (this.open && !this.loadedOnce) {
                        this.fetchNotifications();
                        this.loadedOnce = true;
                    }
                },
                fetchNotifications() {
                    this.loading = true;
                    fetch('{{ route('notifications.feed') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Failed to fetch notifications');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.success) {
                                this.tickets = data.data?.tickets ?? { count: 0, items: [] };
                                this.paymentProofs = data.data?.payment_proofs ?? { count: 0, items: [] };
                                this.total = data.data?.total ?? 0;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                },
                formatCurrency(value) {
                    try {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        }).format(value ?? 0);
                    } catch (e) {
                        return 'Rp ' + (value ?? 0);
                    }
                }
            }
        }
    </script>

    @stack('scripts')

    <!-- Global SweetAlert Configuration -->
    <script>
        // Configure SweetAlert globally
        window.Swal = Swal;

        // Override browser alert with SweetAlert
        window.alert = function(message) {
            Swal.fire({
                title: 'Info',
                text: message,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        };

        // Override browser confirm with SweetAlert (async version)
        window.confirm = function(message) {
            // Return a promise that resolves to boolean
            return new Promise((resolve) => {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    resolve(result.isConfirmed);
                });
            });
        };
    </script>
</body>
</html>
