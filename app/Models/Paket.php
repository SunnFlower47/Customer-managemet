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
        'harga',
        'deskripsi',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
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
