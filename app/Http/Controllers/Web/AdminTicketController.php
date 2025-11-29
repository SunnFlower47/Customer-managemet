<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminTicketController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of tickets
     */
    public function index(Request $request)
    {
        $this->authorize('view-ticket');
        $query = Ticket::with(['pelanggan', 'assignedTo', 'comments' => function($q) {
            $q->where('is_internal', false)->latest()->limit(1);
        }]);

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->has('prioritas') && $request->prioritas) {
            $query->where('prioritas', $request->prioritas);
        }

        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_ticket', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        $users = User::where('aktif', true)->get();

        return view('admin.tickets.index', compact('tickets', 'users'));
    }

    /**
     * Display the specified ticket
     */
    public function show($id)
    {
        $this->authorize('view-ticket');
        $ticket = Ticket::with([
            'pelanggan',
            'assignedTo',
            'comments' => function($q) {
                $q->with(['user', 'pelanggan'])->orderBy('created_at', 'asc');
            },
            'attachments'
        ])->findOrFail($id);

        $users = User::where('aktif', true)->get();

        return view('admin.tickets.show', compact('ticket', 'users'));
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, $id)
    {
        $this->authorize('edit-ticket');
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'status' => $request->status,
            'resolved_at' => $request->status === 'resolved' ? now() : null,
        ]);

        // Add internal comment
        if ($request->status_notes) {
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'comment' => "Status diubah menjadi: " . $ticket->status_label . "\n\n" . $request->status_notes,
                'is_internal' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Status tiket berhasil diperbarui');
    }

    /**
     * Assign ticket to user
     */
    public function assign(Request $request, $id)
    {
        $this->authorize('assign-ticket');
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket = Ticket::findOrFail($id);
        $assignedUser = User::findOrFail($request->assigned_to);

        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress',
        ]);

        // Add internal comment
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => "Tiket ditugaskan kepada: " . $assignedUser->name,
            'is_internal' => true,
        ]);

        return redirect()->back()->with('success', 'Tiket berhasil ditugaskan');
    }

    /**
     * Add internal comment
     */
    public function addComment(Request $request, $id)
    {
        $this->authorize('edit-ticket');
        $request->validate([
            'comment' => 'required|string|max:2000',
            'is_internal' => 'boolean',
        ]);

        $ticket = Ticket::findOrFail($id);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'is_internal' => $request->boolean('is_internal', true),
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan');
    }

    /**
     * Get ticket statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
            'by_category' => Ticket::selectRaw('kategori, count(*) as count')
                ->groupBy('kategori')
                ->pluck('count', 'kategori'),
            'by_priority' => Ticket::selectRaw('prioritas, count(*) as count')
                ->groupBy('prioritas')
                ->pluck('count', 'prioritas'),
        ];

        return view('admin.tickets.statistics', compact('stats'));
    }
}
