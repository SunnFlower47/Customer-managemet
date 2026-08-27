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

        $odps = $query->withCount([
                'pelanggans as pelanggans_count',                             // semua pelanggan
                'pelanggans as aktif_pelanggans_count' => function($q) {
                    $q->whereIn('status', ['aktif', 'bayar double']);
                },                                                             // hanya aktif
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Sync port_terpakai for all ODPs (ensure data is up to date)
        // Port terpakai = pelanggan aktif + jumlah ODP child
        foreach ($odps as $odp) {
            $odp->syncPortTerpakai();
        }

        return view('odps.index', compact('odps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $odcs = Odc::orderBy('nama')->get();
        // Get active ODPs with coordinates for "Hubungkan ke ODP Terdekat" dropdown
        $activeOdps = Odp::where('status', 'aktif')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('odc')
            ->orderBy('nama')
            ->get();
        return view('odps.create', compact('odcs', 'activeOdps'));
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
            'status' => 'required|in:aktif,nonaktif,penuh',
            'odc_id' => 'nullable|exists:odcs,id',
            'parent_odp_id' => 'nullable|exists:odps,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'parent_odp_id.exists' => 'ODP yang dipilih tidak ditemukan.',
        ]);

        // Validasi mutual exclusive: ODC dan parent_odp tidak bisa dipilih bersamaan
        if ($request->filled('odc_id') && $request->filled('parent_odp_id')) {
            return back()->withErrors([
                'odc_id' => 'Pilih salah satu: Hubungkan ke ODC atau Hubungkan ke ODP terdekat.',
                'parent_odp_id' => 'Pilih salah satu: Hubungkan ke ODC atau Hubungkan ke ODP terdekat.',
            ])->withInput();
        }

        $data = $request->all();

        // Jika pilih parent_odp, set odc_id dari parent (inherit)
        if ($request->filled('parent_odp_id')) {
            $parentOdp = Odp::find($request->parent_odp_id);
            if ($parentOdp) {
                $data['odc_id'] = $parentOdp->odc_id;
            }
        } else {
            // Jika tidak pilih parent_odp, set null
            $data['parent_odp_id'] = null;
        }

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $filename = 'odp_' . time() . '.' . $foto->getClientOriginalExtension();
            $path = $foto->storeAs('odps', $filename, 'public');
            $data['foto'] = $path;
        }

        // Set port terpakai awal = 0 (belum ada pelanggan/child)
        $data['port_terpakai'] = 0;

        $odp = Odp::create($data);

        // Update port terpakai parent ODP jika ada
        if ($odp->parentOdp) {
            $odp->parentOdp->syncPortTerpakai();
            $odp->parentOdp->updateParentPortTerpakai();
        }

        return $this->redirectToRouteWithParams('odps.index', $request, 'ODP berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Odp $odp)
    {
        // Sync port_terpakai before showing (include ODP child)
        $odp->syncPortTerpakai();
        $odp->refresh();

        $odp->load('pelanggans.paket', 'pelanggans.penagih', 'odc', 'parentOdp.odc', 'childOdps');
        $pelanggans = $odp->pelanggans()->with(['paket', 'penagih'])->paginate(10);

        return view('odps.show', compact('odp', 'pelanggans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Odp $odp)
    {
        $odcs = Odc::orderBy('nama')->get();
        // Get active ODPs, exclude the ODP being edited
        $activeOdps = Odp::where('status', 'aktif')
            ->where('id', '!=', $odp->id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('odc')
            ->orderBy('nama')
            ->get();
        return view('odps.edit', compact('odp', 'odcs', 'activeOdps'));
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
            'status' => 'required|in:aktif,nonaktif,penuh',
            'odc_id' => 'nullable|exists:odcs,id',
            'parent_odp_id' => 'nullable|exists:odps,id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'parent_odp_id.exists' => 'ODP yang dipilih tidak ditemukan.',
        ]);

        // Validasi mutual exclusive: ODC dan parent_odp tidak bisa dipilih bersamaan
        if ($request->filled('odc_id') && $request->filled('parent_odp_id')) {
            return back()->withErrors([
                'odc_id' => 'Pilih salah satu: Hubungkan ke ODC atau Hubungkan ke ODP terdekat.',
                'parent_odp_id' => 'Pilih salah satu: Hubungkan ke ODC atau Hubungkan ke ODP terdekat.',
            ])->withInput();
        }

        // Simpan parent_odp_id lama untuk update port terpakai
        $oldParentOdpId = $odp->parent_odp_id;
        $newParentOdpId = $request->filled('parent_odp_id') ? $request->parent_odp_id : null;

        $data = $request->all();

        // Jika pilih parent_odp, set odc_id dari parent (inherit)
        if ($request->filled('parent_odp_id')) {
            $parentOdp = Odp::find($request->parent_odp_id);
            if ($parentOdp) {
                $data['odc_id'] = $parentOdp->odc_id;
            }
        } else {
            // Jika tidak pilih parent_odp, set null
            $data['parent_odp_id'] = null;
        }

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

        // Handle perubahan parent_odp_id: update port terpakai parent lama dan baru
        if ($oldParentOdpId != $newParentOdpId) {
            // Update parent lama (jika ada)
            if ($oldParentOdpId) {
                $oldParent = Odp::find($oldParentOdpId);
                if ($oldParent) {
                    $oldParent->syncPortTerpakai();
                    $oldParent->updateParentPortTerpakai();
                }
            }
            // Update parent baru (jika ada)
            if ($newParentOdpId) {
                $newParent = Odp::find($newParentOdpId);
                if ($newParent) {
                    $newParent->syncPortTerpakai();
                    $newParent->updateParentPortTerpakai();
                }
            }
        }

        // Sync port terpakai ODP ini
        $odp->syncPortTerpakai();

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

        // Check if ODP has child ODPs
        if ($odp->childOdps()->count() > 0) {
            return redirect()->route('odps.index')
                ->with('error', 'ODP tidak dapat dihapus karena masih memiliki ODP child terhubung.');
        }

        // Simpan parent ODP untuk update port terpakai setelah delete
        $parentOdp = $odp->parentOdp;

        // Delete foto if exists
        if ($odp->foto) {
            Storage::disk('public')->delete($odp->foto);
        }

        $odp->delete();

        // Update port terpakai parent ODP setelah delete
        if ($parentOdp) {
            $parentOdp->syncPortTerpakai();
            $parentOdp->updateParentPortTerpakai();
        }

        return $this->redirectToRouteWithParams('odps.index', $request, 'ODP berhasil dihapus.');
    }
}

