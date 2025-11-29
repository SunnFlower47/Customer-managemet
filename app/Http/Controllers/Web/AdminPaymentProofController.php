<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminPaymentProofController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of payment proofs
     */
    public function index(Request $request)
    {
        $this->authorize('view-payment-proof');
        $query = PaymentProof::with(['pembayaran.pelanggan', 'pelanggan', 'verifiedBy']);

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('submission_method') && $request->submission_method) {
            $query->where('submission_method', $request->submission_method);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pembayaran', function ($q) use ($search) {
                      $q->where('kode_pembayaran', 'like', "%{$search}%");
                  });
            });
        }

        $paymentProofs = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.payment-proofs.index', compact('paymentProofs'));
    }

    /**
     * Display the specified payment proof
     */
    public function show($id)
    {
        $this->authorize('view-payment-proof');
        $paymentProof = PaymentProof::with([
            'pembayaran.pelanggan',
            'pelanggan',
            'verifiedBy'
        ])->findOrFail($id);

        return view('admin.payment-proofs.show', compact('paymentProof'));
    }

    /**
     * Verify payment proof
     */
    public function verify(Request $request, $id)
    {
        $this->authorize('verify-payment-proof');
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $paymentProof = PaymentProof::findOrFail($id);
        $pembayaran = $paymentProof->pembayaran;

        // Update payment proof status
        $paymentProof->update([
            'status' => 'verified',
            'admin_notes' => $request->admin_notes,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        // Update payment status to paid
        $pembayaran->update([
            'status' => 'lunas',
            'tanggal_bayar' => now(),
            'keterangan' => 'Pembayaran diverifikasi melalui bukti transfer',
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diverifikasi');
    }

    /**
     * Reject payment proof
     */
    public function reject(Request $request, $id)
    {
        $this->authorize('verify-payment-proof');
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $paymentProof = PaymentProof::findOrFail($id);

        // Update payment proof status
        $paymentProof->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran ditolak');
    }

    /**
     * Download payment proof file
     */
    public function download($id)
    {
        $this->authorize('download-payment-proof');
        $paymentProof = PaymentProof::findOrFail($id);

        if (!Storage::disk('public')->exists($paymentProof->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = Storage::disk('public')->path($paymentProof->file_path);

        return response()->download($filePath, $paymentProof->file_name);
    }

    /**
     * Get payment proof statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => PaymentProof::count(),
            'pending' => PaymentProof::where('status', 'pending')->count(),
            'verified' => PaymentProof::where('status', 'verified')->count(),
            'rejected' => PaymentProof::where('status', 'rejected')->count(),
            'by_method' => PaymentProof::selectRaw('submission_method, count(*) as count')
                ->groupBy('submission_method')
                ->pluck('count', 'submission_method'),
            'today' => PaymentProof::whereDate('created_at', today())->count(),
            'this_week' => PaymentProof::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => PaymentProof::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.payment-proofs.statistics', compact('stats'));
    }
}
