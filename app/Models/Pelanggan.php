<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Pelanggan extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'nama',
        'pppoe',
        'alamat',
        'no_hp',
        'paket_id',
        'tanggal_mulai',
        'tanggal_pembayaran',
        'penagih_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
        ];
    }

    /**
     * Get the paket that owns the pelanggan.
     */
    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    /**
     * Get the penagih that owns the pelanggan.
     */
    public function penagih()
    {
        return $this->belongsTo(Penagih::class);
    }

    /**
     * Get the pembayarans for the pelanggan.
     */
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Get the package history for the pelanggan.
     */
    public function packageHistory()
    {
        return $this->hasMany(CustomerPackage::class, 'customer_id');
    }

    /**
     * Get the active package for a specific date
     */
    public function getActivePackageForDate($date)
    {
        return $this->packageHistory()
            ->activeOn($date)
            ->with('package')
            ->first();
    }
}
