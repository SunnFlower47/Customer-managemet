<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Meta Tags -->
    @php
        $companyProfile = \App\Models\CompanyProfile::first();
        $companyName = $companyProfile->display_name ?? 'BCM';
        $companyShortName = $companyProfile->short_name ?? 'BCM';
        $companyInitials = $companyProfile->initials ?? 'BCM';
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
    <link rel="manifest" href="{{ asset('manifest.json') }}?v=3.0.0">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js x-cloak CSS -->
    <style>
        [x-cloak] {
            display: none !important;
        }
        /* Ensure desktop sidebar is always visible */
        @media (min-width: 1024px) {
            #sidebar {
                display: flex !important;
            }
            #sidebar[x-cloak] {
                display: flex !important;
            }
        }
        /* Hide mobile sidebar by default */
        @media (max-width: 1023px) {
            #sidebar {
                display: none;
            }
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
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen bg-gray-50" x-data="{ sidebarOpen: false }" x-init="sidebarOpen = window.innerWidth >= 1024; window.addEventListener('resize', () => { sidebarOpen = window.innerWidth >= 1024; });">
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
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <i class="fas fa-bars h-6 w-6"></i>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1"></div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Notifications -->
                        <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                        </button>

                        <!-- Separator -->
                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

                        <!-- Profile dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()?->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()?->role ?? 'guest') }}</p>
                                </div>
                                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center shadow-sm">
                                    <span class="text-sm font-medium text-white">{{ substr(auth()->user()?->name ?? 'G', 0, 1) }}</span>
                                </div>
                            </button>

                            <!-- Dropdown menu -->
                            <div x-show="open"
                                 x-cloak
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">

                                <!-- Profile Info -->
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()?->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()?->email ?? 'guest@example.com' }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()?->role ?? 'guest') }}</p>
                                </div>

                                <!-- Settings -->
                                @can('view-settings')
                                <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Settings
                                </a>
                                @endcan

                                <!-- Divider -->
                                <div class="border-t border-gray-100"></div>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
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
            <footer class="bg-gray-50 border-t border-gray-200 mt-auto">
                <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                        <div class="flex items-center space-x-4">
                            @php
                                $companyProfile = \App\Models\CompanyProfile::first();
                            @endphp
                            {{-- <span>© {{ date('Y') }} {{ $companyProfile->nama_perusahaan ?? 'BCM' }}</span> --}}
                            <span>© 2025 SunFlower.Tech (R.A). All rights reserved.</span>
                            <span class="hidden md:inline">|</span>
                            {{-- <span>WiFi Customer Management System</span>
                            <span class="hidden md:inline">|</span> --}}
                            <span>Powered by CV. BARAYA CITRA MANDIRI</span>
                        </div>
                        <div class="mt-2 md:mt-0">
                            <span>Internal System v1.0</span>
                        </div>
                    </div>
                </div>
            </footer>
            </div>
        </div>
    </div>

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
