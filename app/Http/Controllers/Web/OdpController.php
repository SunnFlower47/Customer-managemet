<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\Odp;
use App\Models\Odc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OdpController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Odp::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_odp', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $odps = $query->withCount(['pelanggans' => function($q) {
                $q->where('status', 'aktif');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Sync port_terpakai for all ODPs (ensure data is up to date)
        foreach ($odps as $odp) {
            $activeCount = \App\Models\Pelanggan::where('odp_id', $odp->id)
                ->where('status', 'aktif')
                ->count();

            if ($odp->port_terpakai != $activeCount) {
                $odp->update(['port_terpakai' => $activeCount]);
            }
        }

        return view('odps.index', compact('odps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $odcs = Odc::orderBy('nama')->get();
        return view('odps.create', compact('odcs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_odp' => 'required|string|max:255|unique:odps,kode_odp',
            'nama' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'alamat' => 'nullable|string',
            'kapasitas' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'odc_id' => 'nullable|exists:odcs,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = 'odp_' . time() . '.' . $foto->getClientOriginalExtension();
            $path = $foto->storeAs('odps', $filename, 'public');
            $data['foto'] = $path;
        }

        Odp::create($data);

        return $this->redirectToRouteWithParams('odps.index', $request, 'ODP berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Odp $odp)
    {
        // Sync port_terpakai before showing
        $activeCount = \App\Models\Pelanggan::where('odp_id', $odp->id)
            ->where('status', 'aktif')
            ->count();

        if ($odp->port_terpakai != $activeCount) {
            $odp->update(['port_terpakai' => $activeCount]);
            $odp->refresh();
        }

        $odp->load('pelanggans.paket', 'pelanggans.penagih', 'odc');
        $pelanggans = $odp->pelanggans()->with(['paket', 'penagih'])->paginate(10);

        return view('odps.show', compact('odp', 'pelanggans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Odp $odp)
    {
        $odcs = Odc::orderBy('nama')->get();
        return view('odps.edit', compact('odp', 'odcs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Odp $odp)
    {
        $request->validate([
            'kode_odp' => 'required|string|max:255|unique:odps,kode_odp,' . $odp->id,
            'nama' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'alamat' => 'nullable|string',
            'kapasitas' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'odc_id' => 'nullable|exists:odcs,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($odp->foto) {
                Storage::disk('public')->delete($odp->foto);
            }

            $foto = $request->file('foto');
            $filename = 'odp_' . time() . '.' . $foto->getClientOriginalExtension();
            $path = $foto->storeAs('odps', $filename, 'public');
            $data['foto'] = $path;
        } else {
            // Keep existing foto if not uploading new one
            unset($data['foto']);
        }

        $odp->update($data);

        return $this->redirectToRouteWithParams('odps.index', $request, 'ODP berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Odp $odp)
    {
        // Check if ODP has pelanggans
        if ($odp->pelanggans()->count() > 0) {
            return redirect()->route('odps.index')
                ->with('error', 'ODP tidak dapat dihapus karena masih memiliki pelanggan terhubung.');
        }

        // Delete foto if exists
        if ($odp->foto) {
            Storage::disk('public')->delete($odp->foto);
        }

        $odp->delete();

        return $this->redirectToRouteWithParams('odps.index', $request, 'ODP berhasil dihapus.');
    }
}

