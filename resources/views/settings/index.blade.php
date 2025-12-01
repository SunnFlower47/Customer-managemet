@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-cog"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Pengaturan Sistem</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Kelola pengaturan sistem, backup database, dan manajemen role</p>
            </div>
        </div>
        </div>

        <!-- Tabs Navigation -->
    <div class="settings-tabs-shell">
        <div class="app-card app-card--soft">
        <nav class="tab-scroll flex space-x-2 sm:space-x-4 overflow-x-auto pb-2" aria-label="Tabs">
            <button type="button" class="tab-button-mobile flex-shrink-0 active" data-settings-tab="profile" id="tab-profile">
                <div class="flex flex-col items-center p-2 sm:p-3 min-w-[70px] sm:min-w-[80px]">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-1 sm:mb-2">
                        <i class="fas fa-user text-blue-600 text-sm sm:text-base"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-semibold text-center leading-tight">Profil</span>
                </div>
            </button>
            <button type="button" class="tab-button-mobile flex-shrink-0" data-settings-tab="backup" id="tab-backup">
                <div class="flex flex-col items-center p-2 sm:p-3 min-w-[70px] sm:min-w-[80px]">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-100 rounded-lg flex items-center justify-center mb-1 sm:mb-2">
                        <i class="fas fa-database text-gray-600 text-sm sm:text-base"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-semibold text-center leading-tight">Backup</span>
                </div>
            </button>
            <button type="button" class="tab-button-mobile flex-shrink-0" data-settings-tab="roles" id="tab-roles">
                <div class="flex flex-col items-center p-2 sm:p-3 min-w-[70px] sm:min-w-[80px]">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-100 rounded-lg flex items-center justify-center mb-1 sm:mb-2">
                        <i class="fas fa-shield-alt text-gray-600 text-sm sm:text-base"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-semibold text-center leading-tight">Role</span>
                </div>
            </button>
            <button type="button" class="tab-button-mobile flex-shrink-0" data-settings-tab="company" id="tab-company">
                <div class="flex flex-col items-center p-2 sm:p-3 min-w-[70px] sm:min-w-[80px]">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-100 rounded-lg flex items-center justify-center mb-1 sm:mb-2">
                        <i class="fas fa-building text-gray-600 text-sm sm:text-base"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-semibold text-center leading-tight">Perusahaan</span>
                </div>
            </button>
            @can('view-audit-trail')
            <button type="button" class="tab-button-mobile flex-shrink-0" data-settings-tab="audit" id="tab-audit">
                <div class="flex flex-col items-center p-2 sm:p-3 min-w-[70px] sm:min-w-[80px]">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-100 rounded-lg flex items-center justify-center mb-1 sm:mb-2">
                        <i class="fas fa-history text-gray-600 text-sm sm:text-base"></i>
                    </div>
                    <span class="text-[10px] sm:text-xs font-semibold text-center leading-tight">Audit</span>
                </div>
            </button>
            @endcan
        </nav>
        <div class="tab-scroll-indicator sm:hidden">
            <span class="text-[11px] font-medium text-slate-500 flex items-center gap-2">
                <i class="fas fa-arrows-alt-h text-xs"></i>
                Geser untuk melihat menu lainnya
            </span>
                </div>
        </div>

        <!-- Tab Content -->
        <div class="app-card" id="settings-tab-content-wrapper">
            <!-- Profile Tab -->
            <div id="content-profile" class="tab-content space-y-5" data-settings-content="profile">
                <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Update Profil Akun</p>
                <h2 class="text-base font-semibold text-gray-900">Perbarui informasi akun Anda</h2>
            </div>

            <form action="{{ route('settings.update-profile') }}" method="POST" class="space-y-4">
                        @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-indigo-500"></i>Nama Lengkap
                        </label>
                                <input type="text" name="name" value="{{ auth()->user()?->name ?? '' }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" required>
                            </div>
                            <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-indigo-500"></i>Email
                        </label>
                                <input type="email" name="email" value="{{ auth()->user()?->email ?? '' }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" required>
                            </div>
                        </div>

                        <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-indigo-500"></i>Password Saat Ini
                    </label>
                            <input type="password" name="current_password"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" required>
                        </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru (Opsional)</label>
                                <input type="password" name="password"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                            </div>
                            <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                            </div>
                        </div>

                <div class="inline-actions pt-4 border-t border-gray-200">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg transition">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
            </div>


            <!-- Backup Tab -->
            <div id="content-backup" class="tab-content hidden space-y-5" data-settings-content="backup">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Backup Database</p>
                <h2 class="text-base font-semibold text-gray-900">Kelola backup database</h2>
            </div>

                    <!-- Create Backup -->
            <div class="app-card bg-gray-50 border-gray-200 space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Buat Backup Manual</p>
                    <h3 class="text-sm font-semibold text-gray-900">Backup database sekarang</h3>
                </div>

                        <!-- Success Message -->
                        @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            </div>
                        @endif

                        <!-- Warning Message -->
                        @if(session('warning'))
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-xl text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                            </div>
                        @endif

                        <!-- Error Message -->
                        @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        <!-- Backup Error Message -->
                        @if($errors && $errors->has('backup'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $errors->first('backup') }}
                            </div>
                        @endif

                <div class="bg-white border border-gray-200 rounded-xl p-4 space-y-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-1">Backup Database</p>
                        <p class="text-xs text-gray-600">
                                Backup database yang kompatibel dengan sistem restore dan phpMyAdmin import.
                            </p>
                    </div>

                    <form action="{{ route('settings.create-backup') }}" method="POST" class="space-y-4">
                                @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Opsional)</label>
                                    <textarea name="notes" rows="3"
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                                              placeholder="Masukkan catatan untuk backup ini..."></textarea>
                                </div>
                        <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                                    <i class="fas fa-download mr-2"></i>Buat Backup Database
                                </button>
                            </form>

                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                <p class="text-xs text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>Fitur:</strong> Backup ini dapat digunakan untuk restore sistem dan import phpMyAdmin tanpa error foreign key constraint.
                                </p>
                            </div>
                        </div>
                    </div>


                    <!-- Backup History -->
            <div class="space-y-4">
                    <div>
                    <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Riwayat Backup</p>
                    <h3 class="text-sm font-semibold text-gray-900">Daftar backup Laravel (disimpan di storage privat)</h3>
                </div>

                        <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="data-table min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600">
                            <tr>
                                <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                    <i class="fas fa-file mr-2"></i>File
                                </th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                    <i class="fas fa-weight mr-2"></i>Ukuran
                                </th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                    <i class="fas fa-shield-alt mr-2"></i>Lokasi
                                </th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                    <i class="fas fa-calendar mr-2"></i>Waktu Backup
                                </th>
                                <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                    <i class="fas fa-cog mr-2"></i>Aksi
                                </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($backups as $backup)
                            <tr class="hover:bg-indigo-50 transition">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-gray-900 truncate max-w-xs">{{ $backup['filename'] }}</p>
                                    <p class="text-[11px] text-gray-500">Backup ZIP Laravel</p>
                                        </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <p class="text-xs text-gray-700">{{ $backup['size_human'] }}</p>
                                        </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                        Storage Privat
                                    </span>
                                        </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <p class="text-xs text-gray-700">{{ $backup['last_modified']->format('d/m/Y H:i:s') }}</p>
                                        </td>
                                <td class="px-5 py-4 whitespace-nowrap text-xs font-medium">
                                    <a href="{{ route('settings.backup.download', $backup['filename']) }}"
                                       class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-900 transition">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-database text-gray-400 text-4xl mb-2"></i>
                                        <p class="text-gray-500">Belum ada backup</p>
                                    </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                <div class="lg:hidden space-y-2">
                    @forelse($backups as $backup)
                    <div class="mobile-card border border-gray-200 rounded-xl px-4 py-3">
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-database text-indigo-600 text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $backup['filename'] }}</p>
                                <div class="flex flex-wrap gap-2 mt-2 items-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                        Storage Privat
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $backup['size_human'] }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-calendar mr-1"></i>{{ $backup['last_modified']->format('d/m/Y H:i:s') }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('settings.backup.download', $backup['filename']) }}"
                               class="inline-flex items-center justify-center w-full px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-md transition text-xs font-semibold">
                                <i class="fas fa-download mr-1"></i>Download
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="app-card text-center py-12">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-database text-gray-300 text-4xl mb-4"></i>
                            <h3 class="text-base font-semibold text-gray-900 mb-2">Belum ada backup</h3>
                            <p class="text-sm text-gray-500">Belum ada backup database yang tersedia.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
            </div>

            <!-- Roles Tab -->
            <div id="content-roles" class="tab-content hidden space-y-5" data-settings-content="roles">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Role & Permission Management</p>
                <h2 class="text-base font-semibold text-gray-900">Kelola role dan permission</h2>
            </div>

                    @can('manage-roles')
            <div class="app-card bg-gray-50 border-gray-200 space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Buat Role Baru</p>
                    <h3 class="text-sm font-semibold text-gray-900">Tambah role baru</h3>
                </div>
                <form action="{{ route('settings.create-role') }}" method="POST" class="space-y-4">
                            @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Role</label>
                                <input type="text" name="name"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                                       placeholder="Masukkan nama role" required>
                            </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Permissions</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-60 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-white">
                                    @foreach($permissions as $permission)
                            <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700">{{ $permission->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                    <div>
                        <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                                    <i class="fas fa-plus mr-2"></i>Buat Role
                                </button>
                            </div>
                        </form>
                    </div>
                    @endcan

                    <!-- Roles List -->
            <div class="space-y-3">
                    <div>
                    <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Daftar Role</p>
                    <h3 class="text-sm font-semibold text-gray-900">Role yang tersedia</h3>
                </div>
                <div class="space-y-2">
                            @foreach($roles as $role)
                    <div class="app-card border border-gray-200 space-y-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                <h4 class="text-sm font-semibold text-gray-900">{{ ucfirst($role->name) }}</h4>
                                <p class="text-xs text-gray-500">{{ $role->permissions->count() }} permissions</p>
                                    </div>
                                    @can('manage-roles')
                            <div class="inline-flex gap-2">
                                        <button onclick="editRole('{{ $role->id }}', '{{ $role->name }}', {{ $role->permissions->pluck('name')->toJson() }})"
                                        class="text-blue-600 hover:text-blue-900 transition"
                                                title="Edit Role">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteRole('{{ $role->id }}', '{{ $role->name }}')"
                                        class="text-red-600 hover:text-red-900 transition"
                                                title="Delete Role">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @endcan
                                </div>
                        <div class="flex flex-wrap gap-1.5">
                                        @foreach($role->permissions as $permission)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ $permission->name }}
                                        </span>
                                        @endforeach
                                </div>
                            </div>
                            @endforeach
                    </div>
                </div>
            </div>


            <!-- Company Profile Tab -->
            <div id="content-company" class="tab-content hidden space-y-5" data-settings-content="company">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Profil Perusahaan</p>
                <h2 class="text-base font-semibold text-gray-900">Kelola informasi perusahaan</h2>
            </div>

            <form action="{{ route('settings.update-company-profile') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Perusahaan (Utama)</label>
                                <input type="text" name="nama_perusahaan" value="{{ $companyProfile->nama_perusahaan ?? '' }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" required>
                                <p class="text-xs text-gray-500 mt-1">Nama yang akan digunakan di sistem</p>
                            </div>
                            <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Support</label>
                                <input type="email" name="email_support" value="{{ $companyProfile->email_support ?? '' }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" required>
                            </div>
                        </div>

                        <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap Perusahaan</label>
                            <input type="text" name="nama_lengkap_perusahaan" value="{{ $companyProfile->nama_lengkap_perusahaan ?? '' }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                                   placeholder="Contoh: Baraya Citra Mandiri">
                            <p class="text-xs text-gray-500 mt-1">Nama lengkap untuk dokumen resmi dan tampilan</p>
                        </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Inisial Perusahaan</label>
                                <input type="text" name="inisial_perusahaan" value="{{ $companyProfile->inisial_perusahaan ?? '' }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                                       placeholder="Contoh: BCM" maxlength="10">
                                <p class="text-xs text-gray-500 mt-1">Inisial untuk logo dan branding (auto-generate jika kosong)</p>
                            </div>
                            <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Prefix Kode Pembayaran</label>
                                <input type="text" name="payment_code_prefix" value="{{ $companyProfile->payment_code_prefix ?? 'PAY' }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                                       maxlength="3" placeholder="PAY" required>
                                <p class="text-xs text-gray-500 mt-1">Maksimal 3 karakter (contoh: PAY, INV, BIL)</p>
                            </div>
                        </div>

                        <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                            <textarea name="alamat" rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" required>{{ $companyProfile->alamat ?? '' }}</textarea>
                        </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Kontak</label>
                                <input type="text" name="nomor_kontak" value="{{ $companyProfile->nomor_kontak ?? '' }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" required>
                            </div>
                            <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp</label>
                                <input type="text" name="whatsapp" value="{{ $companyProfile->whatsapp ?? '' }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                            </div>
                        </div>

                        <!-- Payment Methods Section -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="mb-4">
                                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Metode Pembayaran</p>
                                <h3 class="text-base font-semibold text-gray-900">Kelola informasi metode pembayaran</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-mobile-alt mr-2 text-indigo-500"></i>Nomor DANA
                                    </label>
                                    <input type="text" name="dana_phone" value="{{ $companyProfile->dana_phone ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                                           placeholder="+62 877-2666-1964">
                                    <p class="text-xs text-gray-500 mt-1">Nomor telepon untuk pembayaran via DANA</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-university mr-2 text-indigo-500"></i>No. Rekening Mandiri
                                    </label>
                                    <input type="text" name="mandiri_account" value="{{ $companyProfile->mandiri_account ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                                           placeholder="1234567890">
                                    <p class="text-xs text-gray-500 mt-1">Nomor rekening Bank Mandiri</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-user mr-2 text-indigo-500"></i>Nama Rekening Mandiri
                                    </label>
                                    <input type="text" name="mandiri_account_name" value="{{ $companyProfile->mandiri_account_name ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                                           placeholder="Nama Pemilik Rekening">
                                    <p class="text-xs text-gray-500 mt-1">Nama pemilik rekening Bank Mandiri</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fab fa-whatsapp mr-2 text-indigo-500"></i>WhatsApp Pembayaran
                                    </label>
                                    <input type="text" name="payment_whatsapp" value="{{ $companyProfile->payment_whatsapp ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                                           placeholder="+62 812-3456-7890">
                                    <p class="text-xs text-gray-500 mt-1">Nomor WhatsApp untuk kirim bukti pembayaran</p>
                                </div>
                            </div>
                        </div>

                            <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                                <input type="url" name="website" value="{{ $companyProfile->website ?? '' }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                        </div>

                        <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Logo Perusahaan</label>
                            <input type="file" name="logo" accept="image/*"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                            @if($companyProfile && $companyProfile->logo_path)
                            <div class="mt-2">
                        <img src="{{ $companyProfile->logo_url }}?v={{ time() }}" alt="Current Logo" class="h-16 sm:h-20 w-auto object-contain border border-gray-200 rounded-xl p-2">
                            </div>
                            @endif
                        </div>

                        <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" rows="4"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">{{ $companyProfile->deskripsi ?? '' }}</textarea>
                        </div>

                <div class="inline-actions pt-4 border-t border-gray-200">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg transition">
                                <i class="fas fa-save mr-2"></i>Simpan Profil Perusahaan
                            </button>
                        </div>
                    </form>
            </div>


            <!-- Audit Trail Tab -->
            @can('view-audit-trail')
            <div id="content-audit" class="tab-content hidden space-y-5" data-settings-content="audit">
                <div>
                    <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Audit Trail</p>
                    <h2 class="text-base font-semibold text-gray-900">Log aktivitas sistem</h2>
                </div>

                <div class="app-card space-y-4">
                    <a href="{{ route('audit-trails.index') }}"
                       class="inline-flex items-center justify-center px-5 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-xl hover:shadow-lg transition text-sm font-semibold">
                        <i class="fas fa-external-link-alt mr-2"></i>Buka Audit Trail
                    </a>
                </div>

                <div class="app-card space-y-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Informasi Audit Trail</p>
                        <h3 class="text-sm font-semibold text-gray-900">Tentang audit trail</h3>
                    </div>

                        <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                            <h4 class="text-xs font-semibold text-gray-900 mb-2 flex items-center">
                                <i class="fas fa-eye mr-2 text-green-600"></i>Yang Dicatat
                            </h4>
                            <ul class="text-xs text-gray-600 space-y-1">
                                <li>• Perubahan data Pelanggan</li>
                                <li>• Perubahan data Pembayaran</li>
                                <li>• Perubahan data User</li>
                                <li>• Perubahan data Paket</li>
                            </ul>
                        </div>

                        <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                            <h4 class="text-xs font-semibold text-gray-900 mb-2 flex items-center">
                                <i class="fas fa-shield-alt mr-2 text-purple-600"></i>Keamanan
                            </h4>
                            <ul class="text-xs text-gray-600 space-y-1">
                                <li>• IP Address tercatat</li>
                                <li>• User Agent tercatat</li>
                                <li>• Timestamp lengkap</li>
                                <li>• Old & New Values</li>
                            </ul>
                        </div>
                    </div>

                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-lightbulb text-blue-600 mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-semibold text-blue-900 mb-1">Tips</h4>
                                <p class="text-xs text-blue-800">
                                    Audit Trail membantu melacak semua perubahan data dalam sistem.
                                    Klik tombol "Buka Audit Trail" di atas untuk melihat log lengkap aktivitas sistem.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
 <!-- end settings tabs wrapper -->

