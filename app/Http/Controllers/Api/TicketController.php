<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Create new ticket
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|in:technical,billing,service,other',
            'prioritas' => 'required|string|in:low,medium,high',
            'deskripsi' => 'required|string|max:2000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $customer = $request->user();

            // Create ticket
            $ticket = Ticket::create([
                'kode_ticket' => Ticket::generateTicketCode(),
                'pelanggan_id' => $customer->id,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori,
                'prioritas' => $request->prioritas,
                'status' => 'open',
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = 'ticket_' . $ticket->kode_ticket . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('ticket_attachments', $filename, 'public');

                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'filename' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => null, // Customer upload
                    ]);
                }
            }

            // Add initial comment from customer
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'pelanggan_id' => $customer->id,
                'comment' => $request->deskripsi,
                'is_internal' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil dibuat',
                'data' => [
                    'ticket' => [
                        'id' => $ticket->id,
                        'kode_ticket' => $ticket->kode_ticket,
                        'judul' => $ticket->judul,
                        'kategori' => $ticket->kategori,
                        'kategori_label' => $ticket->category_label,
                        'prioritas' => $ticket->prioritas,
                        'prioritas_label' => $ticket->priority_label,
                        'status' => $ticket->status,
                        'status_label' => $ticket->status_label,
                        'created_at' => $ticket->created_at->format('d/m/Y H:i'),
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get customer's tickets
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $customer = $request->user();

            $query = Ticket::where('pelanggan_id', $customer->id)
                ->with(['assignedTo', 'comments' => function($q) {
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

            // Pagination
            $perPage = $request->get('per_page', 10);
            $tickets = $query->orderBy('created_at', 'desc')->paginate($perPage);

            $transformedTickets = $tickets->getCollection()->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'kode_ticket' => $ticket->kode_ticket,
                    'judul' => $ticket->judul,
                    'kategori' => $ticket->kategori,
                    'kategori_label' => $ticket->category_label,
                    'prioritas' => $ticket->prioritas,
                    'prioritas_label' => $ticket->priority_label,
                    'status' => $ticket->status,
                    'status_label' => $ticket->status_label,
                    'assigned_to' => $ticket->assignedTo ? [
                        'id' => $ticket->assignedTo->id,
                        'name' => $ticket->assignedTo->name,
                    ] : null,
                    'rating' => $ticket->rating,
                    'created_at' => $ticket->created_at->format('d/m/Y H:i'),
                    'resolved_at' => $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : null,
                    'last_comment' => $ticket->comments->first() ? [
                        'comment' => Str::limit($ticket->comments->first()->comment, 100),
                        'created_at' => $ticket->comments->first()->created_at->format('d/m/Y H:i'),
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data tiket berhasil diambil',
                'data' => $transformedTickets,
                'pagination' => [
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage(),
                    'per_page' => $tickets->perPage(),
                    'total' => $tickets->total(),
                    'from' => $tickets->firstItem(),
                    'to' => $tickets->lastItem()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get ticket detail
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $customer = $request->user();
            $ticket = Ticket::where('pelanggan_id', $customer->id)
                ->with(['assignedTo', 'comments' => function($q) {
                    $q->where('is_internal', false)->with(['user', 'pelanggan'])->orderBy('created_at', 'asc');
                }, 'attachments'])
                ->findOrFail($id);

            $transformedComments = $ticket->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'author_name' => $comment->author_name,
                    'author_type' => $comment->author_type,
                    'created_at' => $comment->created_at->format('d/m/Y H:i'),
                ];
            });

            $transformedAttachments = $ticket->attachments->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'filename' => $attachment->filename,
                    'file_type' => $attachment->file_type,
                    'file_size' => $attachment->formatted_file_size,
                    'file_url' => $attachment->file_url,
                    'is_image' => $attachment->is_image,
                    'is_pdf' => $attachment->is_pdf,
                    'created_at' => $attachment->created_at->format('d/m/Y H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail tiket berhasil diambil',
                'data' => [
                    'ticket' => [
                        'id' => $ticket->id,
                        'kode_ticket' => $ticket->kode_ticket,
                        'judul' => $ticket->judul,
                        'deskripsi' => $ticket->deskripsi,
                        'kategori' => $ticket->kategori,
                        'kategori_label' => $ticket->category_label,
                        'prioritas' => $ticket->prioritas,
                        'prioritas_label' => $ticket->priority_label,
                        'status' => $ticket->status,
                        'status_label' => $ticket->status_label,
                        'assigned_to' => $ticket->assignedTo ? [
                            'id' => $ticket->assignedTo->id,
                            'name' => $ticket->assignedTo->name,
                        ] : null,
                        'rating' => $ticket->rating,
                        'customer_feedback' => $ticket->customer_feedback,
                        'created_at' => $ticket->created_at->format('d/m/Y H:i'),
                        'resolved_at' => $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : null,
                    ],
                    'comments' => $transformedComments,
                    'attachments' => $transformedAttachments,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 404);
        }
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $customer = $request->user();
            $ticket = Ticket::where('pelanggan_id', $customer->id)->findOrFail($id);

            // Check if ticket is closed
            if ($ticket->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menambah komentar pada tiket yang sudah ditutup'
                ], 400);
            }

            $comment = TicketComment::create([
                'ticket_id' => $ticket->id,
                'pelanggan_id' => $customer->id,
                'comment' => $request->comment,
                'is_internal' => false,
            ]);

            // Update ticket status to open if it was resolved
            if ($ticket->status === 'resolved') {
                $ticket->update(['status' => 'open']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil ditambahkan',
                'data' => [
                    'comment' => [
                        'id' => $comment->id,
                        'comment' => $comment->comment,
                        'author_name' => $comment->author_name,
                        'author_type' => $comment->author_type,
                        'created_at' => $comment->created_at->format('d/m/Y H:i'),
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Upload attachment to ticket
     */
    public function uploadAttachment(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $customer = $request->user();
            $ticket = Ticket::where('pelanggan_id', $customer->id)->findOrFail($id);

            // Check if ticket is closed
            if ($ticket->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menambah attachment pada tiket yang sudah ditutup'
                ], 400);
            }

            $file = $request->file('file');
            $filename = 'ticket_' . $ticket->kode_ticket . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('ticket_attachments', $filename, 'public');

            $attachment = TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'filename' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => null, // Customer upload
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => [
                    'attachment' => [
                        'id' => $attachment->id,
                        'filename' => $attachment->filename,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->formatted_file_size,
                        'file_url' => $attachment->file_url,
                        'is_image' => $attachment->is_image,
                        'is_pdf' => $attachment->is_pdf,
                        'created_at' => $attachment->created_at->format('d/m/Y H:i'),
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Rate ticket resolution
     */
    public function rateResolution(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $customer = $request->user();
            $ticket = Ticket::where('pelanggan_id', $customer->id)->findOrFail($id);

            // Check if ticket is resolved
            if ($ticket->status !== 'resolved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya tiket yang sudah diselesaikan yang dapat diberi rating'
                ], 400);
            }

            // Check if already rated
            if ($ticket->rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket sudah diberi rating'
                ], 400);
            }

            $ticket->update([
                'rating' => $request->rating,
                'customer_feedback' => $request->feedback,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rating berhasil disimpan',
                'data' => [
                    'ticket' => [
                        'id' => $ticket->id,
                        'kode_ticket' => $ticket->kode_ticket,
                        'rating' => $ticket->rating,
                        'customer_feedback' => $ticket->customer_feedback,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
