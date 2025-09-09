<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\Penagih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PelangganController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pelanggan::with(['paket', 'penagih']);

        // Filter by penagih if user is penagih
        if (Auth::user()->role === 'penagih') {
            $penagihId = Penagih::where('user_id', Auth::id())->value('id');
            if ($penagihId) {
                $query->where('penagih_id', $penagihId);
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('pppoe', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by paket
        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }

        // Filter by penagih (for all roles)
        if ($request->filled('penagih_id')) {
            $query->where('penagih_id', $request->penagih_id);
        }

        $pelanggans = $query->orderBy('created_at', 'desc')->paginate(10);
        $pakets = Paket::where('aktif', true)->get();
        $penagihs = Penagih::where('aktif', true)->get();


        return view('pelanggans.index', compact('pelanggans', 'pakets', 'penagihs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pakets = Paket::where('aktif', true)->get();
        $penagihs = Penagih::where('aktif', true)->get();
        return view('pelanggans.create', compact('pakets', 'penagihs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'pppoe' => 'required|string|max:255|unique:pelanggans,pppoe',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'paket_id' => 'required|exists:pakets,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_pembayaran' => 'required|integer|between:1,31',
            'penagih_id' => 'required|exists:penagihs,id',
            'status' => 'required|in:aktif,nonaktif,suspend'
        ]);

        $pelanggan = Pelanggan::create($request->all());

        // Create initial package history
        \App\Models\CustomerPackage::create([
            'customer_id' => $pelanggan->id,
            'package_id' => $pelanggan->paket_id,
            'start_date' => $pelanggan->tanggal_mulai,
            'end_date' => null, // Still active
            'price' => $pelanggan->paket->harga,
        ]);

        // IMPORTANT: DO NOT update existing payments when creating new customer
        // This violates the IMMUTABLE principle - historical data must remain unchanged
        // Only new payments generated after this creation will use the assigned penagih

        // Preserve pagination and filters when redirecting
        $redirectParams = $request->only(['page', 'search', 'status', 'penagih_id', 'paket_id']);
        return redirect()->route('pelanggans.index', $redirectParams)->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load(['paket', 'penagih']);

        // Load paginated pembayarans for better performance
        $pembayarans = $pelanggan->pembayarans()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pelanggans.show', compact('pelanggan', 'pembayarans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        $pakets = Paket::where('aktif', true)->get();
        $penagihs = Penagih::where('aktif', true)->get();
        return view('pelanggans.edit', compact('pelanggan', 'pakets', 'penagihs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'pppoe' => 'required|string|max:255|unique:pelanggans,pppoe,' . $pelanggan->id,
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'paket_id' => 'required|exists:pakets,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_pembayaran' => 'required|integer|between:1,31',
            'penagih_id' => 'required|exists:penagihs,id',
            'status' => 'required|in:aktif,nonaktif,suspend'
        ]);


        $oldPenagihId = $pelanggan->penagih_id;
        $newPenagihId = $request->penagih_id;
        $oldPaketId = $pelanggan->paket_id;
        $newPaketId = $request->paket_id;

        $pelanggan->update($request->all());

        // Handle package change - create new package history entry
        if ($oldPaketId != $newPaketId) {
            // End the current package history
            $pelanggan->packageHistory()
                ->whereNull('end_date')
                ->update(['end_date' => now()->format('Y-m-d')]);

            // Create new package history entry
            \App\Models\CustomerPackage::create([
                'customer_id' => $pelanggan->id,
                'package_id' => $newPaketId,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => null, // Still active
                'price' => $pelanggan->paket->harga,
            ]);
        }

        // IMPORTANT: DO NOT update existing payments when penagih changes
        // This violates the IMMUTABLE principle - historical data must remain unchanged
        // Only new payments generated after this change will use the new penagih


        // Preserve pagination and filters when redirecting
        $redirectParams = $request->only(['page', 'search', 'status', 'penagih_id', 'paket_id']);
        return redirect()->route('pelanggans.index', $redirectParams)->with('success', 'Pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Pelanggan $pelanggan)
    {
        $pelanggan->delete();

        // Preserve pagination and filters when redirecting
        $redirectParams = $request->only(['page', 'search', 'status', 'penagih_id', 'paket_id']);
        return redirect()->route('pelanggans.index', $redirectParams)->with('success', 'Pelanggan berhasil dihapus.');
    }

    /**
     * Export pelanggans to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Pelanggan::with(['paket', 'penagih']);

        // Filter by penagih if user is penagih
        if (Auth::user()->role === 'penagih') {
            $penagihId = Penagih::where('user_id', Auth::id())->value('id');
            if ($penagihId) {
                $query->where('penagih_id', $penagihId);
            }
        }

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('pppoe', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }
        if ($request->filled('penagih_id') && in_array(Auth::user()->role, ['admin', 'operator'])) {
            $query->where('penagih_id', $request->penagih_id);
        }

        $pelanggans = $query->orderBy('created_at', 'desc')->get();

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('pelanggans.pdf', compact('pelanggans'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'pelanggans_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }
}
