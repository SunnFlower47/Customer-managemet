<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Pembayaran extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'kode_pembayaran',
        'pelanggan_id',
        'paket_id',
        'nama_paket',
        'harga_paket',
        'bulan_tagihan',
        'tahun_tagihan',
        'jumlah',
        'status',
        'tanggal_bayar',
        'penagih_id',
        'nama_penagih',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'harga_paket' => 'decimal:2',
            'tanggal_bayar' => 'datetime',
        ];
    }

    /**
     * Get the pelanggan that owns the pembayaran.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Get the penagih that owns the pembayaran.
     */
    public function penagih()
    {
        return $this->belongsTo(Penagih::class);
    }

    /**
     * Get the paket that owns the pembayaran.
     */
    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    /**
     * Get the historical package name (fallback to current if not set)
     */
    public function getHistoricalPackageNameAttribute()
    {
        // Priority: nama_paket (historical) > paket relationship > fallback
        if (!empty($this->nama_paket)) {
            return $this->nama_paket;
        }

        // Try to get from relationship if loaded
        if ($this->relationLoaded('paket') && $this->paket) {
            return $this->paket->nama_paket;
        }

        // Try to load relationship if not loaded
        if ($this->paket_id) {
            $paket = $this->paket;
            if ($paket) {
                return $paket->nama_paket;
            }
        }

        // Last resort: try to get from pelanggan's current package
        if ($this->relationLoaded('pelanggan') && $this->pelanggan && $this->pelanggan->paket) {
            return $this->pelanggan->paket->nama_paket;
        }

        // Final fallback
        return 'Paket Tidak Diketahui';
    }

    /**
     * Get the historical collector name (fallback to current if not set)
     */
    public function getHistoricalCollectorNameAttribute()
    {
        return $this->nama_penagih ?: ($this->penagih ? $this->penagih->nama : 'Belum ada penagih');
    }

    /**
     * Get the historical package price (fallback to current if not set)
     */
    public function getHistoricalPackagePriceAttribute()
    {
        return $this->harga_paket ?: ($this->paket ? $this->paket->harga : $this->jumlah);
    }
}
