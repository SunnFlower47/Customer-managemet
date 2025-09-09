<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerPackage extends Model
{
    use HasFactory;

    protected $table = 'customer_packages';

    protected $fillable = [
        'customer_id',
        'package_id',
        'start_date',
        'end_date',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'price' => 'decimal:2',
        ];
    }

    /**
     * Get the customer that owns the package history.
     */
    public function customer()
    {
        return $this->belongsTo(Pelanggan::class, 'customer_id');
    }

    /**
     * Get the package that owns the package history.
     */
    public function package()
    {
        return $this->belongsTo(Paket::class, 'package_id');
    }

    /**
     * Scope to get active package for a specific date
     */
    public function scopeActiveOn($query, $date)
    {
        return $query->where('start_date', '<=', $date)
                    ->where(function($q) use ($date) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $date);
                    });
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format((float)$this->price, 0, ',', '.');
    }
}
