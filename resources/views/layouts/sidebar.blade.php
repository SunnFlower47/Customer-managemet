<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 lg:z-auto h-full flex flex-col border-r border-gray-100"
     :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
     x-cloak
     style="display: flex;">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100 bg-sky-50">
        @php
            $companyProfile = \App\Models\CompanyProfile::first();
            $companyName = $companyProfile->display_name ?? 'BCM.net';
            $companyShortName = $companyProfile->short_name ?? 'BCM';
        @endphp
        <div class="flex items-center gap-3 flex-1 min-w-0">
            @if($companyProfile && $companyProfile->logo_path)
                <img src="{{ $companyProfile->logo_url }}?v={{ time() }}" alt="{{ $companyProfile->nama_perusahaan }}" class="w-10 h-10 rounded-xl object-contain flex-shrink-0">
            @else
                <div class="w-10 h-10 bg-sky-400 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                    <span class="text-white font-bold text-sm">{{ $companyProfile->initials ?? 'BCM' }}</span>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <h1 class="text-sm font-bold text-gray-900 leading-tight truncate">
                    {{ $companyName }}
                </h1>
                <p class="text-[10px] text-gray-500 truncate">WiFi Management</p>
            </div>
        </div>
        <!-- Close button for mobile -->
        <button @click="sidebarOpen = false" class="lg:hidden p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-white transition flex-shrink-0">
            <i class="fas fa-times text-sm"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto">
        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-home w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">Dashboard</span>
                @if(request()->routeIs('dashboard'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>

            <!-- Pelanggan -->
            @can('view-pelanggan')
            <a href="{{ route('pelanggans.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('pelanggans.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-users w-5 h-5 flex-shrink-0 {{ request()->routeIs('pelanggans.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">Pelanggan</span>
                @if(request()->routeIs('pelanggans.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>
            @endcan

            <!-- Pembayaran -->
            @can('view-pembayaran')
            <a href="{{ route('pembayarans.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('pembayarans.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-money-bill-wave w-5 h-5 flex-shrink-0 {{ request()->routeIs('pembayarans.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">Pembayaran</span>
                @if(request()->routeIs('pembayarans.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>
            @endcan

            <!-- Paket -->
            @can('view-paket')
            <a href="{{ route('pakets.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('pakets.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-box w-5 h-5 flex-shrink-0 {{ request()->routeIs('pakets.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">Paket</span>
                @if(request()->routeIs('pakets.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>
            @endcan

            <!-- Penagih -->
            @can('view-penagih')
            <a href="{{ route('penagihs.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('penagihs.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-user-tie w-5 h-5 flex-shrink-0 {{ request()->routeIs('penagihs.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">Penagih</span>
                @if(request()->routeIs('penagihs.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>
            @endcan

            @can('view-mapping')
            <!-- Mapping -->
            <a href="{{ route('mapping.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('mapping.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-map w-5 h-5 flex-shrink-0 {{ request()->routeIs('mapping.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">Mapping</span>
                @if(request()->routeIs('mapping.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>
            @endcan

            @can('view-odp')
            <!-- ODP -->
            <a href="{{ route('odps.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('odps.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-map-marker-alt w-5 h-5 flex-shrink-0 {{ request()->routeIs('odps.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">ODP</span>
                @if(request()->routeIs('odps.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>
            <!-- ODC -->
            <a href="{{ route('odcs.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('odcs.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-project-diagram w-5 h-5 flex-shrink-0 {{ request()->routeIs('odcs.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">ODC</span>
                @if(request()->routeIs('odcs.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>
            @endcan

            <!-- MikroTik -->
            @can('view-mikrotik')
            <a href="{{ route('mikrotiks.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('mikrotiks.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-server w-5 h-5 flex-shrink-0 {{ request()->routeIs('mikrotiks.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">MikroTik</span>
                @if(request()->routeIs('mikrotiks.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>
            @endcan

            <!-- Pengeluaran -->
            @can('view-pengeluaran')
            <a href="{{ route('pengeluarans.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('pengeluarans.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-arrow-down w-5 h-5 flex-shrink-0 {{ request()->routeIs('pengeluarans.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">Pengeluaran</span>
                @if(request()->routeIs('pengeluarans.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>
            @endcan

            <!-- Laporan Dropdown -->
            @canany(['view-laporan-pendapatan', 'view-laporan-pengeluaran', 'view-laporan-laba-rugi'])
            <div x-data="{ open: {{ request()->routeIs('laporan.*') ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open" class="sidebar-link group flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('laporan.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-chart-bar w-5 h-5 flex-shrink-0 {{ request()->routeIs('laporan.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                        <span>Laporan</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 {{ request()->routeIs('laporan.*') ? 'text-blue-600' : 'text-gray-400' }}" :class="{ 'rotate-180': open }"></i>
                    @if(request()->routeIs('laporan.*'))
                    <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                    @endif
                </button>
                <div x-show="open" x-cloak x-transition class="ml-8 mt-1 space-y-1 border-l-2 border-gray-100 pl-3">
                    @can('view-laporan-pendapatan')
                    <a href="{{ route('laporan.pendapatan') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('laporan.pendapatan') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-arrow-up text-green-600 w-4 h-4 flex-shrink-0"></i>
                        <span>Pendapatan</span>
                    </a>
                    @endcan
                    @can('view-laporan-pengeluaran')
                    <a href="{{ route('laporan.pengeluaran') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('laporan.pengeluaran') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-arrow-down text-red-600 w-4 h-4 flex-shrink-0"></i>
                        <span>Pengeluaran</span>
                    </a>
                    @endcan
                    @can('view-laporan-laba-rugi')
                    <a href="{{ route('laporan.laba-rugi') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('laporan.laba-rugi') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-calculator text-blue-600 w-4 h-4 flex-shrink-0"></i>
                        <span>Laba Rugi</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- Users (Admin Only) -->
            @can('view-user')
            <a href="{{ route('users.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                <i class="fas fa-user-shield w-5 h-5 flex-shrink-0 {{ request()->routeIs('users.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                <span class="flex-1">Users</span>
                @if(request()->routeIs('users.*'))
                <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                @endif
            </a>
            @endcan

            <!-- Customer Portal Dropdown -->
            @canany(['view-customer-portal', 'view-ticket', 'view-payment-proof'])
            <div x-data="{ open: {{ request()->routeIs('customer-portal.*') || request()->routeIs('admin.tickets.*') || request()->routeIs('admin.payment-proofs.*') ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open" class="sidebar-link group flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('customer-portal.*') || request()->routeIs('admin.tickets.*') || request()->routeIs('admin.payment-proofs.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-globe w-5 h-5 flex-shrink-0 {{ request()->routeIs('customer-portal.*') || request()->routeIs('admin.tickets.*') || request()->routeIs('admin.payment-proofs.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                        <span>Customer Portal</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 {{ request()->routeIs('customer-portal.*') || request()->routeIs('admin.tickets.*') || request()->routeIs('admin.payment-proofs.*') ? 'text-blue-600' : 'text-gray-400' }}" :class="{ 'rotate-180': open }"></i>
                    @if(request()->routeIs('customer-portal.*') || request()->routeIs('admin.tickets.*') || request()->routeIs('admin.payment-proofs.*'))
                    <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                    @endif
                </button>
                <div x-show="open" x-cloak x-transition class="ml-8 mt-1 space-y-1 border-l-2 border-gray-100 pl-3">
                    @can('view-customer-portal')
                    <a href="{{ route('customer-portal.index') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('customer-portal.index') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tachometer-alt text-blue-600 w-4 h-4 flex-shrink-0"></i>
                        <span>Dashboard</span>
                    </a>
                    @endcan
                    @can('view-ticket')
                    <a href="{{ route('admin.tickets.index') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.tickets.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-ticket-alt text-orange-600 w-4 h-4 flex-shrink-0"></i>
                        <span>Laporan Tiket</span>
                    </a>
                    @endcan
                    @can('view-payment-proof')
                    <a href="{{ route('admin.payment-proofs.index') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('admin.payment-proofs.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-receipt text-green-600 w-4 h-4 flex-shrink-0"></i>
                        <span>Bukti Pembayaran</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            <!-- OLT Monitoring -->
            @canany(['view-olt', 'view-onu', 'view-vlan', 'view-speed-profile'])
            <div x-data="{ open: {{ request()->routeIs('olt-monitoring.*') || request()->routeIs('olts.*') || request()->routeIs('onus.*') || request()->routeIs('vlans.*') || request()->routeIs('speed-profiles.*') ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open" class="sidebar-link group flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('olt-monitoring.*') || request()->routeIs('olts.*') || request()->routeIs('onus.*') || request()->routeIs('vlans.*') || request()->routeIs('speed-profiles.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-network-wired w-5 h-5 flex-shrink-0 {{ request()->routeIs('olt-monitoring.*') || request()->routeIs('olts.*') || request()->routeIs('onus.*') || request()->routeIs('vlans.*') || request()->routeIs('speed-profiles.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
                        <span>OLT Monitoring</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 {{ request()->routeIs('olt-monitoring.*') || request()->routeIs('olts.*') || request()->routeIs('onus.*') || request()->routeIs('vlans.*') || request()->routeIs('speed-profiles.*') ? 'text-blue-600' : 'text-gray-400' }}" :class="{ 'rotate-180': open }"></i>
                    @if(request()->routeIs('olt-monitoring.*') || request()->routeIs('olts.*') || request()->routeIs('onus.*') || request()->routeIs('vlans.*') || request()->routeIs('speed-profiles.*'))
                    <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
                    @endif
                </button>
                <div x-show="open" x-cloak x-transition class="ml-8 mt-1 space-y-1 border-l-2 border-gray-100 pl-3">
                    @can('view-olt')
                    <a href="{{ route('olt-monitoring.dashboard') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('olt-monitoring.dashboard') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tachometer-alt text-blue-600 w-4 h-4 flex-shrink-0"></i>
                        <span>Dashboard</span>
                    </a>
                    @endcan
                    @can('view-olt')
                    <a href="{{ route('olts.index') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('olts.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-server text-purple-600 w-4 h-4 flex-shrink-0"></i>
                        <span>Daftar OLT</span>
                    </a>
                    @endcan
                    @can('manage-onu')
                    <a href="{{ route('onus.register') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('onus.register') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-plus-circle text-green-600 w-4 h-4 flex-shrink-0"></i>
                        <span>Register ONU</span>
                    </a>
                    @endcan
                    @can('view-onu')
                    <a href="{{ route('onus.index') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('onus.index') || request()->routeIs('onus.show') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-list text-orange-600 w-4 h-4 flex-shrink-0"></i>
                        <span>All ONUs</span>
                    </a>
                    @endcan
                    @can('view-vlan')
                    <a href="{{ route('vlans.index') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('vlans.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tags text-indigo-600 w-4 h-4 flex-shrink-0"></i>
                        <span>VLAN Database</span>
                    </a>
                    @endcan
                    @can('view-speed-profile')
                    <a href="{{ route('speed-profiles.index') }}" class="group flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('speed-profiles.*') ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-tachometer-alt text-teal-600 w-4 h-4 flex-shrink-0"></i>
                        <span>Speed Profiles</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

        </div>
    </nav>

    <!-- Sidebar Footer -->
    <div class="border-t border-gray-100 px-3 py-3 bg-gray-50">
        @can('manage-company-profile')
        <a href="{{ route('settings.index') }}" class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-white hover:text-gray-900' }}">
            <i class="fas fa-cog w-5 h-5 flex-shrink-0 {{ request()->routeIs('settings.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-sky-500' }}"></i>
            <span class="flex-1">Settings</span>
            @if(request()->routeIs('settings.*'))
            <div class="absolute right-0 top-0 bottom-0 w-1 bg-blue-600 rounded-l-full"></div>
            @endif
        </a>
        @endcan
    </div>

</div>

<!-- Mobile sidebar overlay -->
<div x-show="sidebarOpen"
     x-cloak
     class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity duration-300"
     @click="sidebarOpen = false"></div>
