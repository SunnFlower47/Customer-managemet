@extends('layouts.app')

@section('title', 'Tambah Penagih - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Tambah Penagih Baru</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Tambahkan penagih baru ke sistem</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('penagihs.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    @if ($errors && $errors->any())
        <div class="app-card bg-red-50 border border-red-200">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-red-800">Terdapat kesalahan:</h3>
                    <div class="mt-2 text-xs text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="app-card bg-red-50 border border-red-200">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('penagihs.store') }}" class="space-y-6">
        @csrf

        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Informasi Dasar</p>
                <h2 class="text-base font-semibold text-gray-900">Data penagih</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-purple-500"></i>Nama Lengkap
                    </label>
                    <input type="text"
                           name="nama"
                           id="nama"
                           value="{{ old('nama') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('nama') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap"
                           required>
                    @error('nama')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-purple-500"></i>Email
                    </label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('email') border-red-500 @enderror"
                           placeholder="Masukkan email"
                           required>
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone mr-2 text-purple-500"></i>No. HP
                    </label>
                    <input type="text"
                           name="no_hp"
                           id="no_hp"
                           value="{{ old('no_hp') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('no_hp') border-red-500 @enderror"
                           placeholder="Masukkan nomor HP">
                    @error('no_hp')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="alamat" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>Alamat
                    </label>
                    <textarea name="alamat"
                              id="alamat"
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('alamat') border-red-500 @enderror"
                              placeholder="Masukkan alamat">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="app-card space-y-5">
            <div>
                <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Akun User</p>
                <h2 class="text-base font-semibold text-gray-900">Buat akun login (opsional)</h2>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-100 rounded-xl px-4 py-3 flex items-start gap-3">
                <input type="checkbox"
                       name="create_user_account"
                       id="create_user_account"
                       value="1"
                       {{ old('create_user_account') ? 'checked' : '' }}
                       class="mt-1 h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                       onchange="toggleUserAccountFields()">
                <label for="create_user_account" class="text-sm font-semibold text-gray-700">
                    Buat Akun User untuk Penagih
                    <span class="block text-xs font-normal text-gray-500 mt-1">Centang untuk membuat akun login yang terhubung dengan penagih ini</span>
                </label>
            </div>

            <div id="user-account-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ old('create_user_account') ? '' : 'hidden' }}">
                <div>
                    <label for="user_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-purple-500"></i>Username
                    </label>
                    <input type="text"
                           name="user_name"
                           id="user_name"
                           value="{{ old('user_name') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('user_name') border-red-500 @enderror"
                           placeholder="Masukkan username">
                    @error('user_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="user_email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-purple-500"></i>Email User
                    </label>
                    <input type="email"
                           name="user_email"
                           id="user_email"
                           value="{{ old('user_email') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('user_email') border-red-500 @enderror"
                           placeholder="Masukkan email untuk login">
                    @error('user_email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="user_password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-purple-500"></i>Password
                    </label>
                    <input type="password"
                           name="user_password"
                           id="user_password"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-gray-50 focus:bg-white @error('user_password') border-red-500 @enderror"
                           placeholder="Masukkan password (min. 6 karakter)">
                    @error('user_password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="app-card">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100 rounded-xl px-4 py-3 flex items-start gap-3">
                <input type="checkbox"
                       name="aktif"
                       id="aktif"
                       value="1"
                       {{ old('aktif', true) ? 'checked' : '' }}
                       class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                <label for="aktif" class="text-sm font-semibold text-gray-700">
                    Penagih Aktif
                    <span class="block text-xs font-normal text-gray-500 mt-1">Centang untuk mengaktifkan penagih ini</span>
                </label>
            </div>
        </div>

        <div class="app-card inline-actions">
            <a href="{{ route('penagihs.index') }}"
               class="flex-1 border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition text-center">
                <i class="fas fa-times mr-2"></i>Batal
            </a>
            <button type="submit"
                    class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i>Simpan Penagih
            </button>
        </div>
    </form>
</div>

@push('scripts')
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
@endpush
@endsection
