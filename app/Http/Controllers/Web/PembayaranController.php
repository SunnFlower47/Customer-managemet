<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Models\Penagih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PembayaranExport;
use Maatwebsite\Excel\Facades\Excel;

class PembayaranController extends BaseController
{
    /**
     * Generate unique payment code
     */
    private function generateKodePembayaran()
    {
        // Get prefix from company profile
        $companyProfile = \App\Models\CompanyProfile::first();
        $prefix = $companyProfile->payment_code_prefix ?? 'PAY';

        do {
            // Shorter format: PAY + YYMMDD + 3 digits
            $kode = $prefix . date('ymd') . rand(100, 999);
        } while (Pembayaran::where('kode_pembayaran', $kode)->exists());

        return $kode;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['pelanggan', 'penagih']);

        // Filter by penagih if user is penagih
        if (Auth::user()->role === 'penagih') {
            $penagihId = Penagih::where('user_id', Auth::id())->value('id');
            if ($penagihId) {
                $query->where('penagih_id', $penagihId);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by month/year
        if ($request->filled('bulan')) {
            $query->where('bulan_tagihan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun_tagihan', $request->tahun);
        }

        // Filter by penagih (for all roles)
        if ($request->filled('penagih_id')) {
            $query->where('penagih_id', $request->penagih_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_pembayaran', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function($subQ) use ($search) {
                      $subQ->where('nama', 'like', "%{$search}%")
                           ->orWhere('pppoe', 'like', "%{$search}%")
                           ->orWhere('no_hp', 'like', "%{$search}%")
                           ->orWhere('alamat', 'like', "%{$search}%");
                  });
            });
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->paginate(10);
        $penagihs = Penagih::where('aktif', true)->get();

