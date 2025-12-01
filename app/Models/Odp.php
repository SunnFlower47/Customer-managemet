<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Odp extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'kode_odp',
        'nama',
        'latitude',
        'longitude',
        'alamat',
        'kapasitas',
        'port_terpakai',
        'status',
        'foto',
        'olt_id',
        'odc_id',
        'parent_odp_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'kapasitas' => 'integer',
            'port_terpakai' => 'integer',
        ];
    }

    /**
     * Get the OLT that owns this ODP
     */
    public function olt()
    {
        return $this->belongsTo(Olt::class);
    }

    /**
     * Get the ODC that owns this ODP
     */
    public function odc()
    {
        return $this->belongsTo(Odc::class);
    }

    /**
     * Get all pelanggans connected to this ODP
     */
    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class);
    }

    /**
     * Get the parent ODP (if this ODP is connected to another ODP)
     */
    public function parentOdp()
    {
        return $this->belongsTo(Odp::class, 'parent_odp_id');
    }

    /**
     * Get all child ODPs connected to this ODP
     */
    public function childOdps()
    {
        return $this->hasMany(Odp::class, 'parent_odp_id');
    }

    /**
     * Get active pelanggans count
     */
    public function getActivePelanggansCountAttribute()
    {
        return $this->pelanggans()->where('status', 'aktif')->count();
    }

    /**
     * Get available ports
     */
    public function getAvailablePortsAttribute()
    {
        return max(0, $this->kapasitas - $this->port_terpakai);
    }

    /**
     * Calculate port terpakai: pelanggan aktif + jumlah ODP child
     */
    public function calculatePortTerpakai()
    {
        $activePelanggans = $this->pelanggans()->where('status', 'aktif')->count();
        $childOdpsCount = $this->childOdps()->count();
        return $activePelanggans + $childOdpsCount;
    }

    /**
     * Update port terpakai based on current data
     */
    public function syncPortTerpakai()
    {
        $portTerpakai = $this->calculatePortTerpakai();
        if ($this->port_terpakai != $portTerpakai) {
            $this->update(['port_terpakai' => $portTerpakai]);
        }
        // Auto-update status based on port usage
        $this->syncStatusBasedOnPorts();
        return $portTerpakai;
    }

    /**
     * Auto-update status based on port usage
     * Status becomes 'penuh' when port_terpakai >= kapasitas
     * Status becomes 'aktif' when port_terpakai < kapasitas and status is 'penuh'
     */
    public function syncStatusBasedOnPorts()
    {
        $this->refresh(); // Refresh to get latest port_terpakai

        // Only update if status is 'aktif' or 'penuh' (don't change 'nonaktif')
        if (!in_array($this->status, ['aktif', 'penuh'])) {
            return;
        }

        $isFull = $this->port_terpakai >= $this->kapasitas;

        if ($isFull && $this->status !== 'penuh') {
            $this->update(['status' => 'penuh']);
        } elseif (!$isFull && $this->status === 'penuh') {
            $this->update(['status' => 'aktif']);
        }
    }

    /**
     * Update parent ODP port terpakai (cascade update)
     */
    public function updateParentPortTerpakai()
    {
        if ($this->parentOdp) {
            $this->parentOdp->syncPortTerpakai();
            // Cascade to parent's parent if exists
            if ($this->parentOdp->parentOdp) {
                $this->parentOdp->updateParentPortTerpakai();
            }
        }
    }

    /**
     * Scope for active ODPs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }
}

