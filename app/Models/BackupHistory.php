<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BackupHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'file_path',
        'file_size',
        'type',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user who created the backup
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get download URL
     */
    public function getDownloadUrlAttribute()
    {
        return route('settings.backup.download', $this->id);
    }
}