<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\Penagih;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\MikrotikService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PelangganExport;

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
                  ->orWhere('serial_number_stb', 'like', "%{$search}%")
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

        // Filter by ODP
        if ($request->filled('odp_id')) {
            $query->where('odp_id', $request->odp_id);
        }

        // Advanced filters - Date range for tanggal_mulai
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_mulai', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_mulai', '<=', $request->date_to);
        }

        // Advanced filters - Date range for created_at
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
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
        // Store current filter parameters in session before create
        $currentFilters = request()->only(['search', 'status', 'penagih_id', 'paket_id', 'page']);
        session(['pelanggan_filters' => $currentFilters]);

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
            'nik' => 'nullable|string|max:20',
            'pppoe' => 'required|string|max:255|unique:pelanggans,pppoe',
            'serial_number_stb' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'paket_id' => 'required|exists:pakets,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_pembayaran' => 'required|integer|between:1,31',
            'penagih_id' => 'nullable|exists:penagihs,id',
            'status' => 'required|in:aktif,isolir,bayar double,nonaktif',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'odp_id' => 'nullable|exists:odps,id',
        ]);

        $pelanggan = Pelanggan::create($request->all());

        // Auto-generate default password for new customer
        $defaultPassword = '123456';
        $pelanggan->update([
            'password' => Hash::make($defaultPassword),
            'is_default_password' => true
        ]);

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
        // Use session to store original filter parameters before create
        $originalFilters = session('pelanggan_filters', []);
        $redirectParams = array_merge($originalFilters, $request->only(['page']));

        // Clear the stored filters after use
        session()->forget('pelanggan_filters');

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

        // Check MikroTik status (optional, non-breaking)
        $mikrotikInfo = null;
        if (\App\Models\Mikrotik::where('is_active', true)->exists() && $pelanggan->pppoe) {
            try {
                $mikrotikService = app(\App\Services\MikrotikService::class);

                // Check in all active routers
                foreach (\App\Models\Mikrotik::where('is_active', true)->get() as $router) {
                    $pppoe = $mikrotikService->findPppoe($router, $pelanggan->pppoe);

                    if ($pppoe) {
                        // Found in this router
                        $mikrotikInfo = [
                            'exists' => true,
                            'router' => $router,
                            'pppoe_data' => $pppoe,
                            'status' => $pppoe['disabled'] ?? 'active',
                            'ip' => $pppoe['remote-address'] ?? null,
                            'profile' => $pppoe['profile'] ?? null,
                        ];

                        // Update pelanggan record
                        $pelanggan->update([
                            'exists_in_mikrotik' => true,
                            'mikrotik_id' => $router->id,
                            'mikrotik_router_name' => $router->nama,
                            'mikrotik_status' => $pppoe['disabled'] === 'true' ? 'disabled' : 'active',
                            'mikrotik_ip' => $pppoe['remote-address'] ?? null,
                            'mikrotik_profile' => $pppoe['profile'] ?? null,
                            'mikrotik_last_checked' => now(),
                        ]);

                        break; // Found, stop searching
                    }
                }

                // If not found in any router
                if (!$mikrotikInfo) {
                    $pelanggan->update([
                        'exists_in_mikrotik' => false,
                        'mikrotik_last_checked' => now(),
                    ]);
                    $mikrotikInfo = ['exists' => false];
                }
            } catch (\Exception $e) {
                // If error, continue without MikroTik info (non-breaking)
                Log::warning('MikroTik check failed for pelanggan ' . $pelanggan->id . ': ' . $e->getMessage());
            }
        }

        return view('pelanggans.show', compact('pelanggan', 'pembayarans', 'mikrotikInfo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        // Store current filter parameters in session before edit
        $currentFilters = request()->only(['search', 'status', 'penagih_id', 'paket_id', 'page']);
        session(['pelanggan_filters' => $currentFilters]);

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
            'nik' => 'nullable|string|max:20',
            'pppoe' => 'required|string|max:255|unique:pelanggans,pppoe,' . $pelanggan->id,
            'serial_number_stb' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'paket_id' => 'required|exists:pakets,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_pembayaran' => 'required|integer|between:1,31',
            'penagih_id' => 'nullable|exists:penagihs,id',
            'status' => 'required|in:aktif,isolir,bayar double,nonaktif',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'odp_id' => 'nullable|exists:odps,id',
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
        // Use session to store original filter parameters before edit
        $originalFilters = session('pelanggan_filters', []);
        $redirectParams = array_merge($originalFilters, $request->only(['page']));

        // Clear the stored filters after use
        session()->forget('pelanggan_filters');

        return redirect()->route('pelanggans.index', $redirectParams)->with('success', 'Pelanggan berhasil diperbarui.');
    }

    /**
     * Bulk delete pelanggans
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|json'
        ]);

        $ids = json_decode($request->ids, true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $count = Pelanggan::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', "Berhasil menghapus {$count} pelanggan.");
    }

    /**
     * Bulk update status pelanggans
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|json',
            'status' => 'required|in:aktif,isolir,bayar double'
        ]);

        $ids = json_decode($request->ids, true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $count = Pelanggan::whereIn('id', $ids)->update(['status' => $request->status]);

        return redirect()->back()->with('success', "Berhasil mengubah status {$count} pelanggan menjadi " . ucfirst($request->status) . ".");
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

        // Jangan masukkan pelanggan yang isolir
        $query->where('status', '!=', 'isolir');

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

        if ($request->filled('status') && $request->status !== 'isolir') {
            $query->where('status', $request->status);
        }
        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }
        if ($request->filled('penagih_id') && in_array(Auth::user()->role, ['admin', 'operator'])) {
            $query->where('penagih_id', $request->penagih_id);
        }

        // Limit to prevent OOM on shared hosting (max 500 records)
        $pelanggans = $query->orderBy('paket_id')->orderBy('nama')->limit(500)->get();

        // Temporarily increase memory limit for PDF generation
        $prevMemory = ini_get('memory_limit');
        ini_set('memory_limit', '256M');

        $pdf = Pdf::loadView('pelanggans.pdf', compact('pelanggans'))
            ->setPaper('A4', 'landscape')
            ->setOptions([
                'isRemoteEnabled'        => false,
                'isHtml5ParserEnabled'   => false,
                'isFontSubsettingEnabled'=> true,
                'dpi'                    => 72,
                'defaultFont'            => 'Arial',
            ]);

        ini_set('memory_limit', $prevMemory);

        $filename = 'pelanggans_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export pelanggans to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Pelanggan::with(['paket', 'penagih'])->orderBy('paket_id')->orderBy('nama');

        // Jangan masukkan pelanggan yang isolir
        $query->where('status', '!=', 'isolir');

        if (Auth::user()->role === 'penagih') {
            $penagihId = Penagih::where('user_id', Auth::id())->value('id');
            if ($penagihId) {
                $query->where('penagih_id', $penagihId);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('pppoe', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'isolir') {
            $query->where('status', $request->status);
        }
        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }
        if ($request->filled('penagih_id') && in_array(Auth::user()->role, ['admin', 'operator'])) {
            $query->where('penagih_id', $request->penagih_id);
        }

        $filename = 'daftar_pelanggan_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Pass query builder (bukan ->get()) agar proses chunked & hemat memori
        return Excel::download(new PelangganExport($query), $filename);
    }

    /**
     * Provide realtime search suggestions.
     */
    public function suggest(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
        ]);

        $search = $request->input('q', '');

        $query = Pelanggan::query()
            ->select('id', 'nama', 'pppoe', 'no_hp', 'alamat', 'penagih_id')
            ->with(['penagih:id,nama'])
            ->orderBy('nama')
            ->limit(8);

        if (Auth::user()->role === 'penagih') {
            $penagihId = Penagih::where('user_id', Auth::id())->value('id');
            if ($penagihId) {
                $query->where('penagih_id', $penagihId);
            }
        }

        if (strlen($search) >= 1) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('pppoe', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        } else {
            $query->limit(5);
        }

        $results = $query->get()->map(function ($pelanggan) {
            return [
                'id' => $pelanggan->id,
                'nama' => $pelanggan->nama,
                'pppoe' => $pelanggan->pppoe,
                'no_hp' => $pelanggan->no_hp,
                'alamat' => $pelanggan->alamat,
                'penagih' => $pelanggan->penagih?->nama,
                'detail_url' => route('pelanggans.show', $pelanggan),
            ];
        });

        return response()->json([
            'data' => $results,
        ]);
    }
}
