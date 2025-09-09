@extends('layouts.app')

@section('title', 'Detail User - WiFi Billing Management')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail User</h1>
                <p class="mt-1 text-sm text-gray-600">Informasi lengkap user {{ $user->name }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('users.edit', $user) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <i class="fas fa-edit mr-2"></i>
                    Edit User
                </a>
                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-600"></i>Informasi User
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <p class="text-lg text-gray-900">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Role</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                <i class="fas {{ $user->role === 'admin' ? 'fa-crown' : 'fa-user-tie' }} mr-2"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                Aktif
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Dibuat</label>
                            <p class="text-lg text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Terakhir Diupdate</label>
                            <p class="text-lg text-gray-900">{{ $user->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($user->role === 'penagih' && $user->penagih)
            <!-- Penagih Information -->
            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user-tie mr-2 text-green-600"></i>Informasi Penagih
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nama Penagih</label>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($user->penagih)
                                    {{ $user->penagih->nama }}
                                @else
                                    <span class="text-gray-400 italic">Belum ada penagih</span>
                                @endif
                            </p>
                        </div>
                        @if($user->penagih)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">No. HP</label>
                            <p class="text-lg text-gray-900">{{ $user->penagih->no_hp ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Alamat</label>
                            <p class="text-lg text-gray-900">{{ $user->penagih->alamat ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Pelanggan</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $user->penagih->pelanggans_count ?? 0 }} pelanggan</p>
                        </div>
                        @endif
                        @if($user->penagih)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->penagih->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas {{ $user->penagih->aktif ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                                {{ $user->penagih->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- User Statistics -->
        <div class="space-y-6">
            <!-- Account Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-purple-600"></i>Statistik Akun
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Login Terakhir</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $user->updated_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Akun Dibuat</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $user->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($user->role === 'penagih' && $user->penagih)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Pelanggan</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $user->penagih->pelanggans()->count() }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bolt mr-2 text-yellow-600"></i>Aksi Cepat
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('users.edit', $user) }}"
                           class="w-full flex items-center px-4 py-3 text-sm text-left text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                            <i class="fas fa-edit mr-3 text-blue-500"></i>
                            Edit Informasi User
                        </a>

                        @if($user->role === 'penagih')
                        <a href="{{ route('penagihs.index') }}"
                           class="w-full flex items-center px-4 py-3 text-sm text-left text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                            <i class="fas fa-user-tie mr-3 text-green-500"></i>
                            Lihat Data Penagih
                        </a>
                        @endif

                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full flex items-center px-4 py-3 text-sm text-left text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                <i class="fas fa-trash mr-3 text-red-500"></i>
                                Hapus User
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
