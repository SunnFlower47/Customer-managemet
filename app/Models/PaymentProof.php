<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class PaymentProof extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'pembayaran_id',
        'pelanggan_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'status',
        'admin_notes',
        'verified_by',
        'verified_at',
        'submission_method',
        'whatsapp_message_id',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    /**
     * Get the pembayaran that owns the payment proof.
     */
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }

    /**
     * Get the pelanggan that owns the payment proof.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Get the user who verified the payment proof.
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if ($this->file_size) {
            $bytes = $this->file_size;
            $units = ['B', 'KB', 'MB', 'GB'];

            for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                $bytes /= 1024;
            }

            return round($bytes, 2) . ' ' . $units[$i];
        }
        return null;
    }
}
