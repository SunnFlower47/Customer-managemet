@extends('layouts.app')

@section('title', 'Admin - Tickets')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl relative">
                <i class="fas fa-ticket-alt"></i>
                <div class="absolute -top-1 -right-1 h-5 w-5 bg-amber-500 rounded-full border-2 border-white flex items-center justify-center">
                    <i class="fas fa-circle text-[6px] text-white"></i>
                </div>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">Ticket Management</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">Kelola dan monitor semua ticket pelanggan</p>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('admin.tickets.statistics') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-chart-bar mr-2 text-xs sm:text-sm"></i>Statistics
            </a>
        </div>
    </div>

    <div class="app-card space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-base font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter mr-2 text-amber-500"></i>Filter & Pencarian
            </h3>
        </div>
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div>
                <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                <select name="kategori" id="kategori" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">All Categories</option>
                    <option value="technical" {{ request('kategori') == 'technical' ? 'selected' : '' }}>Technical</option>
                    <option value="billing" {{ request('kategori') == 'billing' ? 'selected' : '' }}>Billing</option>
                    <option value="general" {{ request('kategori') == 'general' ? 'selected' : '' }}>General</option>
                    <option value="complaint" {{ request('kategori') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                </select>
            </div>

            <div>
                <label for="prioritas" class="block text-sm font-semibold text-gray-700 mb-2">Prioritas</label>
                <select name="prioritas" id="prioritas" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('prioritas') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('prioritas') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('prioritas') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('prioritas') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>

            <div>
                <label for="assigned_to" class="block text-sm font-semibold text-gray-700 mb-2">Assigned To</label>
                <select name="assigned_to" id="assigned_to" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-amber-600 to-amber-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <div class="app-card app-card--soft space-y-5">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-amber-500 font-semibold">Daftar Tickets</p>
                <h2 class="text-base font-semibold text-gray-900">Manajemen tiket pelanggan</h2>
            </div>
        </div>

        <div class="hidden lg:block overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-amber-500 to-amber-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-ticket-alt mr-2"></i>Ticket
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-user mr-2"></i>Pelanggan
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-tag mr-2"></i>Kategori
                        </th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold text-white uppercase tracking-wider">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Prioritas
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
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-amber-50 transition cursor-pointer" onclick="window.location.href='{{ route('admin.tickets.show', $ticket) }}'">
                        <td class="px-5 py-4 whitespace-nowrap">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $ticket->kode_ticket }}</p>
                                <p class="text-xs text-gray-500 truncate max-w-xs">{{ $ticket->judul }}</p>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-xs text-gray-700 truncate max-w-xs">{{ $ticket->pelanggan->nama }}</p>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                {{ ucfirst($ticket->kategori) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                @if($ticket->prioritas == 'low') bg-green-100 text-green-800 border border-green-200
                                @elseif($ticket->prioritas == 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @elseif($ticket->prioritas == 'high') bg-orange-100 text-orange-800 border border-orange-200
                                @else bg-red-100 text-red-800 border border-red-200
                                @endif">
                                {{ ucfirst($ticket->prioritas) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-xs text-gray-700">{{ $ticket->created_at->format('d M Y') }}</p>
                            @if($ticket->assignedTo)
                                <p class="text-[10px] text-gray-500">Assigned: {{ $ticket->assignedTo->name }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                @if($ticket->status == 'open') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @elseif($ticket->status == 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                                @elseif($ticket->status == 'resolved') bg-green-100 text-green-800 border border-green-200
                                @else bg-gray-100 text-gray-800 border border-gray-200
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-ticket-alt text-gray-400 text-4xl mb-2"></i>
                                <p class="text-gray-500">Tidak ada ticket</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($tickets->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $tickets->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        <div class="lg:hidden space-y-2">
            @forelse($tickets as $ticket)
            <a href="{{ route('admin.tickets.show', $ticket) }}" class="mobile-card border border-gray-200 rounded-2xl px-4 py-3 block">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-ticket-alt text-amber-600 text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $ticket->kode_ticket }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $ticket->judul }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-[11px] font-semibold flex-shrink-0
                                @if($ticket->status == 'open') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @elseif($ticket->status == 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                                @elseif($ticket->status == 'resolved') bg-green-100 text-green-800 border border-green-200
                                @else bg-gray-100 text-gray-800 border border-gray-200
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                <i class="fas fa-user mr-1 text-[9px]"></i>{{ $ticket->pelanggan->nama }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                <i class="fas fa-tag mr-1 text-[9px]"></i>{{ ucfirst($ticket->kategori) }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold
                                @if($ticket->prioritas == 'low') bg-green-100 text-green-800
                                @elseif($ticket->prioritas == 'medium') bg-yellow-100 text-yellow-800
                                @elseif($ticket->prioritas == 'high') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800
                                @endif">
                                <i class="fas fa-exclamation-triangle mr-1 text-[9px]"></i>{{ ucfirst($ticket->prioritas) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-calendar mr-1"></i>{{ $ticket->created_at->format('d M Y') }}
                            @if($ticket->assignedTo)
                                • Assigned: {{ $ticket->assignedTo->name }}
                            @endif
                        </p>
                    </div>
                </div>
            </a>
            @empty
            <div class="app-card text-center py-12">
                <div class="flex flex-col items-center">
                    <i class="fas fa-ticket-alt text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-base font-semibold text-gray-900 mb-2">Tidak ada ticket</h3>
                    <p class="text-sm text-gray-500">Belum ada ticket yang terdaftar.</p>
                </div>
            </div>
            @endforelse

            @if($tickets->hasPages())
                <div class="mt-4">
                    {{ $tickets->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
