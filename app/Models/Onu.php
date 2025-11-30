<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use Carbon\Carbon;

class Onu extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'olt_id',
        'olt_pon_port_id',
        'pelanggan_id',
        'odp_id',
        'serial_number',
        'mac_address',
        'nama',
        'description',
        'ont_type',
        'vendor',
        'model',
        'status',
        'is_active',
        'is_registered',
        'card',
        'port',
        'onu_id',
        'rx_power',
        'olt_rx_power',
        'tx_power',
        'ip_address',
        'gateway',
        'subnet_mask',
        'uptime_seconds',
        'last_online_at',
        'last_offline_at',
        'last_synced_at',
        'olt_config',
        'last_error',
        'unmapped_to_pelanggan',
        'last_event_at',
        'last_event_type',
    ];

    protected function casts(): array
    {
        return [
            'card' => 'integer',
            'port' => 'integer',
            'onu_id' => 'integer',
            'rx_power' => 'decimal:2',
            'olt_rx_power' => 'decimal:2',
            'tx_power' => 'decimal:2',
            'uptime_seconds' => 'integer',
            'is_active' => 'boolean',
            'is_registered' => 'boolean',
            'last_online_at' => 'datetime',
            'last_offline_at' => 'datetime',
            'last_synced_at' => 'datetime',
            'olt_config' => 'array',
            'unmapped_to_pelanggan' => 'boolean',
            'last_event_at' => 'datetime',
        ];
    }

    /**
     * Get the OLT that owns this ONU
     */
    public function olt()
    {
        return $this->belongsTo(Olt::class);
    }

    /**
     * Get the PON port this ONU is connected to
     */
    public function ponPort()
    {
        return $this->belongsTo(OltPonPort::class, 'olt_pon_port_id');
    }

    /**
     * Get the pelanggan associated with this ONU
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Get the ODP associated with this ONU
     */
    public function odp()
    {
        return $this->belongsTo(Odp::class);
    }

    /**
     * Get all services for this ONU
     */
    public function services()
    {
        return $this->hasMany(OnuService::class)->orderBy('service_id');
    }

    /**
     * Get primary service (service_id = 1)
     */
    public function primaryService()
    {
        return $this->hasOne(OnuService::class)->where('service_id', 1);
    }

    /**
     * Get uptime formatted
     */
    public function getUptimeFormattedAttribute()
    {
        if ($this->uptime_seconds <= 0) {
            return '0 detik';
        }

        $days = floor($this->uptime_seconds / 86400);
        $hours = floor(($this->uptime_seconds % 86400) / 3600);
        $minutes = floor(($this->uptime_seconds % 3600) / 60);
        $seconds = $this->uptime_seconds % 60;

        $parts = [];
        if ($days > 0) $parts[] = "{$days} hari";
        if ($hours > 0) $parts[] = "{$hours} jam";
        if ($minutes > 0) $parts[] = "{$minutes} menit";
        if ($seconds > 0 && count($parts) < 2) $parts[] = "{$seconds} detik";

        return implode(' ', $parts) ?: '0 detik';
    }

    /**
     * Get duration online since last_online_at
     */
    public function getDurationOnlineAttribute()
    {
        if (!$this->last_online_at || $this->status !== 'online') {
            return 0;
        }

        return $this->last_online_at->diffInSeconds(now());
    }

    /**
     * Scope for online ONUs
     */
    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    /**
     * Scope for offline ONUs
     */
    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    /**
     * Scope for registered ONUs
     */
    public function scopeRegistered($query)
    {
        return $query->where('is_registered', true);
    }

    /**
     * Scope for unregistered ONUs
     */
    public function scopeUnregistered($query)
    {
        return $query->where('is_registered', false);
    }

    /**
     * Scope for active ONUs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for unmapped ONUs (belum dimapping ke pelanggan)
     */
    public function scopeUnmapped($query)
    {
        return $query->where('unmapped_to_pelanggan', true)->orWhereNull('pelanggan_id');
    }

    /**
     * Get events for this ONU
     */
    public function events()
    {
        return $this->hasMany(OnuEvent::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get unresolved events
     */
    public function unresolvedEvents()
    {
        return $this->hasMany(OnuEvent::class)->whereNull('resolved_at');
    }
}
