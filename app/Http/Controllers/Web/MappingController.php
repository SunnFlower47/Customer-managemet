<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Odp;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class MappingController extends BaseController
{
    /**
     * Display the mapping page with all ODPs and customers
     */
    public function index(Request $request)
    {
        // Get all ODPs
        $odps = Odp::active()->get();

        // Get all pelanggans with location
        $pelanggansQuery = Pelanggan::whereNotNull('latitude')
            ->whereNotNull('longitude');

        // Filter by ODP
        if ($request->filled('odp_id')) {
            $pelanggansQuery->where('odp_id', $request->odp_id);
        }

        // Filter by penagih
        if ($request->filled('penagih_id')) {
            $pelanggansQuery->where('penagih_id', $request->penagih_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $pelanggansQuery->where('status', $request->status);
        }

        $pelanggans = $pelanggansQuery->with(['paket', 'penagih', 'odp'])->get();

        // Get filter options
        $allOdps = Odp::orderBy('nama')->get();
        $penagihs = \App\Models\Penagih::orderBy('nama')->get();

        return view('mapping.index', compact('odps', 'pelanggans', 'allOdps', 'penagihs'));
    }

    /**
     * Update pelanggan location from mapping page
     */
    public function updatePelangganLocation(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'odp_id' => 'nullable|exists:odps,id',
        ]);

        $pelanggan->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'odp_id' => $request->odp_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Koordinat pelanggan berhasil diperbarui.',
            'data' => [
                'id' => $pelanggan->id,
                'nama' => $pelanggan->nama,
                'latitude' => $pelanggan->latitude,
                'longitude' => $pelanggan->longitude,
                'odp_id' => $pelanggan->odp_id,
            ]
        ]);
    }

    /**
     * Search pelanggans for mapping
     */
    public function searchPelanggans(Request $request)
    {
        $search = $request->get('search', '');

        $pelanggans = Pelanggan::where('nama', 'like', "%{$search}%")
            ->orWhere('pppoe', 'like', "%{$search}%")
            ->orWhere('no_hp', 'like', "%{$search}%")
            ->select('id', 'nama', 'pppoe', 'no_hp', 'alamat', 'latitude', 'longitude', 'odp_id')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pelanggans
        ]);
    }
}

