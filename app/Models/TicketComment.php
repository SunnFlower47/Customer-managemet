<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class TicketComment extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'pelanggan_id',
        'comment',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    /**
     * Get the ticket that owns the comment.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pelanggan that owns the comment.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Get the author name
     */
    public function getAuthorNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }

        if ($this->pelanggan) {
            return $this->pelanggan->nama;
        }

        return 'Unknown';
    }

    /**
     * Get the author type
     */
    public function getAuthorTypeAttribute()
    {
        if ($this->user) {
            return 'admin';
        }

        if ($this->pelanggan) {
            return 'customer';
        }

        return 'unknown';
    }
}
