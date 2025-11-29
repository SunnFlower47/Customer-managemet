<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class VlanDatabase extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'vlan_database';

    protected $fillable = [
        'vlan_id',
        'nama',
        'description',
        'purpose',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'vlan_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope for active VLANs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
