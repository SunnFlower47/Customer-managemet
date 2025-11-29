@extends('layouts.app')

@section('title', 'Audit Trail - WiFi Billing Management')

@section('content')
<div class="page-shell">
    <div class="space-y-6 lg:space-y-8">
        <!-- Header -->
        <div class="page-header">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <h1 class="page-header__title text-slate-900">Audit Trail</h1>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">Log aktivitas sistem dan perubahan data</p>
                </div>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('audit-trails.export', request()->query()) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:shadow-lg transition text-sm font-semibold">
                    <i class="fas fa-file-csv"></i>
                    <span class="hidden sm:inline">Export CSV</span>
                </a>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="app-card">
            <form method="GET" action="{{ route('audit-trails.index') }}" class="space-y-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-xs font-semibold text-gray-700 mb-2">
                        <i class="fas fa-search mr-1.5 text-indigo-500"></i>Pencarian
                    </label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white"
                           placeholder="Cari berdasarkan event, model type, atau tags...">
                </div>

                <!-- Filters -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label for="event" class="block text-xs font-semibold text-gray-700 mb-2">
                            <i class="fas fa-cog mr-1.5 text-indigo-500"></i>Event
                        </label>
                        <select name="event"
                                id="event"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                            <option value="">Semua Event</option>
                            @foreach($events as $event)
                                <option value="{{ $event }}" {{ request('event') === $event ? 'selected' : '' }}>
                                    {{ ucfirst($event) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="auditable_type" class="block text-xs font-semibold text-gray-700 mb-2">
                            <i class="fas fa-table mr-1.5 text-indigo-500"></i>Model Type
                        </label>
                        <select name="auditable_type"
                                id="auditable_type"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                            <option value="">Semua Model</option>
                            @foreach($auditableTypes as $type)
                                <option value="{{ $type }}" {{ request('auditable_type') === $type ? 'selected' : '' }}>
                                    {{ class_basename($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="user_id" class="block text-xs font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-1.5 text-indigo-500"></i>User
                        </label>
                        <select name="user_id"
                                id="user_id"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                            <option value="">Semua User</option>
                            @foreach($users as $userId => $userName)
                                <option value="{{ $userId }}" {{ request('user_id') == $userId ? 'selected' : '' }}>
                                    {{ $userName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-xs font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1.5 text-indigo-500"></i>Dari Tanggal
                        </label>
                        <input type="date"
                               name="date_from"
                               id="date_from"
                               value="{{ request('date_from') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    </div>

                    <div>
                        <label for="date_to" class="block text-xs font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar mr-1.5 text-indigo-500"></i>Sampai Tanggal
                        </label>
                        <input type="date"
                               name="date_to"
                               id="date_to"
                               value="{{ request('date_to') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    </div>
                </div>

                <div class="inline-actions pt-4 border-t border-gray-200">
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                        <i class="fas fa-filter mr-2"></i>Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Audit Trails Table -->
        <div class="app-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-indigo-500 font-semibold">Log Aktivitas</p>
                    <h2 class="text-base font-semibold text-gray-900">Daftar audit trail sistem</h2>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                    {{ $auditTrails->total() }} total
                </span>
            </div>

            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="data-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-purple-500 to-purple-600">
                        <tr>
                            <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                <i class="fas fa-user mr-2"></i>User
                            </th>
                            <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                <i class="fas fa-cog mr-2"></i>Event
                            </th>
                            <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                <i class="fas fa-table mr-2"></i>Model Type
                            </th>
                            <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                <i class="fas fa-info mr-2"></i>Tags
                            </th>
                            <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                <i class="fas fa-clock mr-2"></i>Waktu
                            </th>
                            <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                                <i class="fas fa-eye mr-2"></i>Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($auditTrails as $audit)
                        <tr class="hover:bg-purple-50 transition cursor-pointer" onclick="window.location.href='{{ route('audit-trails.show', $audit) }}'">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-purple-600">
                                                {{ $audit->user_id ? substr($audit->user_id, 0, 1) : 'S' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3 min-w-0">
                                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $audit->user_id ?: 'System' }}</div>
                                        <div class="text-[11px] text-gray-500 truncate">{{ $audit->ip_address }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $audit->event === 'created' ? 'bg-green-100 text-green-800 border border-green-200' : ($audit->event === 'updated' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-red-100 text-red-800 border border-red-200') }}">
                                    <i class="fas {{ $audit->event === 'created' ? 'fa-plus' : ($audit->event === 'updated' ? 'fa-edit' : 'fa-trash') }} mr-1"></i>
                                    {{ ucfirst($audit->event) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ class_basename($audit->auditable_type) }}</div>
                                @if($audit->auditable_id)
                                    <div class="text-[11px] text-gray-500">ID: {{ $audit->auditable_id }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ $audit->tags }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $audit->created_at->format('d M Y') }}</div>
                                <div class="text-[11px] text-gray-500">{{ $audit->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('audit-trails.show', $audit) }}"
                                   class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 transition"
                                   onclick="event.stopPropagation()">
                                    <i class="fas fa-eye"></i>
                                    <span class="hidden xl:inline">Detail</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-history text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-gray-500 text-sm">Tidak ada audit trail</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Desktop Pagination -->
                @if($auditTrails->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $auditTrails->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
                @endif
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden space-y-2">
                @forelse($auditTrails as $audit)
                <div class="mobile-card border border-gray-200 rounded-xl px-4 py-3">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-semibold text-purple-600">
                                {{ $audit->user_id ? substr($audit->user_id, 0, 1) : 'S' }}
                            </span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $audit->user_id ?: 'System' }}</p>
                                    <p class="text-xs text-gray-500">{{ $audit->ip_address }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold flex-shrink-0 {{ $audit->event === 'created' ? 'bg-green-100 text-green-800 border border-green-200' : ($audit->event === 'updated' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-red-100 text-red-800 border border-red-200') }}">
                                    <i class="fas {{ $audit->event === 'created' ? 'fa-plus' : ($audit->event === 'updated' ? 'fa-edit' : 'fa-trash') }} mr-1"></i>
                                    {{ ucfirst($audit->event) }}
                                </span>
                            </div>
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-table text-xs text-gray-400 w-4"></i>
                                    <span class="text-xs text-gray-700">{{ class_basename($audit->auditable_type) }}</span>
                                    @if($audit->auditable_id)
                                        <span class="text-[10px] text-gray-500">(ID: {{ $audit->auditable_id }})</span>
                                    @endif
                                </div>
                                @if($audit->tags)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-info text-xs text-gray-400 w-4"></i>
                                    <span class="text-xs text-gray-700 truncate">{{ $audit->tags }}</span>
                                </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-xs text-gray-400 w-4"></i>
                                    <span class="text-xs text-gray-700">{{ $audit->created_at->format('d M Y H:i:s') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <a href="{{ route('audit-trails.show', $audit) }}"
                           class="inline-flex items-center justify-center w-full px-3 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-xl hover:shadow-md transition text-xs font-semibold">
                            <i class="fas fa-eye mr-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>
                @empty
                <div class="app-card text-center py-12">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-history text-gray-300 text-4xl mb-4"></i>
                        <h3 class="text-base font-semibold text-gray-900 mb-2">Tidak ada audit trail</h3>
                        <p class="text-sm text-gray-500">Belum ada aktivitas yang tercatat dalam sistem.</p>
                    </div>
                </div>
                @endforelse

                <!-- Mobile Pagination -->
                @if($auditTrails->hasPages())
                <div class="app-card">
                    {{ $auditTrails->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
