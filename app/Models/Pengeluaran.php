<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Pengeluaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kategori',
        'nama_pengeluaran',
        'deskripsi',
        'jumlah',
        'tanggal_pengeluaran',
        'metode_pembayaran',
        'status',
        'bukti_pembayaran',
        'user_id',
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date',
        'jumlah' => 'decimal:2',
    ];

    /**
     * Get the user that created the expense
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pengeluaran', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by month/year
     */
    public function scopeByMonthYear($query, $month, $year)
    {
        return $query->whereMonth('tanggal_pengeluaran', $month)
                    ->whereYear('tanggal_pengeluaran', $year);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedJumlahAttribute()
    {
        return 'Rp ' . number_format((float) $this->jumlah, 0, ',', '.');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'terkonfirmasi' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'dibatalkan' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get payment method badge class
     */
    public function getMetodeBadgeClassAttribute()
    {
        return match($this->metode_pembayaran) {
            'tunai' => 'bg-blue-100 text-blue-800',
            'transfer' => 'bg-purple-100 text-purple-800',
            'kartu' => 'bg-indigo-100 text-indigo-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get available categories
     */
    public static function getKategoriOptions()
    {
        return [
            'operasional' => 'Operasional',
            'maintenance' => 'Maintenance',
            'gaji' => 'Gaji Karyawan',
            'listrik' => 'Listrik',
            'internet' => 'Internet',
            'sewa' => 'Sewa',
            'peralatan' => 'Peralatan',
            'marketing' => 'Marketing',
            'lainnya' => 'Lainnya'
        ];
    }

    /**
     * Get available payment methods
     */
    public static function getMetodePembayaranOptions()
    {
        return [
            'tunai' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'kartu' => 'Kartu Kredit/Debit'
        ];
    }

    /**
     * Get available statuses
     */
    public static function getStatusOptions()
    {
        return [
            'terkonfirmasi' => 'Terkonfirmasi',
            'pending' => 'Pending',
            'dibatalkan' => 'Dibatalkan'
        ];
    }
}
