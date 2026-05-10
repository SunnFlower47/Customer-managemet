<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Paket extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'nama_paket',
        'harga_dasar',
        'ppn_nominal',
        'bhp_nominal',
        'uso_nominal',
        'harga',
        'deskripsi',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga_dasar' => 'decimal:2',
            'ppn_nominal' => 'decimal:2',
            'bhp_nominal' => 'decimal:2',
            'uso_nominal' => 'decimal:2',
            'harga' => 'decimal:2',
            'aktif' => 'boolean',
        ];
    }

    /**
     * Get the pelanggans for the paket.
     */
    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class);
    }
}
