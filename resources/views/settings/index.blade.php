@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-cog mr-3 text-blue-600"></i>Pengaturan Sistem
            </h1>
            <p class="mt-2 text-gray-600">Kelola pengaturan sistem, backup database, dan manajemen role</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-8">
            <!-- Mobile Grid Layout -->
            <div class="grid grid-cols-2 gap-3 sm:hidden">
                <button onclick="showTab('profile')" id="tab-profile" class="tab-button-mobile active">
                    <div class="flex flex-col items-center p-4">
                        <i class="fas fa-user text-2xl mb-2"></i>
                        <span class="text-sm font-medium">Profil Akun</span>
                    </div>
                </button>
                <button onclick="showTab('backup')" id="tab-backup" class="tab-button-mobile">
                    <div class="flex flex-col items-center p-4">
                        <i class="fas fa-database text-2xl mb-2"></i>
                        <span class="text-sm font-medium">Backup Database</span>
                    </div>
                </button>
                <button onclick="showTab('roles')" id="tab-roles" class="tab-button-mobile">
                    <div class="flex flex-col items-center p-4">
                        <i class="fas fa-shield-alt text-2xl mb-2"></i>
                        <span class="text-sm font-medium">Role & Permission</span>
                    </div>
                </button>
                <button onclick="showTab('company')" id="tab-company" class="tab-button-mobile">
                    <div class="flex flex-col items-center p-4">
                        <i class="fas fa-building text-2xl mb-2"></i>
                        <span class="text-sm font-medium">Profil Perusahaan</span>
                    </div>
                </button>
                @can('view-audit-trail')
                <button onclick="showTab('audit')" id="tab-audit" class="tab-button-mobile">
                    <div class="flex flex-col items-center p-4">
                        <i class="fas fa-history text-2xl mb-2"></i>
                        <span class="text-sm font-medium">Audit Trail</span>
                    </div>
                </button>
                @endcan
            </div>

            <!-- Desktop Horizontal Layout -->
            <nav class="hidden sm:flex flex-wrap space-x-2 lg:space-x-8" aria-label="Tabs">
                <button onclick="showTab('profile')" id="tab-profile-desktop" class="tab-button active">
                    <i class="fas fa-user mr-2"></i><span>Profil Akun</span>
                </button>
                <button onclick="showTab('backup')" id="tab-backup-desktop" class="tab-button">
                    <i class="fas fa-database mr-2"></i><span>Backup Database</span>
                </button>
                <button onclick="showTab('roles')" id="tab-roles-desktop" class="tab-button">
                    <i class="fas fa-shield-alt mr-2"></i><span>Role & Permission</span>
                </button>
                <button onclick="showTab('company')" id="tab-company-desktop" class="tab-button">
                    <i class="fas fa-building mr-2"></i><span>Profil Perusahaan</span>
                </button>
                @can('view-audit-trail')
                <button onclick="showTab('audit')" id="tab-audit-desktop" class="tab-button">
                    <i class="fas fa-history mr-2"></i><span>Audit Trail</span>
                </button>
                @endcan
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="bg-white rounded-lg shadow">
            <!-- Profile Tab -->
            <div id="content-profile" class="tab-content">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">
                        <i class="fas fa-user-edit mr-2 text-blue-600"></i>Update Profil Akun
                    </h2>

                    <form action="{{ route('settings.update-profile') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ auth()->user()?->name ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ auth()->user()?->email ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru (Opsional)</label>
                                <input type="password" name="password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-150">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Backup Tab -->
            <div id="content-backup" class="tab-content hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">
                        <i class="fas fa-database mr-2 text-blue-600"></i>Backup Database
                    </h2>

                    <!-- Create Backup -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Buat Backup Manual</h3>

                        <!-- Success Message -->
                        @if(session('success'))
                            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            </div>
                        @endif

                        <!-- Warning Message -->
                        @if(session('warning'))
                            <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                            </div>
                        @endif

                        <!-- Error Message -->
                        @if(session('error'))
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        <!-- Backup Error Message -->
                        @if($errors && $errors->has('backup'))
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                                <i class="fas fa-exclamation-triangle mr-2"></i>{{ $errors->first('backup') }}
                            </div>
                        @endif

                        <!-- Single Effective Backup -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-2">
                                <i class="fas fa-database mr-2 text-blue-600"></i>Backup Database
                            </h4>
                            <p class="text-sm text-gray-600 mb-4">
                                Backup database yang kompatibel dengan sistem restore dan phpMyAdmin import. 
                                File backup akan otomatis dioptimalkan untuk kedua keperluan.
                            </p>
                            
                            <form action="{{ route('settings.create-backup') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                                    <textarea name="notes" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Masukkan catatan untuk backup ini..."></textarea>
                                </div>
                                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-150 font-medium">
                                    <i class="fas fa-download mr-2"></i>Buat Backup Database
                                </button>
                            </form>
                            
                            <div class="mt-4 p-3 bg-blue-50 rounded-md">
                                <p class="text-xs text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <strong>Fitur:</strong> Backup ini dapat digunakan untuk restore sistem dan import phpMyAdmin tanpa error foreign key constraint.
                                </p>
                            </div>
                        </div>
                    </div>


                    <!-- Backup History -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Backup</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($backupHistories as $backup)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $backup->filename }}</div>
                                            @if($backup->notes)
                                            <div class="text-sm text-gray-500">{{ $backup->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $backup->formatted_file_size }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $backup->type === 'manual' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst($backup->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $backup->creator ? $backup->creator->name : 'System' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $backup->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route('settings.backup.download', $backup) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-download mr-1"></i>Download
                                            </a>
                                            @can('restore-database')
                                            <button onclick="confirmRestore('{{ $backup->id }}', '{{ $backup->filename }}')"
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-undo mr-1"></i>Restore
                                            </button>
                                            @endcan
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada backup
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles Tab -->
            <div id="content-roles" class="tab-content hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">
                        <i class="fas fa-shield-alt mr-2 text-blue-600"></i>Role & Permission Management
                    </h2>

                    <!-- Create Role -->
                    @can('manage-roles')
                    <div class="bg-gray-50 rounded-lg p-6 mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Buat Role Baru</h3>
                        <form action="{{ route('settings.create-role') }}" method="POST">
                            @csrf
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Role</label>
                                <input type="text" name="name"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Masukkan nama role" required>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-4">
                                    @foreach($permissions as $permission)
                                    <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-150">
                                    <i class="fas fa-plus mr-2"></i>Buat Role
                                </button>
                            </div>
                        </form>
                    </div>
                    @endcan

                    <!-- Roles List -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Role</h3>
                        <div class="space-y-4">
                            @foreach($roles as $role)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900">{{ ucfirst($role->name) }}</h4>
                                        <p class="text-sm text-gray-500">{{ $role->permissions->count() }} permissions</p>
                                    </div>
                                    @can('manage-roles')
                                    <div class="flex space-x-2">
                                        <button onclick="editRole('{{ $role->id }}', '{{ $role->name }}', {{ $role->permissions->pluck('name')->toJson() }})"
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-md hover:bg-blue-50 transition duration-150"
                                                title="Edit Role">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteRole('{{ $role->id }}', '{{ $role->name }}')"
                                                class="text-red-600 hover:text-red-900 p-2 rounded-md hover:bg-red-50 transition duration-150"
                                                title="Delete Role">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @endcan
                                </div>
                                <div class="mt-2">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($role->permissions as $permission)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $permission->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Profile Tab -->
            <div id="content-company" class="tab-content hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">
                        <i class="fas fa-building mr-2 text-blue-600"></i>Profil Perusahaan
                    </h2>

                    <form action="{{ route('settings.update-company-profile') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Perusahaan (Utama)</label>
                                <input type="text" name="nama_perusahaan" value="{{ $companyProfile->nama_perusahaan ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <p class="text-xs text-gray-500 mt-1">Nama yang akan digunakan di sistem</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Support</label>
                                <input type="email" name="email_support" value="{{ $companyProfile->email_support ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap Perusahaan</label>
                            <input type="text" name="nama_lengkap_perusahaan" value="{{ $companyProfile->nama_lengkap_perusahaan ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Contoh: Baraya Citra Mandiri">
                            <p class="text-xs text-gray-500 mt-1">Nama lengkap untuk dokumen resmi dan tampilan</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Inisial Perusahaan</label>
                                <input type="text" name="inisial_perusahaan" value="{{ $companyProfile->inisial_perusahaan ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Contoh: BCM" maxlength="10">
                                <p class="text-xs text-gray-500 mt-1">Inisial untuk logo dan branding (auto-generate jika kosong)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Prefix Kode Pembayaran</label>
                                <input type="text" name="payment_code_prefix" value="{{ $companyProfile->payment_code_prefix ?? 'PAY' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       maxlength="3" placeholder="PAY" required>
                                <p class="text-xs text-gray-500 mt-1">Maksimal 3 karakter (contoh: PAY, INV, BIL)</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea name="alamat" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ $companyProfile->alamat ?? '' }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Kontak</label>
                                <input type="text" name="nomor_kontak" value="{{ $companyProfile->nomor_kontak ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                                <input type="text" name="whatsapp" value="{{ $companyProfile->whatsapp ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                                <input type="url" name="website" value="{{ $companyProfile->website ?? '' }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <!-- Website field moved up -->
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo Perusahaan</label>
                            <input type="file" name="logo" accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @if($companyProfile && $companyProfile->logo_path)
                            <div class="mt-2">
                                <img src="{{ $companyProfile->logo_url }}?v={{ time() }}" alt="Current Logo" class="h-20 w-auto object-contain border border-gray-200 rounded-lg p-2">
                            </div>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $companyProfile->deskripsi ?? '' }}</textarea>
                        </div>


                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-150">
                                <i class="fas fa-save mr-2"></i>Simpan Profil Perusahaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Audit Trail Tab -->
            @can('view-audit-trail')
            <div id="content-audit" class="tab-content hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">
                        <i class="fas fa-history mr-2 text-blue-600"></i>Audit Trail
                    </h2>
                    
                    <div class="mb-6">
                        <a href="{{ route('audit-trails.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Buka Audit Trail
                        </a>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>Informasi Audit Trail
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h4 class="font-medium text-gray-900 mb-2">
                                    <i class="fas fa-eye mr-2 text-green-600"></i>Yang Dicatat
                                </h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Perubahan data Pelanggan</li>
                                    <li>• Perubahan data Pembayaran</li>
                                    <li>• Perubahan data User</li>
                                    <li>• Perubahan data Paket</li>
                                </ul>
                            </div>
                            
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h4 class="font-medium text-gray-900 mb-2">
                                    <i class="fas fa-shield-alt mr-2 text-purple-600"></i>Keamanan
                                </h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• IP Address tercatat</li>
                                    <li>• User Agent tercatat</li>
                                    <li>• Timestamp lengkap</li>
                                    <li>• Old & New Values</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start">
                                <i class="fas fa-lightbulb text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-blue-900 mb-1">Tips</h4>
                                    <p class="text-sm text-blue-800">
                                        Audit Trail membantu melacak semua perubahan data dalam sistem. 
                                        Klik tombol "Buka Audit Trail" di atas untuk melihat log lengkap aktivitas sistem.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div id="restoreModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Restore</h3>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <!-- Restore Error Message -->
            @if($errors && $errors->has('restore'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ $errors->first('restore') }}
                </div>
            @endif

            <p class="text-sm text-gray-600 mb-6">
                Apakah Anda yakin ingin me-restore database dari backup <span id="backupFilename" class="font-medium"></span>?
                <br><br>
                <strong class="text-red-600">PERINGATAN:</strong> Tindakan ini akan mengganti semua data database dengan data dari backup.
            </p>
            <form id="restoreForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="confirm_restore" value="1" required class="mr-2">
                        <span class="text-sm text-gray-700">Saya mengerti dan setuju untuk me-restore database</span>
                    </label>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRestoreModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Ya, Restore
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div id="editRoleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Role</h3>
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Role</label>
                    <input type="text" id="editRoleName" name="name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-4">
                        @foreach($permissions as $permission)
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                   class="edit-permission-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditRoleModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
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
    @apply bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105;
}

