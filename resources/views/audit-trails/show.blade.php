@extends('layouts.app')

@section('title', 'Detail Audit Trail - WiFi Billing Management')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Audit Trail</h1>
                <p class="mt-1 text-sm text-gray-600">Informasi lengkap aktivitas sistem</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('audit-trails.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-history mr-2 text-blue-600"></i>Informasi Audit Trail
            </h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user mr-1 text-gray-400"></i>User
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ $auditTrail->user_id ?: 'System' }}</p>
                        @if($auditTrail->user)
                            <p class="text-sm text-gray-500">{{ $auditTrail->user->email }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-cog mr-1 text-gray-400"></i>Event
                        </label>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $auditTrail->event === 'created' ? 'bg-green-100 text-green-800' : ($auditTrail->event === 'updated' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            <i class="fas {{ $auditTrail->event === 'created' ? 'fa-plus' : ($auditTrail->event === 'updated' ? 'fa-edit' : 'fa-trash') }} mr-1"></i>
                            {{ ucfirst($auditTrail->event) }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-table mr-1 text-gray-400"></i>Model Type
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ class_basename($auditTrail->auditable_type) }}</p>
                        @if($auditTrail->auditable_id)
                            <p class="text-sm text-gray-500">Record ID: {{ $auditTrail->auditable_id }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-clock mr-1 text-gray-400"></i>Waktu
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ $auditTrail->created_at->format('d F Y H:i:s') }}</p>
                        <p class="text-sm text-gray-500">{{ $auditTrail->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <!-- Technical Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-info-circle mr-1 text-gray-400"></i>Tags
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ $auditTrail->tags }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-globe mr-1 text-gray-400"></i>IP Address
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ $auditTrail->ip_address ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-desktop mr-1 text-gray-400"></i>User Agent
                        </label>
                        <p class="text-sm text-gray-600 break-all">{{ $auditTrail->user_agent ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Changes -->
            @if($auditTrail->old_values || $auditTrail->new_values)
            <div class="mt-8">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-exchange-alt mr-2 text-blue-600"></i>Perubahan Data
                </h4>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @if($auditTrail->old_values)
                    <div>
                        <h5 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-arrow-left mr-1 text-red-500"></i>Data Lama
                        </h5>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($auditTrail->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif

                    @if($auditTrail->new_values)
                    <div>
                        <h5 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-arrow-right mr-1 text-green-500"></i>Data Baru
                        </h5>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ json_encode($auditTrail->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
