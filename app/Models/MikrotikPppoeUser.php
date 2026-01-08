<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MikrotikPppoeUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'mikrotik_id',
        'pelanggan_id',
        'username',
        'password',
        'service',
        'profile',
        'local_address',
        'remote_address',
        'mac_address',
        'status',
        'is_active',
        'last_seen',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen' => 'datetime',
    ];

    public function mikrotik(): BelongsTo
    {
        return $this->belongsTo(Mikrotik::class);
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }
}
