<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OltEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'olt_id',
        'event_type',
        'severity',
        'event_data',
        'message',
        'resolved_at',
    ];

    protected $casts = [
        'event_data' => 'array',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the OLT that owns this event
     */
    public function olt()
    {
        return $this->belongsTo(Olt::class);
    }

    /**
     * Scope for unresolved events
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Scope for critical events
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope for recent events
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}
