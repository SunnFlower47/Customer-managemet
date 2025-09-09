<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penagih extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'no_hp',
        'alamat',
        'user_id',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the penagih.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pelanggans for the penagih.
     */
    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class);
    }

    /**
     * Get the pembayarans for the penagih.
     */
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
