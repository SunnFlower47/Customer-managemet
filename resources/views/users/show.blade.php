@extends('layouts.app')

@section('title', 'Detail User - WiFi Billing Management')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">{{ $user->name }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Detail informasi user</p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <span class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }} rounded-xl">
                <i class="fas {{ $user->role === 'admin' ? 'fa-crown' : 'fa-user-tie' }} mr-2 text-xs sm:text-sm"></i>{{ ucfirst($user->role) }}
            </span>
            <a href="{{ route('users.edit', $user) }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-edit mr-2 text-xs sm:text-sm"></i>Edit
            </a>
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Informasi User</p>
                    <h2 class="text-base font-semibold text-gray-900">Data lengkap pengguna</h2>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama Lengkap</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</dt>
                        <dd class="text-sm font-semibold text-gray-900 truncate">{{ $user->email }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Role</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                <i class="fas {{ $user->role === 'admin' ? 'fa-crown' : 'fa-user-tie' }} mr-1 text-[9px]"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-green-100 text-green-800 border border-green-200">
                                <i class="fas fa-check-circle mr-1 text-[9px]"></i>Aktif
                            </span>
                        </dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal Dibuat</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Terakhir Diupdate</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $user->updated_at->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            @if($user->role === 'penagih' && $user->penagih)
            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Informasi Penagih</p>
                    <h2 class="text-base font-semibold text-gray-900">Data penagih terkait</h2>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-700">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama Penagih</dt>
                        <dd class="text-sm font-semibold text-gray-900">
                            @if($user->penagih)
                                {{ $user->penagih->nama }}
                            @else
                                <span class="text-gray-400 italic">Belum ada penagih</span>
                            @endif
                        </dd>
                    </div>
                    @if($user->penagih)
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">No. HP</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $user->penagih->no_hp ?? '-' }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 sm:col-span-2">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Alamat</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $user->penagih->alamat ?? '-' }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Pelanggan</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $user->penagih->pelanggans_count ?? 0 }} pelanggan</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $user->penagih->aktif ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                <i class="fas {{ $user->penagih->aktif ? 'fa-check-circle' : 'fa-times-circle' }} mr-1 text-[9px]"></i>
                                {{ $user->penagih->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif
        </div>

        <div>
            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-purple-500 font-semibold">Statistik Akun</p>
                    <h2 class="text-base font-semibold text-gray-900">Informasi sistem</h2>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Login Terakhir</span>
                        <span class="font-semibold text-gray-900">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Akun Dibuat</span>
                        <span class="font-semibold text-gray-900">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                    @if($user->role === 'penagih' && $user->penagih)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Total Pelanggan</span>
                        <span class="font-semibold text-gray-900">{{ $user->penagih->pelanggans()->count() }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="app-card mt-6">
                <div class="space-y-3">
                    @if($user->role === 'penagih')
                    <a href="{{ route('penagihs.index') }}"
                       class="w-full inline-flex items-center justify-center px-5 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:shadow-lg transition text-sm font-semibold">
                        <i class="fas fa-user-tie mr-2"></i>Lihat Data Penagih
                    </a>
                    @endif

                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-5 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg transition text-sm font-semibold">
                            <i class="fas fa-trash mr-2"></i>Hapus User
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.querySelector('.delete-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus User?',
                text: 'Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    }
});
</script>
@endpush
@endsection
