<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Mikrotik extends Model
{
    use HasFactory, SoftDeletes;

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
        'last_error'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'port' => 'integer',
        'last_connected_at' => 'datetime',
    ];

    // Encrypt password when setting
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    // Decrypt password when getting (custom accessor/helper)
    public function getDecryptedPasswordAttribute()
    {
        try {
            return Crypt::decryptString($this->attributes['password']);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function pppoeUsers(): HasMany
    {
        return $this->hasMany(MikrotikPppoeUser::class);
    }
    
    // Direct relationship to Customers linked to this router
    public function pelanggans(): HasMany
    {
        return $this->hasMany(Pelanggan::class);
    }
}
