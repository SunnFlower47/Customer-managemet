<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\VlanDatabase;
use App\Services\VlanService;
use Illuminate\Http\Request;

class VlanController extends BaseController
{
    protected $vlanService;

    public function __construct(VlanService $vlanService)
    {
        $this->vlanService = $vlanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = VlanDatabase::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vlan_id', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $vlans = $query->orderBy('vlan_id')->paginate(20)->appends($request->query());

        return view('vlans.index', compact('vlans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vlan_id' => 'required|integer|unique:vlan_database,vlan_id',
            'nama' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purpose' => 'nullable|string|max:255',
        ]);

        $this->vlanService->createVlan($request->all());

        return $this->redirectToRouteWithParams('vlans.index', $request, 'VLAN berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VlanDatabase $vlan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purpose' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $this->vlanService->updateVlan($vlan, $request->all());

        return $this->redirectToRouteWithParams('vlans.index', $request, 'VLAN berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, VlanDatabase $vlan)
    {
        $this->vlanService->deleteVlan($vlan);

        return $this->redirectToRouteWithParams('vlans.index', $request, 'VLAN berhasil dihapus.');
    }
}
