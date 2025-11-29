@extends('layouts.app')

@section('title', 'Speed Profiles')

@section('content')
<div class="space-y-6 lg:space-y-8" x-data="{ showCreateModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-500 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Speed Profiles</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Kelola profile bandwidth download/upload</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('manage-speed-profile')
            <button @click="showCreateModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Profile
            </button>
            @endcan
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <form method="GET" action="{{ route('speed-profiles.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i>Cari
                </label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Nama atau deskripsi"
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-filter mr-1"></i>Status
                </label>
                <select name="is_active"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('speed-profiles.index') }}"
                   class="px-4 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-teal-500 to-indigo-500">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Download</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Upload</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Nama di OLT</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                        @can('manage-speed-profile')
                        <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($profiles as $profile)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-900">{{ $profile->nama }}</p>
                            @if($profile->description)
                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($profile->description, 40) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-900">
                            <span class="font-semibold">{{ number_format($profile->download_speed/1000, 2) }}</span> Mbps
                        </td>
                        <td class="px-4 py-3 text-gray-900">
                            <span class="font-semibold">{{ number_format($profile->upload_speed/1000, 2) }}</span> Mbps
                        </td>
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $profile->profile_name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $profile->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $profile->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        @can('manage-speed-profile')
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openEditProfileModal({{ $profile->id }}, '{{ addslashes($profile->nama) }}', {{ $profile->download_speed }}, {{ $profile->upload_speed }}, '{{ addslashes($profile->profile_name ?? '') }}', '{{ addslashes($profile->description ?? '') }}', {{ $profile->is_active ? 'true' : 'false' }})" class="px-3 py-1.5 text-xs font-semibold bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('speed-profiles.destroy', $profile) }}" method="POST" class="inline delete-form" data-message="Yakin ingin menghapus profile ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endcan
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ (auth()->user() && auth()->user()->can('manage-speed-profile')) ? 6 : 5 }}" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-tachometer-alt text-5xl mb-3"></i>
                            <p class="text-sm font-medium">Belum ada speed profile</p>
                            @can('manage-speed-profile')
                            <p class="text-xs mt-1">Klik tombol "Tambah Profile" untuk menambahkan</p>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($profiles->hasPages())
        <div class="px-4 py-4 border-t border-gray-200">
            {{ $profiles->links() }}
        </div>
        @endif
    </div>

    <!-- Create Modal -->
    @can('manage-speed-profile')
    <div x-show="showCreateModal"
     x-cloak
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4"
     @click.self="showCreateModal = false"
     @keydown.escape.window="showCreateModal = false">
    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full mx-auto overflow-hidden"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">TAMBAH</p>
                <h3 class="text-lg font-bold text-gray-900">Tambah Speed Profile</h3>
            </div>
            <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('speed-profiles.store') }}" class="px-6 py-4 space-y-4">
            @csrf
            
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-2"></i>
                    <div class="flex-1">
                        <ul class="text-xs text-red-700 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Nama Profile <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: 10Mbps" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Download (Kbps) <span class="text-red-500">*</span></label>
                    <input type="number" name="download_speed" value="{{ old('download_speed') }}" 
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="10000" min="1" required>
                    <p class="text-xs text-gray-500 mt-1">Contoh: 10000 = 10 Mbps</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Upload (Kbps) <span class="text-red-500">*</span></label>
                    <input type="number" name="upload_speed" value="{{ old('upload_speed') }}" 
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="5000" min="1" required>
                    <p class="text-xs text-gray-500 mt-1">Contoh: 5000 = 5 Mbps</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Nama di OLT</label>
                <input type="text" name="profile_name" value="{{ old('profile_name') }}" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Nama profile di OLT (opsional)">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                          placeholder="Deskripsi profile (opsional)">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" @click="showCreateModal = false" 
                        class="px-5 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
    @endcan

    <!-- Edit Modal -->
    @can('manage-speed-profile')
    <div x-data="{ showEditModal: false, editProfileId: null, editProfileName: '', editDownloadSpeed: 0, editUploadSpeed: 0, editProfileNameOlt: '', editDescription: '', editIsActive: true }"
         x-show="showEditModal"
     x-cloak
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4"
     @click.self="showEditModal = false"
     @keydown.escape.window="showEditModal = false">
    <div class="bg-white rounded-2xl w-full max-w-xl mx-4 shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <p class="text-xs uppercase tracking-wide text-blue-500 font-semibold">EDIT</p>
                <h3 class="text-lg font-bold text-gray-900" x-text="'Edit Profile ' + editProfileName"></h3>
            </div>
            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editProfileForm" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Nama Profile <span class="text-red-500">*</span></label>
                <input type="text" name="nama" x-model="editProfileName" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Download (Kbps) <span class="text-red-500">*</span></label>
                    <input type="number" name="download_speed" x-model="editDownloadSpeed" 
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="1" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Upload (Kbps) <span class="text-red-500">*</span></label>
                    <input type="number" name="upload_speed" x-model="editUploadSpeed" 
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="1" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Nama di OLT</label>
                <input type="text" name="profile_name" x-model="editProfileNameOlt" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3" x-model="editDescription"
                          class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                <select name="is_active" x-model="editIsActive" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option :value="true">Aktif</option>
                    <option :value="false">Nonaktif</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" @click="showEditModal = false" 
                        class="px-5 py-2.5 text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 text-sm font-semibold bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
    @endcan
</div>
@endsection

<script>
function openEditProfileModal(id, nama, downloadSpeed, uploadSpeed, profileNameOlt, description, isActive) {
    const modalElement = document.querySelector('[x-data*="editProfileId"]');
    if (!modalElement) return;
    
    const modal = Alpine.$data(modalElement);
    modal.editProfileId = id;
    modal.editProfileName = nama;
    modal.editDownloadSpeed = downloadSpeed;
    modal.editUploadSpeed = uploadSpeed;
    modal.editProfileNameOlt = profileNameOlt || '';
    modal.editDescription = description || '';
    modal.editIsActive = isActive;
    modal.showEditModal = true;
    
    // Set form action
    const form = document.getElementById('editProfileForm');
    if (form) {
        form.action = '{{ route("speed-profiles.update", ":id") }}'.replace(':id', id);
    }
}
</script>
