<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_perusahaan',
        'nama_lengkap_perusahaan',
        'inisial_perusahaan',
        'alamat',
        'nomor_kontak',
        'whatsapp',
        'email_support',
        'logo_path',
        'website',
        'deskripsi',
        'payment_code_prefix',
    ];

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            // Try multiple approaches for shared hosting compatibility
            
            // 1. Try Laravel route first
            try {
                return route('logo', ['filename' => basename($this->logo_path)]);
            } catch (\Exception $e) {
                // Route not available, try direct paths
            }
            
            // 2. Try storage path
            $storagePath = 'storage/' . $this->logo_path;
            if (file_exists(public_path($storagePath))) {
                return asset($storagePath);
            }
            
            // 3. Try direct path in public
            if (file_exists(public_path($this->logo_path))) {
                return asset($this->logo_path);
            }
            
            // 4. Try with uploads folder (common in shared hosting)
            $uploadsPath = 'uploads/' . basename($this->logo_path);
            if (file_exists(public_path($uploadsPath))) {
                return asset($uploadsPath);
            }
        }
        
        // Default fallback
        return asset('icon-192x192.png');
    }

    /**
     * Get formatted WhatsApp number
     */
    public function getFormattedWhatsappAttribute()
    {
        if ($this->whatsapp) {
            // Remove any non-numeric characters
            $number = preg_replace('/[^0-9]/', '', $this->whatsapp);
            // Add +62 if not present
            if (!str_starts_with($number, '62')) {
                $number = '62' . ltrim($number, '0');
            }
            return $number;
        }
        return null;
    }

    /**
     * Get WhatsApp link
     */
    public function getWhatsappLinkAttribute()
    {
        if ($this->formatted_whatsapp) {
            return 'https://wa.me/' . $this->formatted_whatsapp;
        }
        return null;
    }

    /**
     * Get display name for UI/Interface (use main company name)
     */
    public function getDisplayNameAttribute()
    {
        return $this->nama_perusahaan;
    }

    /**
     * Get full name for official documents (PDF, Excel, Faktur)
     */
    public function getOfficialNameAttribute()
    {
        return $this->nama_lengkap_perusahaan ?: $this->nama_perusahaan;
    }

    /**
     * Get short name for UI (use main company name)
     */
    public function getShortNameAttribute()
    {
        return $this->nama_perusahaan;
    }

    /**
     * Get initials (only use manual initials, no auto-generation)
     */
    public function getInitialsAttribute()
    {
        return $this->inisial_perusahaan ?: 'BCM';
    }
}