.tab-button-mobile.active {
    @apply bg-blue-50 border-blue-300 shadow-md transform scale-105;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    position: relative;
}

.tab-button-mobile.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    border-radius: 0 0 4px 4px;
}

.tab-button-mobile.active .fas {
    @apply text-blue-600;
}

.tab-button-mobile.active span {
    @apply text-blue-600 font-semibold;
}

.tab-button-mobile:hover:not(.active) {
    @apply border-gray-300;
}

.tab-content {
    @apply transition duration-150;
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
    .tab-button {
        @apply px-2 py-1 text-xs;
    }
}
</style>

<script>
// Tab switching
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tab buttons (both mobile and desktop)
    document.querySelectorAll('.tab-button, .tab-button-mobile').forEach(button => {
        button.classList.remove('active');
    });

    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');

    // Add active class to clicked tab button (both mobile and desktop)
    const mobileButton = document.getElementById('tab-' + tabName);
    const desktopButton = document.getElementById('tab-' + tabName + '-desktop');

    if (mobileButton) mobileButton.classList.add('active');
    if (desktopButton) desktopButton.classList.add('active');

    // Save active tab to localStorage
    localStorage.setItem('settingsActiveTab', tabName);

    // Update URL hash
    window.location.hash = tabName;
}

// Initialize tab on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check URL hash first
    const hash = window.location.hash.substring(1);
    const savedTab = localStorage.getItem('settingsActiveTab');
    const urlParams = new URLSearchParams(window.location.search);
    const tabFromUrl = urlParams.get('tab');

    // Priority: URL param > URL hash > localStorage > default (profile)
    const activeTab = tabFromUrl || hash || savedTab || 'profile';

    // Validate tab exists
    const validTabs = ['profile', 'backup', 'roles', 'company'];
    const finalTab = validTabs.includes(activeTab) ? activeTab : 'profile';

    // Force show the correct tab
    showTab(finalTab);

    console.log('Settings tab initialized:', finalTab);
});

