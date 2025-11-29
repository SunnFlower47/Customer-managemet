<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Ticket extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'kode_ticket',
        'pelanggan_id',
        'judul',
        'deskripsi',
        'kategori',
        'prioritas',
        'status',
        'assigned_to',
        'resolved_at',
        'rating',
        'customer_feedback',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'rating' => 'integer',
        ];
    }

    /**
     * Get the pelanggan that owns the ticket.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Get the user assigned to the ticket.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the comments for the ticket.
     */
    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Get the attachments for the ticket.
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Generate ticket code
     */
    public static function generateTicketCode()
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');

        // Get the last ticket for today
        $lastTicket = self::where('kode_ticket', 'like', $prefix . $date . '%')
            ->orderBy('kode_ticket', 'desc')
            ->first();

        if ($lastTicket) {
            $lastNumber = (int) substr($lastTicket->kode_ticket, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'open' => 'red',
            'in_progress' => 'yellow',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute()
    {
        return match($this->prioritas) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        return match($this->kategori) {
            'technical' => 'Technical',
            'billing' => 'Billing',
            'service' => 'Service',
            'other' => 'Other',
            default => 'Unknown'
        };
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute()
    {
        return match($this->prioritas) {
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
            default => 'Unknown'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => 'Unknown'
        };
    }
}
