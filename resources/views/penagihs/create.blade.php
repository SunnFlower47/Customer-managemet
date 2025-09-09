@extends('layouts.app')

@section('title', 'Tambah Penagih - WiFi Billing Management')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    @if ($errors && $errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user-plus mr-2 text-blue-600"></i>Tambah Penagih Baru
                </h1>
            </div>

            <form method="POST" action="{{ route('penagihs.store') }}" class="p-6">
                @csrf

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
                                       value="{{ old('nama') }}"
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
                                       value="{{ old('email') }}"
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
                                       value="{{ old('no_hp') }}"
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
                                          placeholder="Masukkan alamat">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- User Account Creation -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex items-center mb-4">
                            <input type="checkbox"
                                   name="create_user_account"
                                   id="create_user_account"
                                   value="1"
                                   {{ old('create_user_account') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   onchange="toggleUserAccountFields()">
                            <label for="create_user_account" class="ml-2 block text-sm font-medium text-gray-700">
                                <i class="fas fa-user-cog mr-1 text-blue-500"></i>Buat Akun User untuk Penagih
                            </label>
                        </div>

                        <div id="user-account-fields" class="space-y-4 {{ old('create_user_account') ? '' : 'hidden' }}">
                            <div>
                                <label for="user_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user mr-1 text-gray-400"></i>Username
                                </label>
                                <input type="text"
                                       name="user_name"
                                       id="user_name"
                                       value="{{ old('user_name') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('user_name') border-red-500 @enderror"
                                       placeholder="Masukkan username">
                                @error('user_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="user_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-1 text-gray-400"></i>Email User
                                </label>
                                <input type="email"
                                       name="user_email"
                                       id="user_email"
                                       value="{{ old('user_email') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('user_email') border-red-500 @enderror"
                                       placeholder="Masukkan email untuk login">
                                @error('user_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="user_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-1 text-gray-400"></i>Password
                                </label>
                                <input type="password"
                                       name="user_password"
                                       id="user_password"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('user_password') border-red-500 @enderror"
                                       placeholder="Masukkan password">
                                @error('user_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="aktif"
                               id="aktif"
                               value="1"
                               {{ old('aktif', true) ? 'checked' : '' }}
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
                        <i class="fas fa-save mr-2"></i>Simpan Penagih
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
function toggleUserAccountFields() {
    const checkbox = document.getElementById('create_user_account');
    const fields = document.getElementById('user-account-fields');

    if (checkbox.checked) {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
    }
}
</script>
@endsection
