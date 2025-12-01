<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Odc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OdcController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Odc::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_odc', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $odcs = $query
            ->withCount([
                'odps as direct_odps_count' => function ($q) {
                    $q->whereNull('parent_odp_id'); // Hanya ODP yang terhubung langsung
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        return view('odcs.index', compact('odcs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('odcs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_odc' => 'required|string|max:255|unique:odcs,kode_odc',
            'nama' => 'required|string|max:255',
            'kapasitas_port' => 'required|integer|min:0',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:aktif,penuh,rusak',
            'keterangan' => 'nullable|string',
        ]);

        Odc::create($request->all());

        return $this->redirectToRouteWithParams('odcs.index', $request, 'ODC berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Odc $odc)
    {
        $odc->load(['odps.pelanggans']);

        // Port terpakai ODC = jumlah ODP yang terhubung LANGSUNG (parent_odp_id IS NULL)
        // ODP yang terhubung melalui parent ODP tidak menghitung port ODC
        $usedPorts = $odc->odps()->whereNull('parent_odp_id')->count();

        return view('odcs.show', compact('odc', 'usedPorts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Odc $odc)
    {
        return view('odcs.edit', compact('odc'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Odc $odc)
    {
        $request->validate([
            'kode_odc' => 'required|string|max:255|unique:odcs,kode_odc,' . $odc->id,
            'nama' => 'required|string|max:255',
            'kapasitas_port' => 'required|integer|min:0',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => 'required|in:aktif,penuh,rusak',
            'keterangan' => 'nullable|string',
        ]);

        $odc->update($request->all());

        return $this->redirectToRouteWithParams('odcs.index', $request, 'ODC berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Odc $odc)
    {
        if ($odc->odps()->count() > 0) {
            return redirect()->route('odcs.index')
                ->with('error', 'ODC tidak dapat dihapus karena masih memiliki ODP terhubung.');
        }

        $odc->delete();

        return $this->redirectToRouteWithParams('odcs.index', $request, 'ODC berhasil dihapus.');
    }
}