<!-- Edit Role Modal -->
<div id="editRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="app-card max-w-2xl w-full max-h-[90vh] overflow-y-auto space-y-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Edit Role</p>
                <h3 class="text-base font-semibold text-gray-900">Perbarui role</h3>
            </div>
            <form id="editRoleForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Role</label>
                    <input type="text" id="editRoleName" name="name"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Permissions</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-60 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-white">
                        @foreach($permissions as $permission)
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded-lg">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                   class="edit-permission-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs text-gray-700">{{ $permission->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="inline-actions pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeEditRoleModal()"
                            class="flex-1 border border-gray-200 text-gray-700 px-5 py-3 rounded-xl text-sm font-semibold hover:bg-gray-50 transition text-center">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3 rounded-xl text-sm font-semibold hover:shadow-lg transition">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.tab-button {
    @apply px-2 sm:px-3 py-2 text-xs sm:text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 transition-all duration-200 whitespace-nowrap relative;
}

.tab-button.active {
    @apply text-blue-600 border-blue-600 font-semibold bg-blue-50;
    position: relative;
}

.tab-button.active::before {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 3px;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    border-radius: 2px;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

.tab-button-mobile {
    @apply bg-white border border-gray-200 rounded-xl hover:shadow-md transition-all duration-200;
    min-width: 70px;
}

.tab-button-mobile.active {
    @apply bg-indigo-50 border-indigo-200 shadow-md;
    position: relative;
}

.tab-button-mobile.active::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #6366f1, #4f46e5);
    border-radius: 0 0 12px 12px;
}

