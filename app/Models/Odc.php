<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Odc extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'kode_odc',
        'nama',
        'kapasitas_port',
        'alamat',
        'latitude',
        'longitude',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas_port' => 'integer',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Get all ODPs connected to this ODC.
     */
    public function odps()
    {
        return $this->hasMany(Odp::class);
    }
}


