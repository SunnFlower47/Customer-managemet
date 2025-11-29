@extends('layouts.app')

@section('title', 'Admin - Payment Proof Detail')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Payment Proof Verification</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">
                    Submitted by {{ $paymentProof->pelanggan->nama }} on {{ $paymentProof->created_at->format('d M Y H:i') }}
                </p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <span class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl
                @if($paymentProof->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                @elseif($paymentProof->status == 'verified') bg-green-100 text-green-800 border border-green-200
                @else bg-red-100 text-red-800 border border-red-200
                @endif">
                {{ ucfirst($paymentProof->status) }}
            </span>
            <a href="{{ route('admin.payment-proofs.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Payment Information</p>
                    <h2 class="text-base font-semibold text-gray-900">Informasi pembayaran</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Payment Code</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $paymentProof->pembayaran->kode_pembayaran }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Amount</label>
                        <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($paymentProof->pembayaran->harga_paket, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Due Date</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $paymentProof->pembayaran->tanggal_jatuh_tempo ? $paymentProof->pembayaran->tanggal_jatuh_tempo->format('d M Y') : 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Package</label>
                        <p class="text-sm font-semibold text-gray-900">{{ $paymentProof->pembayaran->paket->nama_paket ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Payment Proof File</p>
                    <h2 class="text-base font-semibold text-gray-900">File bukti pembayaran</h2>
                </div>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-4 sm:p-6 text-center bg-gray-50">
                    @if(in_array($paymentProof->file_type, ['image/jpeg', 'image/jpg', 'image/png']))
                        <img src="{{ $paymentProof->file_url }}" alt="Payment Proof" class="mx-auto max-h-96 rounded-xl shadow-lg">
                    @elseif($paymentProof->file_type == 'application/pdf')
                        <div class="text-center">
                            <i class="fas fa-file-pdf text-red-500 text-5xl sm:text-6xl mb-4"></i>
                            <p class="text-sm text-gray-700 mb-4 font-semibold">{{ $paymentProof->file_name }}</p>
                            <a href="{{ $paymentProof->file_url }}" target="_blank" class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:shadow-lg transition text-sm font-semibold">
                                <i class="fas fa-external-link-alt mr-2"></i>View PDF
                            </a>
                        </div>
                    @else
                        <div class="text-center">
                            <i class="fas fa-file text-gray-400 text-5xl sm:text-6xl mb-4"></i>
                            <p class="text-sm text-gray-700 mb-4 font-semibold">{{ $paymentProof->file_name }}</p>
                            <a href="{{ route('admin.payment-proofs.download', $paymentProof) }}" class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:shadow-lg transition text-sm font-semibold">
                                <i class="fas fa-download mr-2"></i>Download File
                            </a>
                        </div>
                    @endif
                </div>
                <div class="text-center">
                    <a href="{{ route('admin.payment-proofs.download', $paymentProof) }}" class="inline-flex items-center px-5 py-3 border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition text-sm font-semibold">
                        <i class="fas fa-download mr-2"></i>Download Original File
                    </a>
                </div>
            </div>

            @if($paymentProof->admin_notes)
            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Admin Notes</p>
                    <h2 class="text-base font-semibold text-gray-900">Catatan verifikasi</h2>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                    <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $paymentProof->admin_notes }}</p>
                    @if($paymentProof->verifiedBy)
                        <p class="mt-3 text-xs text-gray-500 pt-3 border-t border-gray-200">
                            By {{ $paymentProof->verifiedBy->name }} on {{ $paymentProof->verified_at->format('d M Y H:i') }}
                        </p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            @if($paymentProof->status == 'pending')
            @can('verify-payment-proof')
            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Verification Actions</p>
                    <h2 class="text-base font-semibold text-gray-900">Aksi verifikasi</h2>
                </div>

                <form method="POST" action="{{ route('admin.payment-proofs.verify', $paymentProof) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="admin_notes" class="block text-sm font-semibold text-gray-700 mb-2">Verification Notes</label>
                        <textarea name="admin_notes" id="admin_notes" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" placeholder="Add verification notes..."></textarea>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                            <i class="fas fa-check mr-2"></i>Verify Payment
                        </button>
                    </div>
                </form>

                <form method="POST" action="{{ route('admin.payment-proofs.reject', $paymentProof) }}" class="space-y-4 pt-4 border-t border-gray-200">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="reject_notes" class="block text-sm font-semibold text-gray-700 mb-2">Rejection Reason</label>
                        <textarea name="admin_notes" id="reject_notes" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" placeholder="Explain why this payment proof is rejected..." required></textarea>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                            <i class="fas fa-times mr-2"></i>Reject Payment
                        </button>
                    </div>
                </form>
            </div>
            @endcan
            @endif

            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Payment Proof Information</p>
                    <h2 class="text-base font-semibold text-gray-900">Informasi file</h2>
                </div>
                <dl class="space-y-3 text-sm">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">File Name</dt>
                        <dd class="text-sm font-semibold text-gray-900 truncate">{{ $paymentProof->file_name }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">File Type</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $paymentProof->file_type }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">File Size</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $paymentProof->formatted_file_size }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Submission Method</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $paymentProof->submission_method)) }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Submitted</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $paymentProof->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    @if($paymentProof->verified_at)
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Verified</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $paymentProof->verified_at->format('d M Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-green-500 font-semibold">Customer Information</p>
                    <h2 class="text-base font-semibold text-gray-900">Data pelanggan</h2>
                </div>
                <dl class="space-y-3 text-sm">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Name</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $paymentProof->pelanggan->nama }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Phone</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $paymentProof->pelanggan->no_hp }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Address</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $paymentProof->pelanggan->alamat }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
