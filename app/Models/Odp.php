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
     * Scope for active ODPs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }
}