        return view('pembayarans.index', compact('pembayarans', 'penagihs'));
    }

    /**
     * Provide realtime payment search suggestions.
     */
    public function suggest(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
        ]);

        $search = trim($request->input('q', ''));

        $query = Pembayaran::query()
            ->select('id', 'kode_pembayaran', 'pelanggan_id', 'jumlah', 'status', 'bulan_tagihan', 'tahun_tagihan', 'penagih_id', 'created_at')
            ->with(['pelanggan:id,nama,pppoe', 'penagih:id,nama'])
            ->latest()
            ->limit(8);

        if (Auth::user()->role === 'penagih') {
            $penagihId = Penagih::where('user_id', Auth::id())->value('id');
            if ($penagihId) {
                $query->where('penagih_id', $penagihId);
            }
        }

        if (strlen($search) >= 2) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_pembayaran', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', function ($subQ) use ($search) {
                        $subQ->where('nama', 'like', "%{$search}%")
                            ->orWhere('pppoe', 'like', "%{$search}%")
                            ->orWhere('no_hp', 'like', "%{$search}%");
                    })
                    ->orWhereHas('penagih', function ($subQ) use ($search) {
                        $subQ->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $results = $query->get()->map(function ($pembayaran) {
            return [
                'id' => $pembayaran->id,
                'kode' => $pembayaran->kode_pembayaran,
                'pelanggan' => $pembayaran->pelanggan?->nama,
                'pppoe' => $pembayaran->pelanggan?->pppoe,
                'jumlah' => number_format((float)$pembayaran->jumlah, 0, ',', '.'),
                'status' => $pembayaran->status,
                'periode' => \Carbon\Carbon::create(null, $pembayaran->bulan_tagihan, 1)->format('M') . ' ' . $pembayaran->tahun_tagihan,
                'detail_url' => route('pembayarans.show', $pembayaran),
            ];
        });

        return response()->json(['data' => $results]);
    }

    // Create and Store methods removed - payments are generated automatically

    /**
     * Display the specified resource.
     */
    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['pelanggan', 'penagih']);

        // Load paginated payment history for the customer
        $paymentHistory = $pembayaran->pelanggan->pembayarans()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pembayarans.show', compact('pembayaran', 'paymentHistory'));
    }

    /**
     * Export pembayarans to PDF
     */
    public function export(Request $request)
    {
        $query = Pembayaran::with(['pelanggan:id,nama,pppoe', 'penagih:id,nama']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by month/year
        if ($request->filled('bulan')) {
            $query->where('bulan_tagihan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun_tagihan', $request->tahun);
        }

        // Filter by penagih (for all roles)
        if ($request->filled('penagih_id')) {
            $query->where('penagih_id', $request->penagih_id);
        } elseif (Auth::user()->role === 'penagih') {
            // Auto-filter for penagih role
            $query->where('penagih_id', Auth::user()->penagih->id);
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->get();

        // Get filter info for PDF
        $filters = [
            'status' => $request->status,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'penagih_id' => $request->penagih_id,
        ];

        $pdf = Pdf::loadView('pembayarans.pdf', compact('pembayarans', 'filters'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'pembayarans_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembayaran $pembayaran)
    {
        // Include pelanggans with status 'aktif' or 'bayar double' (both are active)
        $pelanggans = Pelanggan::whereIn('status', ['aktif', 'bayar double'])->get();
        $penagihs = Penagih::where('aktif', true)->get();
        return view('pembayarans.edit', compact('pembayaran', 'pelanggans', 'penagihs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembayaran $pembayaran)
    {
        // IMMUTABLE PRINCIPLE: Only allow editing of non-critical fields
        $request->validate([
            'jumlah' => 'required|numeric|min:0',
            'status' => 'required|in:belum_bayar,lunas',
            'tanggal_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500'
        ]);

        // CRITICAL: Do not allow changes to immutable fields
        // bulan_tagihan, tahun_tagihan, pelanggan_id, penagih_id are IMMUTABLE
        // SEMENTARA: jumlah dapat diubah untuk memperbaiki kesalahan input paket
        $updateData = $request->only(['jumlah', 'status', 'keterangan']);

        // Handle tanggal_bayar based on status
        if ($request->status === 'lunas' && $request->tanggal_bayar) {
            $updateData['tanggal_bayar'] = $request->tanggal_bayar;
        } elseif ($request->status === 'belum_bayar') {
            $updateData['tanggal_bayar'] = null;
        }

        $pembayaran->update($updateData);

        // Preserve pagination and filters when redirecting
        $redirectParams = $request->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun']);
        return redirect()->route('pembayarans.index', $redirectParams)->with('success', 'Pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Pembayaran $pembayaran)
    {
        // IMMUTABLE PRINCIPLE: Do not allow deletion of payment records
        // Historical data must be preserved for audit and integrity
        return redirect()->route('pembayarans.index')
            ->with('error', 'Pembayaran tidak dapat dihapus untuk menjaga integritas data historis.');
    }

    /**
     * Mark payment as paid
     */
    public function markPaid(Request $request, Pembayaran $pembayaran)
    {
        $pembayaran->update([
            'status' => 'lunas',
            'tanggal_bayar' => now()
        ]);

        // Preserve pagination and filters when redirecting
        $redirectParams = $request->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun']);
        return redirect()->route('pembayarans.index', $redirectParams)
            ->with('success', 'Pembayaran berhasil ditandai sebagai lunas.')
            ->with('show_invoice_option', true)
            ->with('pembayaran_id', $pembayaran->id);
    }

    /**
     * Bulk update status pembayarans
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|json',
            'status' => 'required|in:belum_bayar,lunas'
        ]);

        $ids = json_decode($request->ids, true);

        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $updateData = ['status' => $request->status];

        // If changing to lunas, set tanggal_bayar
        if ($request->status === 'lunas') {
            $updateData['tanggal_bayar'] = now();
        } else {
            $updateData['tanggal_bayar'] = null;
        }

        $count = Pembayaran::whereIn('id', $ids)->update($updateData);

        $redirectParams = $request->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun']);
        return redirect()->route('pembayarans.index', $redirectParams)
            ->with('success', "Berhasil mengubah status {$count} pembayaran menjadi " . ($request->status === 'lunas' ? 'Lunas' : 'Belum Bayar') . ".");
    }

    /**
     * Bulk mark payments as paid
     */
    public function bulkMarkPaid(Request $request)
    {
        $request->validate([
            'ids' => 'required|json'
        ]);

        $ids = json_decode($request->ids, true);

        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $count = Pembayaran::whereIn('id', $ids)->update([
            'status' => 'lunas',
            'tanggal_bayar' => now()
        ]);

        $redirectParams = $request->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun']);
        return redirect()->route('pembayarans.index', $redirectParams)
            ->with('success', "Berhasil menandai {$count} pembayaran sebagai lunas.");
    }

    /**
     * Update payment status
     */
    public function updateStatus(Request $request, Pembayaran $pembayaran)
    {
        try {
            // IMMUTABLE PRINCIPLE: Only allow status changes and related fields
            $request->validate([
                'status' => 'required|in:belum_bayar,lunas',
                'keterangan' => 'nullable|string|max:500',
                'cetak_faktur' => 'nullable|boolean'
            ]);

            $oldStatus = $pembayaran->status;
            $newStatus = $request->status;
            $keterangan = $request->keterangan;
            $cetakFaktur = $request->boolean('cetak_faktur', false);

            $updateData = ['status' => $newStatus];

            // If changing to lunas, set tanggal_bayar
            if ($newStatus === 'lunas' && $oldStatus !== 'lunas') {
                $updateData['tanggal_bayar'] = now();

                // Add keterangan if provided
                if ($keterangan) {
                    $updateData['keterangan'] = $keterangan;
                }
            }
            // If changing from lunas to belum_bayar, clear tanggal_bayar
            elseif ($newStatus === 'belum_bayar' && $oldStatus === 'lunas') {
                $updateData['tanggal_bayar'] = null;
                $updateData['keterangan'] = null; // Clear keterangan when reverting
            }

            $pembayaran->update($updateData);

            $response = [
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui.',
                'new_status' => $newStatus,
                'cetak_faktur' => $cetakFaktur,
                'show_invoice_option' => ($newStatus === 'lunas' && $oldStatus !== 'lunas')
            ];

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
            }

            // Preserve pagination and filters when redirecting
            $redirectParams = $request->only(['page', 'search', 'status', 'penagih_id', 'bulan', 'tahun']);
            return redirect()->route('pembayarans.index', $redirectParams)
                ->with('success', $response['message'])
                ->with('show_invoice_option', $response['show_invoice_option'])
                ->with('pembayaran_id', $pembayaran->id);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Update status error: ' . $e->getMessage());

            $errorResponse = [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json($errorResponse, 500, [], JSON_UNESCAPED_UNICODE);
            }

            return redirect()->back()->with('error', $errorResponse['message']);
        }
    }

    /**
     * Generate bills for current month
     */
    public function generateBills(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Check if bills already exist
        $existingBills = Pembayaran::where('bulan_tagihan', $currentMonth)
            ->where('tahun_tagihan', $currentYear)
            ->count();

        if ($existingBills > 0) {
            return redirect()->back()
                ->with('error', "Tagihan untuk bulan {$currentMonth}/{$currentYear} sudah ada.");
        }

        // Include customers with status 'aktif' or 'bayar double' (both can receive bills)
        $activeCustomers = Pelanggan::whereIn('status', ['aktif', 'bayar double'])
            ->with(['paket', 'penagih'])
            ->get();

        $generatedCount = 0;

        foreach ($activeCustomers as $customer) {
            Pembayaran::create([
                'kode_pembayaran' => $this->generateKodePembayaran(),
                'pelanggan_id' => $customer->id,
                'bulan_tagihan' => $currentMonth,
                'tahun_tagihan' => $currentYear,
                'jumlah' => $customer->paket->harga,
                'status' => 'belum_bayar',
                'penagih_id' => $customer->penagih_id,
                'keterangan' => "Tagihan bulan {$currentMonth}/{$currentYear}",
            ]);

            $generatedCount++;
        }

        return redirect()->back()
            ->with('success', "Berhasil generate {$generatedCount} tagihan untuk bulan {$currentMonth}/{$currentYear}.");
    }

    /**
     * Export pembayarans to CSV
     */
    public function exportCsv(Request $request)
    {
        $query = Pembayaran::with(['pelanggan', 'penagih']);

        // Apply same filters as index
        if (Auth::user()->role === 'penagih') {
            $penagihId = Penagih::where('user_id', Auth::id())->value('id');
            if ($penagihId) {
                $query->where('penagih_id', $penagihId);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('bulan')) {
            $query->where('bulan_tagihan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun_tagihan', $request->tahun);
        }

        $pembayarans = $query->get();

        $filename = 'pembayarans_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($pembayarans) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'Nama Pelanggan',
                'PPPoE',
                'Penagih',
                'Bulan Tagihan',
                'Tahun Tagihan',
                'Jumlah',
                'Status',
                'Tanggal Bayar',
                'Keterangan',
                'Dibuat'
            ]);

            foreach ($pembayarans as $pembayaran) {
                fputcsv($file, [
                    $pembayaran->id,
                    $pembayaran->pelanggan->nama,
                    $pembayaran->pelanggan->pppoe,
                    $pembayaran->penagih->nama,
                    $pembayaran->bulan_tagihan,
                    $pembayaran->tahun_tagihan,
                    $pembayaran->jumlah,
                    $pembayaran->status,
                    $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('Y-m-d H:i:s') : '',
                    $pembayaran->keterangan,
                    $pembayaran->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export pembayarans to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Pembayaran::with(['pelanggan', 'penagih']);

        // Apply same filters as index
        if (Auth::user()->role === 'penagih') {
            $penagihId = Penagih::where('user_id', Auth::id())->value('id');
            if ($penagihId) {
                $query->where('penagih_id', $penagihId);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('bulan')) {
            $query->where('bulan_tagihan', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun_tagihan', $request->tahun);
        }

        // Filter by penagih (for all roles)
        if ($request->filled('penagih_id')) {
            $query->where('penagih_id', $request->penagih_id);
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->get();

        $filename = 'pembayarans_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PembayaranExport($pembayarans), $filename);
    }

    /**
     * Print invoice for payment
     */
    public function printInvoice(Pembayaran $pembayaran)
    {
        $pembayaran->load(['pelanggan', 'penagih']);

        $pdf = Pdf::loadView('pembayarans.invoice', compact('pembayaran'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Invoice_' . $pembayaran->kode_pembayaran . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Show invoice for a specific payment
     */
    public function invoice(Pembayaran $pembayaran)
    {
        $pembayaran->load(['pelanggan', 'penagih']);

        return view('pembayarans.invoice', compact('pembayaran'));
    }

    /**
     * Generate PDF for a specific payment
     */
    public function pdf(Pembayaran $pembayaran)
    {
        $pembayaran->load(['pelanggan', 'penagih']);
        $isPdf = true; // Flag to hide buttons in PDF

        $pdf = Pdf::loadView('pembayarans.invoice', compact('pembayaran', 'isPdf'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'isPhpEnabled' => true,
            'isJavascriptEnabled' => false,
            'isFontSubsettingEnabled' => true,
            'defaultMediaType' => 'print',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10
        ]);

        $filename = 'Invoice_' . $pembayaran->kode_pembayaran . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Generate PDF for invoice (same as pdf but with different route)
     */
    public function invoicePdf(Pembayaran $pembayaran)
    {
        $pembayaran->load(['pelanggan', 'penagih']);
        $isPdf = true; // Flag to hide buttons in PDF

        $pdf = Pdf::loadView('pembayarans.invoice', compact('pembayaran', 'isPdf'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial',
            'isPhpEnabled' => true,
            'isJavascriptEnabled' => false,
            'isFontSubsettingEnabled' => true,
            'defaultMediaType' => 'print',
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10
        ]);

        $filename = 'Invoice_' . $pembayaran->kode_pembayaran . '.pdf';
        return $pdf->download($filename);
    }
}