.tab-button-mobile.active .w-8,
.tab-button-mobile.active .w-10 {
    @apply bg-indigo-100;
}

.tab-button-mobile.active .fas {
    @apply text-indigo-600;
}

.tab-button-mobile.active span {
    @apply text-indigo-600 font-semibold;
}

.tab-button-mobile:hover:not(.active) {
    @apply border-gray-300 bg-gray-50;
}

.tab-button-mobile:hover:not(.active) .w-8,
.tab-button-mobile:hover:not(.active) .w-10 {
    @apply bg-gray-200;
}

.tab-button-mobile:hover:not(.active) .fas {
    @apply text-gray-700;
}

.tab-scroll::-webkit-scrollbar {
    height: 4px;
}

.tab-scroll::-webkit-scrollbar-track {
    background: #e2e8f0;
    border-radius: 999px;
}

.tab-scroll::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg, #6366f1, #4f46e5);
    border-radius: 999px;
}

.tab-scroll-indicator {
    padding-top: 0.35rem;
}

/* Hide scrollbar for mobile tabs */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.tab-content {
    @apply transition duration-150;
    display: block;
    min-height: 200px;
    position: relative;
    width: 100%;
}

.tab-content.hidden {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    max-height: 0 !important;
    overflow: hidden !important;
    pointer-events: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

.tab-content:not(.hidden) {
    position: relative !important;
    width: 100% !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    height: auto !important;
    overflow: visible !important;
    min-height: 200px !important;
    pointer-events: auto !important;
}

#settings-tab-content-wrapper {
    position: relative;
    min-height: 400px;
    overflow: visible;
    width: 100%;
    display: block;
}

