@extends('layouts.app')

@section('title', 'Admin - Payment Proofs')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-receipt"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Payment Proof Management</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Verifikasi dan kelola bukti pembayaran pelanggan</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('admin.payment-proofs.statistics') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-chart-bar mr-2 text-xs sm:text-sm"></i>Statistics
            </a>
        </div>
    </div>

    <div class="app-card space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter mr-2 text-green-500"></i>Filter & Pencarian
            </h3>
        </div>
        <form method="GET" action="{{ route('admin.payment-proofs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div>
                <label for="submission_method" class="block text-sm font-semibold text-gray-700 mb-2">Submission Method</label>
                <select name="submission_method" id="submission_method" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">All Methods</option>
                    <option value="website_upload" {{ request('submission_method') == 'website_upload' ? 'selected' : '' }}>Website Upload</option>
                    <option value="whatsapp" {{ request('submission_method') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
            </div>

            <div>
                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Customer name, phone, or payment code..." class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Daftar Bukti Pembayaran</p>
                <h2 class="text-base font-semibold text-gray-900">Verifikasi pembayaran pelanggan</h2>
            </div>
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-green-500 to-green-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-receipt mr-2"></i>Payment Code
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Pelanggan
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-file mr-2"></i>File
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-upload mr-2"></i>Method
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-calendar mr-2"></i>Tanggal
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-cog mr-2"></i>Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($paymentProofs as $proof)
                    <tr class="hover:bg-green-50 transition cursor-pointer" onclick="window.location.href='{{ route('admin.payment-proofs.show', $proof) }}'">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-sm font-semibold text-gray-900">{{ $proof->pembayaran->kode_pembayaran }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate max-w-xs">{{ $proof->pelanggan->nama }}</p>
                                <p class="text-xs text-gray-500 truncate max-w-xs">{{ $proof->pelanggan->no_hp }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="min-w-0">
                                <p class="text-xs text-gray-700 truncate max-w-xs">{{ $proof->file_name }}</p>
                                <p class="text-[10px] text-gray-500">{{ $proof->formatted_file_size }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                {{ ucfirst(str_replace('_', ' ', $proof->submission_method)) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-xs text-gray-700">{{ $proof->created_at->format('d M Y') }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                @if($proof->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @elseif($proof->status == 'verified') bg-green-100 text-green-800 border border-green-200
                                @else bg-red-100 text-red-800 border border-red-200
                                @endif">
                                {{ ucfirst($proof->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-receipt text-gray-400 text-4xl mb-2"></i>
                                <p class="text-gray-500">Tidak ada bukti pembayaran</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($paymentProofs->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $paymentProofs->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        <div class="lg:hidden space-y-2">
            @forelse($paymentProofs as $proof)
            <a href="{{ route('admin.payment-proofs.show', $proof) }}" class="mobile-card border border-gray-200 rounded-2xl px-4 py-3 block">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-receipt text-green-600 text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $proof->pembayaran->kode_pembayaran }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $proof->pelanggan->nama }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold flex-shrink-0
                                @if($proof->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @elseif($proof->status == 'verified') bg-green-100 text-green-800 border border-green-200
                                @else bg-red-100 text-red-800 border border-red-200
                                @endif">
                                {{ ucfirst($proof->status) }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                <i class="fas fa-file mr-1 text-[9px]"></i>{{ Str::limit($proof->file_name, 20) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                <i class="fas fa-upload mr-1 text-[9px]"></i>{{ ucfirst(str_replace('_', ' ', $proof->submission_method)) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-calendar mr-1"></i>{{ $proof->created_at->format('d M Y') }}
                            <span class="ml-2">•</span>
                            <span class="ml-2">{{ $proof->formatted_file_size }}</span>
                        </p>
                    </div>
                </div>
            </a>
            @empty
            <div class="app-card text-center py-12">
                <div class="flex flex-col items-center">
                    <i class="fas fa-receipt text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Tidak ada bukti pembayaran</h3>
                    <p class="text-sm text-gray-500">Belum ada bukti pembayaran yang terdaftar.</p>
                </div>
            </div>
            @endforelse

            @if($paymentProofs->hasPages())
                <div class="mt-4">
                    {{ $paymentProofs->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
