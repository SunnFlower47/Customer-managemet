<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class OltPonPort extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'olt_id',
        'card',
        'port',
        'port_name',
        'status',
        'onu_count',
        'rx_power',
        'tx_power',
        'description',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'card' => 'integer',
            'port' => 'integer',
            'onu_count' => 'integer',
            'rx_power' => 'decimal:2',
            'tx_power' => 'decimal:2',
            'last_synced_at' => 'datetime',
        ];
    }

    /**
     * Get the OLT that owns this port
     */
    public function olt()
    {
        return $this->belongsTo(Olt::class);
    }

    /**
     * Get all ONUs connected to this port
     */
    public function onus()
    {
        return $this->hasMany(Onu::class);
    }

    /**
     * Get port identifier
     */
    public function getPortIdentifierAttribute()
    {
        return "{$this->card}/{$this->port}";
    }

    /**
     * Get full port name
     */
    public function getFullPortNameAttribute()
    {
        return $this->port_name ?: "gpon-olt_{$this->card}/{$this->card}/{$this->port}";
    }
}
