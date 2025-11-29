@extends('layouts.app')

@section('title', 'Admin - Ticket Detail')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="page-header">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg text-white text-xl sm:text-2xl">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <div>
                <h1 class="page-header__title text-slate-900">{{ $ticket->judul }}</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">
                    Created by {{ $ticket->pelanggan->nama }} on {{ $ticket->created_at->format('d M Y H:i') }}
                </p>
            </div>
        </div>
        <div class="page-header__actions flex flex-col sm:flex-row gap-2 sm:gap-3">
            <span class="inline-flex items-center justify-center px-4 py-2 text-xs sm:text-sm font-semibold rounded-xl
                @if($ticket->status == 'open') bg-yellow-100 text-yellow-800 border border-yellow-200
                @elseif($ticket->status == 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                @elseif($ticket->status == 'resolved') bg-green-100 text-green-800 border border-green-200
                @else bg-gray-100 text-gray-800 border border-gray-200
                @endif">
                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
            </span>
            <a href="{{ route('admin.tickets.index') }}"
               class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2 text-xs sm:text-sm"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-amber-500 font-semibold">Detail Ticket</p>
                    <h2 class="text-base font-semibold text-gray-900">Informasi lengkap</h2>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Description</label>
                        <p class="text-sm text-gray-900 whitespace-pre-wrap bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">{{ $ticket->deskripsi }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Category</label>
                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst($ticket->kategori) }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Priority</label>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                @if($ticket->prioritas == 'low') bg-green-100 text-green-800 border border-green-200
                                @elseif($ticket->prioritas == 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                                @elseif($ticket->prioritas == 'high') bg-orange-100 text-orange-800 border border-orange-200
                                @else bg-red-100 text-red-800 border border-red-200
                                @endif">
                                {{ ucfirst($ticket->prioritas) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-amber-500 font-semibold">Comments</p>
                    <h2 class="text-base font-semibold text-gray-900">Diskusi tiket</h2>
                </div>
                <div class="space-y-3">
                    @forelse($ticket->comments as $comment)
                        <div class="border-l-4 border-amber-200 bg-gray-50 rounded-r-xl px-4 py-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-900">
                                        @if($comment->user)
                                            {{ $comment->user->name }}
                                        @else
                                            {{ $comment->pelanggan->nama }}
                                        @endif
                                    </p>
                                    @if($comment->is_internal)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                            Internal
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500">{{ $comment->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->comment }}</p>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <i class="fas fa-comments text-gray-300 text-3xl mb-2"></i>
                            <p class="text-sm text-gray-500">Belum ada komentar</p>
                        </div>
                    @endforelse
                </div>

                @can('edit-ticket')
                <form method="POST" action="{{ route('admin.tickets.add-comment', $ticket) }}" class="mt-6 pt-6 border-t border-gray-200">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="comment" class="block text-sm font-semibold text-gray-700 mb-2">Add Comment</label>
                            <textarea name="comment" id="comment" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" placeholder="Add a comment..."></textarea>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_internal" id="is_internal" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                            <label for="is_internal" class="ml-2 block text-sm text-gray-700">
                                Internal comment (not visible to customer)
                            </label>
                        </div>
                        <div>
                            <button type="submit" class="bg-gradient-to-r from-amber-600 to-amber-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                                <i class="fas fa-comment mr-2"></i>Add Comment
                            </button>
                        </div>
                    </div>
                </form>
                @endcan
            </div>
        </div>

        <div class="space-y-6">
            @can('edit-ticket')
            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-amber-500 font-semibold">Actions</p>
                    <h2 class="text-base font-semibold text-gray-900">Kelola tiket</h2>
                </div>

                <form method="POST" action="{{ route('admin.tickets.update-status', $ticket) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                            <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div>
                        <label for="status_notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                        <textarea name="status_notes" id="status_notes" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white" placeholder="Status update notes..."></textarea>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-gradient-to-r from-amber-600 to-amber-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                            <i class="fas fa-save mr-2"></i>Update Status
                        </button>
                    </div>
                </form>

                @can('assign-ticket')
                <form method="POST" action="{{ route('admin.tickets.assign', $ticket) }}" class="space-y-4 pt-4 border-t border-gray-200">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="assigned_to" class="block text-sm font-semibold text-gray-700 mb-2">Assign To</label>
                        <select name="assigned_to" id="assigned_to" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-200 text-sm font-medium bg-gray-50 focus:bg-white">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $ticket->assigned_to == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white px-5 py-3 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                            <i class="fas fa-user-plus mr-2"></i>Assign Ticket
                        </button>
                    </div>
                </form>
                @endcan
            </div>
            @endcan

            <div class="app-card space-y-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-amber-500 font-semibold">Ticket Information</p>
                    <h2 class="text-base font-semibold text-gray-900">Informasi sistem</h2>
                </div>
                <dl class="space-y-3 text-sm">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Ticket ID</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $ticket->kode_ticket }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Customer</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $ticket->pelanggan->nama }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Phone</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $ticket->pelanggan->no_hp }}</dd>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Created</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $ticket->created_at->format('d M Y H:i') }}</dd>
                    </div>
                    @if($ticket->assignedTo)
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Assigned To</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $ticket->assignedTo->name }}</dd>
                    </div>
                    @endif
                    @if($ticket->resolved_at)
                    <div class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Resolved</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $ticket->resolved_at->format('d M Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
