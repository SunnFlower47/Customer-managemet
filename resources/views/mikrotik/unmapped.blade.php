@extends('layouts.app')

@section('title', 'Unmapped PPPoE Users')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <!-- Header -->
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-unlink"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">User Belum Terhubung</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-600">
                    Daftar user PPPoE di <strong>{{ $mikrotik->nama }}</strong> yang belum terhubung ke data pelanggan
                </p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('mikrotik.show', $mikrotik->id) }}" 
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-300 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Detail
            </a>
        </div>
    </div>

    <!-- Alert Info -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-xl">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    User di bawah ini ada di router MikroTik tetapi belum ditautkan ke data pelanggan di sistem billing. 
                    Klik <strong>"Buat Pelanggan"</strong> untuk menambahkan mereka sebagai pelanggan baru secara otomatis.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Tindakan Diperlukan</p>
                <h2 class="text-base font-semibold text-gray-900">Daftar User Unmapped</h2>
            </div>
             <form action="{{ route('mikrotik.sync', $mikrotik->id) }}" method="POST">
                @csrf
                <button type="submit" class="text-sm font-semibold text-green-600 hover:text-green-700 inline-flex items-center" onclick="return confirm('Sync ulang?')">
                    <i class="fas fa-sync mr-1"></i> Refresh Data
                </button>
            </form>
        </div>

        <!-- Desktop Table -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-orange-500 to-orange-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Username
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-lock mr-2"></i>Password
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>Profile
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-2"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-orange-50 transition border-b border-gray-100">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="font-bold text-gray-900">{{ $user->username }}</span>
                            @if($user->mac_address)
                                <div class="text-xs text-gray-500 mt-0.5"><i class="fas fa-desktop mr-1"></i>{{ $user->mac_address }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap font-mono text-sm text-gray-600">
                            {{ $user->password ?? '******' }}
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $user->profile }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-xs font-medium">
                            <a href="{{ route('mikrotik.create-customer', $user->id) }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-xs font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                <i class="fas fa-user-plus mr-2"></i>Buat Pelanggan
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                                <p class="font-medium text-gray-900">Semua Beres!</p>
                                <p class="text-sm">Semua user PPPoE sudah terhubung dengan pelanggan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Layout -->
        <div class="lg:hidden space-y-3">
            @forelse($users as $user)
            <div class="mobile-card border border-gray-200 rounded-2xl px-4 py-3 bg-white shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-gray-900">{{ $user->username }}</span>
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded-lg text-gray-600">{{ $user->profile }}</span>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mb-4">
                    <div class="bg-gray-50 p-2 rounded">
                        <span class="block text-[10px] uppercase text-gray-400">Password</span>
                        <span class="font-mono">{{ $user->password ?? '***' }}</span>
                    </div>
                    <div class="bg-gray-50 p-2 rounded">
                        <span class="block text-[10px] uppercase text-gray-400">MAC</span>
                        <span>{{ $user->mac_address ?? '-' }}</span>
                    </div>
                </div>
                
                <a href="{{ route('mikrotik.create-customer', $user->id) }}" class="block w-full text-center py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 shadow-sm transition">
                    <i class="fas fa-user-plus mr-1"></i> Buat Pelanggan
                </a>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                <p>Semua user sudah terhubung.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
