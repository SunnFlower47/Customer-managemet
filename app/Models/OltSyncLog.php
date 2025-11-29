<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OltSyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'olt_id',
        'sync_type',
        'status',
        'progress_percentage',
        'total_items',
        'processed_items',
        'new_onus',
        'updated_onus',
        'errors',
        'error_message',
        'started_at',
        'completed_at',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
            'total_items' => 'integer',
            'processed_items' => 'integer',
            'new_onus' => 'integer',
            'updated_onus' => 'integer',
            'errors' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'details' => 'array',
        ];
    }

    /**
     * Get the OLT that owns this sync log
     */
    public function olt()
    {
        return $this->belongsTo(Olt::class);
    }

    /**
     * Scope for running syncs
     */
    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    /**
     * Scope for completed syncs
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed syncs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
