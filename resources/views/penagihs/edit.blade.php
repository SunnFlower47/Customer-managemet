@extends('layouts.app')

@section('title', 'Edit Penagih - WiFi Billing Management')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user-edit mr-2 text-blue-600"></i>Edit Penagih: {{ $penagih->nama }}
                </h1>
            </div>

            <form method="POST" action="{{ route('penagihs.update', $penagih) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-info-circle mr-2 text-gray-400"></i>Informasi Dasar
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user mr-1 text-gray-400"></i>Nama Lengkap
                                </label>
                                <input type="text"
                                       name="nama"
                                       id="nama"
                                       value="{{ old('nama', $penagih->nama) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('nama') border-red-500 @enderror"
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                @error('nama')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-1 text-gray-400"></i>Email
                                </label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       value="{{ old('email', $penagih->email) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('email') border-red-500 @enderror"
                                       placeholder="Masukkan email"
                                       required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-phone mr-1 text-gray-400"></i>No. HP
                                </label>
                                <input type="text"
                                       name="no_hp"
                                       id="no_hp"
                                       value="{{ old('no_hp', $penagih->no_hp) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('no_hp') border-red-500 @enderror"
                                       placeholder="Masukkan nomor HP">
                                @error('no_hp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>Alamat
                                </label>
                                <textarea name="alamat"
                                          id="alamat"
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('alamat') border-red-500 @enderror"
                                          placeholder="Masukkan alamat">{{ old('alamat', $penagih->alamat) }}</textarea>
                                @error('alamat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- User Account Information -->
                    @if($penagih->user)
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-user-cog mr-2 text-gray-400"></i>Informasi Akun User
                        </h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                    <p class="text-sm text-gray-900">{{ $penagih->user->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Login</label>
                                    <p class="text-sm text-gray-900">{{ $penagih->user->email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($penagih->user->role) }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Akun</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $penagih->user->aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $penagih->user->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Status -->
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="aktif"
                               id="aktif"
                               value="1"
                               {{ old('aktif', $penagih->aktif) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="aktif" class="ml-2 block text-sm text-gray-700">
                            <i class="fas fa-check-circle mr-1 text-green-500"></i>Penagih Aktif
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end space-x-4">
                    <a href="{{ route('penagihs.index') }}"
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Update Penagih
                    </button>
                </div>
            </form>
    </div>
</div>

@endsection
