<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Mikrotik extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'nama',
        'ip_address',
        'port',
        'username',
        'password',
        'routeros_version',
        'location',
        'description',
        'is_active',
        'last_connected_at',
        'connection_status',
        'last_error',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'is_active' => 'boolean',
            'last_connected_at' => 'datetime',
        ];
    }

    /**
     * Encrypt password before saving
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = encrypt($value);
    }

    /**
     * Get decrypted password
     */
    public function getDecryptedPasswordAttribute()
    {
        try {
            return decrypt($this->attributes['password']);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get all pelanggans using this router
     */
    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class);
    }

    /**
     * Scope for active routers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for online routers
     */
    public function scopeOnline($query)
    {
        return $query->where('connection_status', 'online');
    }
}
