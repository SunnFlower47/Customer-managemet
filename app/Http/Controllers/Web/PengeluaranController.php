<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengeluaranController extends BaseController
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pengeluaran::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pengeluaran', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        // Filter by month
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pengeluaran', $request->bulan);
        }

        // Filter by year
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pengeluaran', $request->tahun);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pengeluaran', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pengeluaran', '<=', $request->tanggal_sampai);
        }

        $pengeluarans = $query->orderBy('tanggal_pengeluaran', 'desc')->paginate(20);

        // Get filter options
        $kategoriOptions = Pengeluaran::getKategoriOptions();
        $statusOptions = Pengeluaran::getStatusOptions();
        $metodeOptions = Pengeluaran::getMetodePembayaranOptions();

        return view('pengeluarans.index', compact('pengeluarans', 'kategoriOptions', 'statusOptions', 'metodeOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoriOptions = Pengeluaran::getKategoriOptions();
        $metodeOptions = Pengeluaran::getMetodePembayaranOptions();
        $statusOptions = Pengeluaran::getStatusOptions();

        return view('pengeluarans.create', compact('kategoriOptions', 'metodeOptions', 'statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
            'nama_pengeluaran' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_pengeluaran' => 'required|date',
            'metode_pembayaran' => 'required|string|in:tunai,transfer,kartu',
            'status' => 'required|string|in:terkonfirmasi,pending,dibatalkan',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
            $data['bukti_pembayaran'] = $path;
        }

        Pengeluaran::create($data);

        return $this->redirectToRouteWithParams('pengeluarans.index', $request, 'Pengeluaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengeluaran $pengeluaran)
    {
        $pengeluaran->load('user');
        return view('pengeluarans.show', compact('pengeluaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengeluaran $pengeluaran)
    {
        $kategoriOptions = Pengeluaran::getKategoriOptions();
        $metodeOptions = Pengeluaran::getMetodePembayaranOptions();
        $statusOptions = Pengeluaran::getStatusOptions();

        return view('pengeluarans.edit', compact('pengeluaran', 'kategoriOptions', 'metodeOptions', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
            'nama_pengeluaran' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_pengeluaran' => 'required|date',
            'metode_pembayaran' => 'required|string|in:tunai,transfer,kartu',
            'status' => 'required|string|in:terkonfirmasi,pending,dibatalkan',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $data = $request->all();

        // Handle file upload
        if ($request->hasFile('bukti_pembayaran')) {
            // Delete old file if exists
            if ($pengeluaran->bukti_pembayaran) {
                Storage::disk('public')->delete($pengeluaran->bukti_pembayaran);
            }

            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
            $data['bukti_pembayaran'] = $path;
        }

        $pengeluaran->update($data);

        return $this->redirectToRouteWithParams('pengeluarans.index', $request, 'Pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Pengeluaran $pengeluaran)
    {
        // Delete file if exists
        if ($pengeluaran->bukti_pembayaran) {
            Storage::disk('public')->delete($pengeluaran->bukti_pembayaran);
        }

        $pengeluaran->delete();

        return $this->redirectToRouteWithParams('pengeluarans.index', $request, 'Pengeluaran berhasil dihapus.');
    }

    /**
     * Export pengeluarans to PDF
     */
    public function export(Request $request)
    {
        $query = Pengeluaran::with('user');

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_pengeluaran', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pengeluaran', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pengeluaran', $request->tahun);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pengeluaran', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pengeluaran', '<=', $request->tanggal_sampai);
        }

        $pengeluarans = $query->orderBy('tanggal_pengeluaran', 'desc')->get();

        // Get filter info for PDF
        $filters = [
            'kategori' => $request->kategori,
            'status' => $request->status,
            'metode_pembayaran' => $request->metode_pembayaran,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'tanggal_dari' => $request->tanggal_dari,
            'tanggal_sampai' => $request->tanggal_sampai,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pengeluarans.pdf', compact('pengeluarans', 'filters'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'pengeluarans_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }
}
