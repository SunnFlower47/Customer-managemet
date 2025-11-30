<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnuEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'onu_id',
        'event_type',
        'severity',
        'old_value',
        'new_value',
        'event_data',
        'message',
        'user_id',
        'resolved_at',
    ];

    protected $casts = [
        'event_data' => 'array',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the ONU that owns this event
     */
    public function onu()
    {
        return $this->belongsTo(Onu::class);
    }

    /**
     * Get the user who triggered this event (if manual)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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

    /**
     * Scope for specific event type
     */
    public function scopeOfType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }
}
