@extends('layouts.app')

@section('title', 'VLAN Database')

@section('content')
<div class="space-y-6 lg:space-y-8" x-data="{ showCreateModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-tags"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">VLAN Database</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Atur VLAN dan tujuan penggunaannya</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('manage-vlan')
            <button @click="showCreateModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah VLAN
            </button>
            @endcan
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
        <form method="GET" action="{{ route('vlans.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">
                    <i class="fas fa-search mr-1"></i>Cari
                </label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="VLAN ID, nama, atau tujuan"
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
                <a href="{{ route('vlans.index') }}"
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
                <thead class="bg-gradient-to-r from-indigo-500 to-blue-500">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">VLAN ID</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Tujuan</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase">Status</th>
                        @can('manage-vlan')
                        <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vlans as $vlan)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $vlan->vlan_id }}</td>
                        <td class="px-4 py-3 text-gray-900">{{ $vlan->nama }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $vlan->purpose ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ Str::limit($vlan->description ?? '-', 50) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $vlan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $vlan->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        @can('manage-vlan')
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openEditModal({{ $vlan->id }}, '{{ $vlan->nama }}', '{{ $vlan->purpose ?? '' }}', '{{ addslashes($vlan->description ?? '') }}', {{ $vlan->is_active ? 'true' : 'false' }})" class="px-3 py-1.5 text-xs font-semibold bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('vlans.destroy', $vlan) }}" method="POST" class="inline delete-form" data-message="Yakin ingin menghapus VLAN ini?">
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
                        <td colspan="{{ (auth()->user() && auth()->user()->can('manage-vlan')) ? 6 : 5 }}" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-tags text-5xl mb-3"></i>
                            <p class="text-sm font-medium">Belum ada VLAN</p>
                            @can('manage-vlan')
                            <p class="text-xs mt-1">Klik tombol "Tambah VLAN" untuk menambahkan</p>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vlans->hasPages())
        <div class="px-4 py-4 border-t border-gray-200">
            {{ $vlans->links() }}
        </div>
        @endif
    </div>

    <!-- Create Modal -->
    @can('manage-vlan')
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
                <h3 class="text-lg font-bold text-gray-900">Tambah VLAN Baru</h3>
            </div>
            <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('vlans.store') }}" class="px-6 py-4 space-y-4">
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
                <label class="block text-xs font-semibold text-gray-700 mb-2">VLAN ID <span class="text-red-500">*</span></label>
                <input type="number" name="vlan_id" value="{{ old('vlan_id') }}" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: 100" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: Internet" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Tujuan</label>
                <input type="text" name="purpose" value="{{ old('purpose') }}" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Contoh: Internet, IPTV, VoIP">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                          placeholder="Deskripsi VLAN (opsional)">{{ old('description') }}</textarea>
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
    @endcan

    <!-- Edit Modal -->
    @can('manage-vlan')
<div x-data="{ showEditModal: false, editVlanId: null, editVlanName: '', editVlanPurpose: '', editVlanDescription: '', editVlanActive: true }"
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
                <h3 class="text-lg font-bold text-gray-900" x-text="'Edit VLAN ' + editVlanId"></h3>
            </div>
            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="editVlanForm" method="POST" class="px-6 py-4 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="nama" x-model="editVlanName" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Tujuan</label>
                <input type="text" name="purpose" x-model="editVlanPurpose" 
                       class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="3" x-model="editVlanDescription"
                          class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                <select name="is_active" x-model="editVlanActive" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

<script>
function openEditModal(id, nama, purpose, description, isActive) {
    const modal = Alpine.$data(document.querySelector('[x-data*="showEditModal"]'));
    modal.editVlanId = id;
    modal.editVlanName = nama;
    modal.editVlanPurpose = purpose || '';
    modal.editVlanDescription = description || '';
    modal.editVlanActive = isActive;
    modal.showEditModal = true;
    
    // Set form action
    const form = document.getElementById('editVlanForm');
    if (form) {
        form.action = '{{ route("vlans.update", ":id") }}'.replace(':id', id);
    }
}
</script>
@endcan
@endsection
