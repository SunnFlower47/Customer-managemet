<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use App\Models\Ticket;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user && ($user->can('view-ticket') || $user->can('view-payment-proof')),
            403
        );

        $ticketQuery = Ticket::with('pelanggan')
            ->where('status', 'open')
            ->orderByDesc('created_at');

        $paymentProofQuery = PaymentProof::with(['pelanggan', 'pembayaran'])
            ->where('status', 'pending')
            ->orderByDesc('created_at');

        $emptyTicketPaginator = new LengthAwarePaginator([], 0, 10, 1, [
            'path' => $request->url(),
            'pageName' => 'ticket_page',
        ]);

        $emptyProofPaginator = new LengthAwarePaginator([], 0, 10, 1, [
            'path' => $request->url(),
            'pageName' => 'proof_page',
        ]);

        return view('notifications.index', [
            'ticketCount' => $user->can('view-ticket') ? $ticketQuery->count() : 0,
            'paymentProofCount' => $user->can('view-payment-proof') ? $paymentProofQuery->count() : 0,
            'ticketPaginator' => $user->can('view-ticket')
                ? $ticketQuery->paginate(10, ['*'], 'ticket_page')
                : $emptyTicketPaginator,
            'paymentProofPaginator' => $user->can('view-payment-proof')
                ? $paymentProofQuery->paginate(10, ['*'], 'proof_page')
                : $emptyProofPaginator,
        ]);
    }

    /**
     * API feed for notifications dropdown.
     */
    public function feed(Request $request)
    {
        $user = $request->user();

        $ticketData = [
            'count' => 0,
            'items' => [],
        ];

        $paymentProofData = [
            'count' => 0,
            'items' => [],
        ];

        if ($user && $user->can('view-ticket')) {
            $ticketData['count'] = Ticket::where('status', 'open')->count();
            $ticketData['items'] = Ticket::with('pelanggan')
                ->where('status', 'open')
                ->orderByDesc('created_at')
                ->take(5)
                ->get()
                ->map(function (Ticket $ticket) {
                    return [
                        'id' => $ticket->id,
                        'kode' => $ticket->kode_ticket,
                        'pelanggan' => $ticket->pelanggan->nama ?? '-',
                        'kategori' => ucfirst($ticket->kategori ?? '-'),
                        'prioritas' => ucfirst($ticket->prioritas ?? '-'),
                        'created_at' => $ticket->created_at?->diffForHumans() ?? '-',
                        'url' => route('admin.tickets.show', $ticket),
                    ];
                });
        }

        if ($user && $user->can('view-payment-proof')) {
            $paymentProofData['count'] = PaymentProof::where('status', 'pending')->count();
            $paymentProofData['items'] = PaymentProof::with(['pelanggan', 'pembayaran'])
                ->where('status', 'pending')
                ->orderByDesc('created_at')
                ->take(5)
                ->get()
                ->map(function (PaymentProof $paymentProof) {
                    return [
                        'id' => $paymentProof->id,
                        'pelanggan' => $paymentProof->pelanggan->nama ?? '-',
                        'kode_pembayaran' => $paymentProof->pembayaran->kode_pembayaran ?? '-',
                        'metode' => ucfirst(str_replace('_', ' ', $paymentProof->submission_method ?? '-')),
                        'jumlah' => $paymentProof->pembayaran->harga_paket ?? 0,
                        'created_at' => $paymentProof->created_at?->diffForHumans() ?? '-',
                        'url' => route('admin.payment-proofs.show', $paymentProof),
                    ];
                });
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tickets' => $ticketData,
                'payment_proofs' => $paymentProofData,
                'total' => $ticketData['count'] + $paymentProofData['count'],
            ],
        ]);
    }
}

