<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class SpeedProfile extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'nama',
        'description',
        'download_speed',
        'upload_speed',
        'profile_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'download_speed' => 'integer',
            'upload_speed' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all ONU services using this profile
     */
    public function onuServices()
    {
        return $this->hasMany(OnuService::class);
    }

    /**
     * Get download speed formatted
     */
    public function getDownloadSpeedFormattedAttribute()
    {
        if ($this->download_speed >= 1000000) {
            return number_format($this->download_speed / 1000000, 2) . ' Gbps';
        } elseif ($this->download_speed >= 1000) {
            return number_format($this->download_speed / 1000, 2) . ' Mbps';
        }
        return $this->download_speed . ' Kbps';
    }

    /**
     * Get upload speed formatted
     */
    public function getUploadSpeedFormattedAttribute()
    {
        if ($this->upload_speed >= 1000000) {
            return number_format($this->upload_speed / 1000000, 2) . ' Gbps';
        } elseif ($this->upload_speed >= 1000) {
            return number_format($this->upload_speed / 1000, 2) . ' Mbps';
        }
        return $this->upload_speed . ' Kbps';
    }

    /**
     * Scope for active profiles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
