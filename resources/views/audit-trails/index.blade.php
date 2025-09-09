@extends('layouts.app')

@section('title', 'Audit Trail - WiFi Billing Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Audit Trail</h1>
                <p class="mt-1 text-sm text-gray-600">Log aktivitas sistem dan perubahan data</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('audit-trails.export', request()->query()) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                    <i class="fas fa-file-csv mr-2"></i>
                    Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('audit-trails.index') }}" class="space-y-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-search mr-1 text-gray-400"></i>Search
                </label>
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ request('search') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                       placeholder="Cari berdasarkan event, model type, atau tags...">
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="event" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-cog mr-1 text-gray-400"></i>Event
                    </label>
                    <select name="event"
                            id="event"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Semua Event</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}" {{ request('event') === $event ? 'selected' : '' }}>
                                {{ ucfirst($event) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="auditable_type" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-table mr-1 text-gray-400"></i>Model Type
                    </label>
                    <select name="auditable_type"
                            id="auditable_type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Semua Model</option>
                        @foreach($auditableTypes as $type)
                            <option value="{{ $type }}" {{ request('auditable_type') === $type ? 'selected' : '' }}>
                                {{ class_basename($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-1 text-gray-400"></i>User
                    </label>
                    <select name="user_id"
                            id="user_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        <option value="">Semua User</option>
                        @foreach($users as $userId => $userName)
                            <option value="{{ $userId }}" {{ request('user_id') == $userId ? 'selected' : '' }}>
                                {{ $userName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1 text-gray-400"></i>Dari Tanggal
                    </label>
                    <input type="date"
                           name="date_from"
                           id="date_from"
                           value="{{ request('date_from') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-1 text-gray-400"></i>Sampai Tanggal
                    </label>
                    <input type="date"
                           name="date_to"
                           id="date_to"
                           value="{{ request('date_to') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Trails Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-history mr-2 text-blue-600"></i>Log Aktivitas
                <span class="ml-2 text-sm font-normal text-gray-500">({{ $auditTrails->total() }} total)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-user mr-1"></i>User ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-cog mr-1"></i>Event
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-table mr-1"></i>Model Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-info mr-1"></i>Tags
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-clock mr-1"></i>Waktu
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-eye mr-1"></i>Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($auditTrails as $audit)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-xs font-semibold text-gray-600">
                                            {{ $audit->user_id ? substr($audit->user_id, 0, 1) : 'S' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $audit->user_id ?: 'System' }}</div>
                                    <div class="text-xs text-gray-500">{{ $audit->ip_address }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $audit->event === 'created' ? 'bg-green-100 text-green-800' : ($audit->event === 'updated' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                <i class="fas {{ $audit->event === 'created' ? 'fa-plus' : ($audit->event === 'updated' ? 'fa-edit' : 'fa-trash') }} mr-1"></i>
                                {{ ucfirst($audit->event) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ class_basename($audit->auditable_type) }}</div>
                            @if($audit->auditable_id)
                                <div class="text-xs text-gray-500">ID: {{ $audit->auditable_id }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate">{{ $audit->tags }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $audit->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $audit->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('audit-trails.show', $audit) }}"
                               class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-history text-gray-300 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada audit trail</h3>
                                <p class="text-gray-500">Belum ada aktivitas yang tercatat dalam sistem.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($auditTrails->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $auditTrails->appends(request()->query())->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection
