<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Odc extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'kode_odc',
        'nama',
        'kapasitas_port',
        'alamat',
        'latitude',
        'longitude',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas_port' => 'integer',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Get all ODPs connected to this ODC.
     */
    public function odps()
    {
        return $this->hasMany(Odp::class);
    }

    /**
     * Get ODPs directly connected to this ODC (not through parent ODP)
     */
    public function directOdps()
    {
        return $this->hasMany(Odp::class)->whereNull('parent_odp_id');
    }

    /**
     * Get total ODP ports used (only direct connections)
     */
    public function getTotalOdpPortsUsedAttribute()
    {
        return $this->directOdps()->count();
    }

    /**
     * Get available ports
     */
    public function getAvailablePortsAttribute()
    {
        return max(0, $this->kapasitas_port - $this->total_odp_ports_used);
    }

    /**
     * Check if ODC is full
     */
    public function getIsFullAttribute()
    {
        return $this->total_odp_ports_used >= $this->kapasitas_port;
    }

    /**
     * Auto-update status based on port usage
     * Status becomes 'penuh' when port terpakai >= kapasitas_port
     * Status becomes 'aktif' when port terpakai < kapasitas_port and status is 'penuh'
     */
    public function syncStatusBasedOnPorts()
    {
        $this->refresh(); // Refresh to get latest data

        // Only update if status is 'aktif' or 'penuh' (don't change 'rusak')
        if ($this->status === 'rusak') {
            return;
        }

        $isFull = $this->is_full;

        if ($isFull && $this->status !== 'penuh') {
            $this->update(['status' => 'penuh']);
        } elseif (!$isFull && $this->status === 'penuh') {
            $this->update(['status' => 'aktif']);
        }
    }
}


