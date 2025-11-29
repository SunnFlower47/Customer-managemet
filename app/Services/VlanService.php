<?php

namespace App\Services;

use App\Models\VlanDatabase;
use Illuminate\Support\Facades\Log;

class VlanService
{
    /**
     * Get all active VLANs
     */
    public function getActiveVlans()
    {
        return VlanDatabase::active()->orderBy('vlan_id')->get();
    }

    /**
     * Create VLAN
     */
    public function createVlan(array $data): VlanDatabase
    {
        return VlanDatabase::create([
            'vlan_id' => $data['vlan_id'],
            'nama' => $data['nama'],
            'description' => $data['description'] ?? null,
            'purpose' => $data['purpose'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Update VLAN
     */
    public function updateVlan(VlanDatabase $vlan, array $data): VlanDatabase
    {
        $vlan->update($data);
        return $vlan->fresh();
    }

    /**
     * Delete VLAN
     */
    public function deleteVlan(VlanDatabase $vlan): bool
    {
        return $vlan->delete();
    }

    /**
     * Get VLAN by ID
     */
    public function getVlanById(int $vlanId): ?VlanDatabase
    {
        return VlanDatabase::where('vlan_id', $vlanId)->first();
    }
}

