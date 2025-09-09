<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 lg:z-auto h-full flex flex-col"
     :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
     x-show="sidebarOpen"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     x-init="
        // Initialize sidebar state based on screen size
        if (window.innerWidth >= 1024) {
            sidebarOpen = true;
        } else {
            sidebarOpen = false;
        }

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebarOpen = true;
            } else {
                sidebarOpen = false;
            }
        });
     "
     style="display: flex;">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
        @php
            $companyProfile = \App\Models\CompanyProfile::first();
            $companyName = $companyProfile->display_name ?? 'BCM';
            $companyShortName = $companyProfile->short_name ?? 'BCM';
        @endphp
        <div class="flex items-center">
            @if($companyProfile && $companyProfile->logo_path)
                <img src="{{ $companyProfile->logo_url }}?v={{ time() }}" alt="{{ $companyProfile->nama_perusahaan }}" class="w-16 h-16 rounded-lg mr-4 object-contain">
            @else
                <div class="w-16 h-16 bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">{{ $companyProfile->initials ?? 'BCM' }}</span>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <h1 class="text-xs font-bold text-gray-900 leading-tight break-words">
                    {{ $companyName }}
                </h1>

                <p class="text-xs text-gray-500">WiFi Customer Management</p>
            </div>
        </div>
        <!-- Close button for mobile -->
        <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 mt-6 px-3 overflow-y-auto">
        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="sidebar-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                </svg>
                Dashboard
            </a>

            <!-- Pelanggan -->
            @can('view-pelanggan')
            <a href="{{ route('pelanggans.index') }}" class="sidebar-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pelanggans.*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('pelanggans.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Pelanggan
            </a>
            @endcan

            <!-- Pembayaran -->
            @can('view-pembayaran')
            <a href="{{ route('pembayarans.index') }}" class="sidebar-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pembayarans.*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('pembayarans.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Pembayaran
            </a>
            @endcan

            <!-- Paket -->
            @can('view-paket')
            <a href="{{ route('pakets.index') }}" class="sidebar-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pakets.*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('pakets.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Paket
            </a>
            @endcan

            <!-- Penagih -->
            @can('view-penagih')
            <a href="{{ route('penagihs.index') }}" class="sidebar-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('penagihs.*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('penagihs.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Penagih
            </a>
            @endcan

            <!-- Pengeluaran -->
            @can('view-pengeluaran')
            <a href="{{ route('pengeluarans.index') }}" class="sidebar-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pengeluarans.*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('pengeluarans.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                Pengeluaran
            </a>
            @endcan

            <!-- Laporan Dropdown -->
            @canany(['view-laporan-pendapatan', 'view-laporan-pengeluaran', 'view-laporan-laba-rugi'])
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="sidebar-link group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('laporan.*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('laporan.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Laporan
                    </div>
                    <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-cloak x-transition class="ml-6 mt-1 space-y-1">
                    @can('view-laporan-pendapatan')
                    <a href="{{ route('laporan.pendapatan') }}" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors {{ request()->routeIs('laporan.pendapatan') ? 'bg-primary-50 text-primary-600' : '' }}">
                        <i class="fas fa-arrow-up text-green-600 mr-3"></i>
                        Pendapatan
                    </a>
                    @endcan
                    @can('view-laporan-pengeluaran')
                    <a href="{{ route('laporan.pengeluaran') }}" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors {{ request()->routeIs('laporan.pengeluaran') ? 'bg-primary-50 text-primary-600' : '' }}">
                        <i class="fas fa-arrow-down text-red-600 mr-3"></i>
                        Pengeluaran
                    </a>
                    @endcan
                    @can('view-laporan-laba-rugi')
                    <a href="{{ route('laporan.laba-rugi') }}" class="group flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900 transition-colors {{ request()->routeIs('laporan.laba-rugi') ? 'bg-primary-50 text-primary-600' : '' }}">
                        <i class="fas fa-calculator text-blue-600 mr-3"></i>
                        Laba Rugi
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- Users (Admin Only) -->
            @can('view-user')
            <a href="{{ route('users.index') }}" class="sidebar-link group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('users.*') ? 'bg-primary-50 text-primary-600 border-r-2 border-primary-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('users.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                Users
            </a>
            @endcan

        </div>
    </nav>

</div>

<!-- Mobile sidebar overlay -->
<div x-show="sidebarOpen"
     x-cloak
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
     @click="sidebarOpen = false"></div>