#settings-tab-content-wrapper > .tab-content {
    width: 100%;
    box-sizing: border-box;
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
    .tab-button {
        @apply px-2 py-1 text-xs;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = Array.from(document.querySelectorAll('[data-settings-tab]'));
    const tabContents = Array.from(document.querySelectorAll('[data-settings-content]'));

    console.log('Settings tabs: Found buttons:', tabButtons.length);
    console.log('Settings tabs: Found contents:', tabContents.length);
    console.log('Settings tabs: Tab buttons:', tabButtons.map(b => b.dataset.settingsTab));
    console.log('Settings tabs: Tab contents:', tabContents.map(c => c.dataset.settingsContent));

    if (!tabButtons.length || !tabContents.length) {
        console.warn('Settings tabs: No tab buttons or contents found');
        return;
    }

    const validTabs = tabContents.map(el => el.dataset.settingsContent);
    console.log('Settings tabs: Valid tabs found:', validTabs);

    const pickValid = value => (value && validTabs.includes(value)) ? value : null;
    const params = new URLSearchParams(window.location.search);
    const initialTab =
        pickValid(params.get('tab')) ||
        pickValid(window.location.hash.replace('#', '')) ||
        pickValid(localStorage.getItem('settingsActiveTab')) ||
        validTabs[0];

    console.log('Settings tabs: Initial tab:', initialTab);

    const showTab = (tabName, { skipSave = false } = {}) => {
        if (!validTabs.includes(tabName)) {
            console.warn('Settings tabs: Invalid tab name:', tabName);
            return;
        }

        console.log('Settings tabs: Showing tab:', tabName);

        // Find the specific tab content
        const activeContent = tabContents.find(c => c.dataset.settingsContent === tabName);
        if (!activeContent) {
            console.error('Settings tabs: Tab content not found for:', tabName);
            console.error('Settings tabs: Available contents:', tabContents.map(c => c.dataset.settingsContent));
            return;
        }

        // Hide all tab contents first
        tabContents.forEach(content => {
            if (content !== activeContent) {
                content.classList.add('hidden');
                content.style.setProperty('display', 'none', 'important');
                content.style.setProperty('visibility', 'hidden', 'important');
                content.style.setProperty('opacity', '0', 'important');
                content.style.setProperty('height', '0', 'important');
                content.style.setProperty('overflow', 'hidden', 'important');
                content.style.setProperty('pointer-events', 'none', 'important');
            }
        });

        // Show the active tab content - ensure it's visible and takes up space
        activeContent.classList.remove('hidden');
        activeContent.style.removeProperty('display');
        activeContent.style.removeProperty('visibility');
        activeContent.style.removeProperty('opacity');
        activeContent.style.removeProperty('height');
        activeContent.style.removeProperty('overflow');
        activeContent.style.removeProperty('pointer-events');
        activeContent.style.setProperty('display', 'block', 'important');
        activeContent.style.setProperty('visibility', 'visible', 'important');
        activeContent.style.setProperty('opacity', '1', 'important');
        activeContent.style.setProperty('position', 'relative', 'important');
        activeContent.style.setProperty('width', '100%', 'important');
        activeContent.style.setProperty('min-height', '200px', 'important');

        // Ensure parent wrapper is visible and has proper height
        const parentCard = activeContent.closest('#settings-tab-content-wrapper');
        if (parentCard) {
            parentCard.style.setProperty('display', 'block', 'important');
            parentCard.style.setProperty('min-height', '400px', 'important');
            parentCard.style.setProperty('position', 'relative', 'important');
            parentCard.style.setProperty('width', '100%', 'important');
            parentCard.style.setProperty('overflow', 'visible', 'important');

            // Ensure active content is inside the wrapper
            if (!parentCard.contains(activeContent)) {
                console.error('Settings tabs: Active content is not inside wrapper!');
            }
        }

        // Force multiple reflows to ensure styles are applied
        void activeContent.offsetHeight;
        void activeContent.offsetWidth;

        // Wait a bit and check again
        setTimeout(() => {
            console.log('Settings tabs: After timeout - offsetHeight:', activeContent.offsetHeight);
            console.log('Settings tabs: After timeout - scrollHeight:', activeContent.scrollHeight);
            if (activeContent.offsetHeight === 0) {
                // If still 0, try forcing a layout recalculation
                activeContent.style.setProperty('display', 'none', 'important');
                void activeContent.offsetHeight;
                activeContent.style.setProperty('display', 'block', 'important');
                void activeContent.offsetHeight;
            }
        }, 100);

        console.log('Settings tabs: Active content element:', activeContent);
        console.log('Settings tabs: Active content ID:', activeContent.id);
        console.log('Settings tabs: Active content classes:', activeContent.className);
        console.log('Settings tabs: Active content innerHTML length:', activeContent.innerHTML.length);
        console.log('Settings tabs: Active content display (computed):', window.getComputedStyle(activeContent).display);
        console.log('Settings tabs: Active content visibility (computed):', window.getComputedStyle(activeContent).visibility);
        console.log('Settings tabs: Active content height (computed):', window.getComputedStyle(activeContent).height);
        console.log('Settings tabs: Active content min-height (computed):', window.getComputedStyle(activeContent).minHeight);
        console.log('Settings tabs: Active content parent:', activeContent.parentElement);
        console.log('Settings tabs: Active content parent display:', activeContent.parentElement ? window.getComputedStyle(activeContent.parentElement).display : 'N/A');
        console.log('Settings tabs: Active content offsetHeight:', activeContent.offsetHeight);
        console.log('Settings tabs: Active content scrollHeight:', activeContent.scrollHeight);
        console.log('Settings tabs: Active content first child:', activeContent.firstElementChild);
        console.log('Settings tabs: Active content first child display:', activeContent.firstElementChild ? window.getComputedStyle(activeContent.firstElementChild).display : 'N/A');

        // Update button states
        tabButtons.forEach(button => {
            const isActive = button.dataset.settingsTab === tabName;
            button.classList.toggle('active', isActive);

            // Update icon box colors
            const iconBox = button.querySelector('.w-8, .w-10');
            const icon = button.querySelector('.fas');
            const label = button.querySelector('span');

            if (isActive) {
                if (iconBox) {
                    iconBox.className = iconBox.className.replace(/bg-\w+-\d+/g, '') + ' bg-indigo-100';
                }
                if (icon) {
                    icon.className = icon.className.replace(/text-\w+-\d+/g, '') + ' text-indigo-600';
                }
                if (label) {
                    label.className = label.className.replace(/text-\w+-\d+/g, '') + ' text-indigo-600 font-semibold';
                }
            } else {
                if (iconBox) {
                    iconBox.className = iconBox.className.replace(/bg-\w+-\d+/g, '') + ' bg-gray-100';
                }
                if (icon) {
                    icon.className = icon.className.replace(/text-\w+-\d+/g, '') + ' text-gray-600';
                }
                if (label) {
                    label.className = label.className.replace(/text-\w+-\d+/g, '').replace(/font-semibold/g, '');
                }
            }
        });

        if (!skipSave) {
            localStorage.setItem('settingsActiveTab', tabName);
            window.location.hash = tabName;
        }
    };

    // Add click handlers
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.dataset.settingsTab;
            console.log('Settings tabs: Button clicked:', tabName);
            showTab(tabName);
        });
    });

    // Initialize with initial tab
    showTab(initialTab, { skipSave: true });

    // Handle hash changes
    window.addEventListener('hashchange', function() {
        const hashTab = pickValid(window.location.hash.replace('#', ''));
        if (hashTab) {
            showTab(hashTab, { skipSave: true });
        }
    });
});

// Role management
function editRole(roleId, roleName, permissions) {
    // Set form action
    document.getElementById('editRoleForm').action = `/settings/roles/${roleId}`;

    // Set role name
    document.getElementById('editRoleName').value = roleName;

    // Clear all checkboxes first
    document.querySelectorAll('.edit-permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Check permissions that are assigned to this role
    permissions.forEach(permission => {
        const checkbox = document.querySelector(`.edit-permission-checkbox[value="${permission}"]`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });

    // Show modal
    document.getElementById('editRoleModal').classList.remove('hidden');
}

function closeEditRoleModal() {
    document.getElementById('editRoleModal').classList.add('hidden');
}

function deleteRole(roleId, roleName) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus role "${roleName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/settings/roles/${roleId}`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
