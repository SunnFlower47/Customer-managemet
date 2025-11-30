<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Olt extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'kode_olt',
        'nama',
        'ip_address',
        'port',
        'snmp_community',
        'snmp_version',
        'vendor',
        'model',
        'firmware_version',
        'connection_type',
        'api_endpoint',
        'username',
        'password',
        'latitude',
        'longitude',
        'alamat',
        'description',
        'total_ports',
        'ports_terpakai',
        'onu_terhubung',
        'status',
        'is_active',
        'last_checked_at',
        'last_error',
        'temperature',
        'fan_speed',
        'power_supply_status',
        'last_temperature_check',
        'last_fan_check',
        'last_power_check',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'total_ports' => 'integer',
            'ports_terpakai' => 'integer',
            'onu_terhubung' => 'integer',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_active' => 'boolean',
            'last_checked_at' => 'datetime',
            'temperature' => 'decimal:2',
            'fan_speed' => 'array',
            'last_temperature_check' => 'datetime',
            'last_fan_check' => 'datetime',
            'last_power_check' => 'datetime',
        ];
    }

    /**
     * Encrypt password before saving
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = encrypt($value);
        }
    }

    /**
     * Get decrypted password
     */
    public function getDecryptedPasswordAttribute()
    {
        try {
            return $this->attributes['password'] ? decrypt($this->attributes['password']) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get all PON ports for this OLT
     */
    public function ponPorts()
    {
        return $this->hasMany(OltPonPort::class);
    }

    /**
     * Get all ONUs connected to this OLT
     */
    public function onus()
    {
        return $this->hasMany(Onu::class);
    }

    /**
     * Get all ODPs connected to this OLT
     */
    public function odps()
    {
        return $this->hasMany(Odp::class);
    }

    /**
     * Get sync logs for this OLT
     */
    public function syncLogs()
    {
        return $this->hasMany(OltSyncLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get latest sync log
     */
    public function latestSyncLog()
    {
        return $this->hasOne(OltSyncLog::class)->latestOfMany();
    }

    /**
     * Get events for this OLT
     */
    public function events()
    {
        return $this->hasMany(OltEvent::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get unresolved events
     */
    public function unresolvedEvents()
    {
        return $this->hasMany(OltEvent::class)->whereNull('resolved_at');
    }

    /**
     * Get available ports
     */
    public function getAvailablePortsAttribute()
    {
        return max(0, $this->total_ports - $this->ports_terpakai);
    }

    /**
     * Scope for active OLTs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for online OLTs
     */
    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    /**
     * Scope for offline OLTs
     */
    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    /**
     * Get full port name (e.g., gpon-olt_1/1/1)
     */
    public function getFullPortName($card, $port)
    {
        return "gpon-olt_{$card}/{$card}/{$port}";
    }
}