// Restore confirmation
function confirmRestore(backupId, filename) {
    document.getElementById('backupFilename').textContent = filename;
    document.getElementById('restoreForm').action = `/settings/backup/${backupId}/restore`;
    document.getElementById('restoreModal').classList.remove('hidden');

    // Clear any previous error/success messages
    const successMsg = document.querySelector('#restoreModal .bg-green-100');
    const errorMsg = document.querySelector('#restoreModal .bg-red-100');
    if (successMsg) successMsg.remove();
    if (errorMsg) errorMsg.remove();
}

function closeRestoreModal() {
    document.getElementById('restoreModal').classList.add('hidden');

    // Clear any error/success messages when closing
    const successMsg = document.querySelector('#restoreModal .bg-green-100');
    const errorMsg = document.querySelector('#restoreModal .bg-red-100');
    if (successMsg) successMsg.remove();
    if (errorMsg) errorMsg.remove();
}

// Auto-close modal after successful restore
document.addEventListener('DOMContentLoaded', function() {
    // Check if there's a success or error message
    const successMsg = document.querySelector('.bg-green-100');
    const errorMsg = document.querySelector('.bg-red-100');

    if (successMsg || errorMsg) {
        // Auto-close modal if it's open
        const modal = document.getElementById('restoreModal');
        if (modal && !modal.classList.contains('hidden')) {
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 3000); // Close after 3 seconds
        }
    }
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
