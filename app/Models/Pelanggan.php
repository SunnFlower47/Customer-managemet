<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Auditable;
use Illuminate\Support\Facades\DB;

class Pelanggan extends Authenticatable
{
    use HasFactory, Auditable, Notifiable, HasApiTokens;

    protected $fillable = [
        'nama',
        'pppoe',
        'serial_number_stb',
        'alamat',
        'no_hp',
        'paket_id',
        'tanggal_mulai',
        'tanggal_pembayaran',
        'penagih_id',
        'status',
        'password',
        'remember_token',
        'last_login_at',
        'is_default_password',
        'latitude',
        'longitude',
        'odp_id',
        'mikrotik_id',
        'exists_in_mikrotik',
        'mikrotik_last_checked',
        'mikrotik_router_name',
        'mikrotik_status',
        'mikrotik_ip',
        'mikrotik_profile',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_default_password' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'no_hp';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->no_hp;
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the paket that owns the pelanggan.
     */
    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    /**
     * Get the penagih that owns the pelanggan.
     */
    public function penagih()
    {
        return $this->belongsTo(Penagih::class);
    }

    /**
     * Get the pembayarans for the pelanggan.
     */
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Get the package history for the pelanggan.
     */
    public function packageHistory()
    {
        return $this->hasMany(CustomerPackage::class, 'customer_id');
    }

    /**
     * Get the active package for a specific date
     */
    public function getActivePackageForDate($date)
    {
        return $this->packageHistory()
            ->activeOn($date)
            ->with('package')
            ->first();
    }

    /**
     * Get the ODP that owns the pelanggan.
     */
    public function odp()
    {
        return $this->belongsTo(Odp::class);
    }

    /**
     * Get the MikroTik router for this pelanggan.
     */
    public function mikrotik()
    {
        return $this->belongsTo(Mikrotik::class);
    }

    /**
     * Check if pelanggan has location coordinates
     */
    public function hasLocation()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Update ODP port_terpakai when pelanggan is created
        static::created(function ($pelanggan) {
            if ($pelanggan->odp_id) {
                static::updateOdpPortUsage($pelanggan->odp_id);
            }
        });

        // Update ODP port_terpakai when pelanggan is updated
        static::updated(function ($pelanggan) {
            // Get old and new ODP IDs
            $oldOdpId = $pelanggan->getOriginal('odp_id');
            $newOdpId = $pelanggan->odp_id;

            // Update old ODP if changed
            if ($oldOdpId && $oldOdpId != $newOdpId) {
                static::updateOdpPortUsage($oldOdpId);
            }

            // Update new ODP if changed
            if ($newOdpId && $newOdpId != $oldOdpId) {
                static::updateOdpPortUsage($newOdpId);
            } elseif ($newOdpId && $newOdpId == $oldOdpId) {
                // If ODP is the same but status might have changed, update anyway
                static::updateOdpPortUsage($newOdpId);
            }
        });

        // Update ODP port_terpakai when pelanggan is deleted
        static::deleted(function ($pelanggan) {
            if ($pelanggan->odp_id) {
                static::updateOdpPortUsage($pelanggan->odp_id);
            }
        });
    }

    /**
     * Scope for active pelanggans (aktif or bayar double - both can receive bills)
     * Status 'bayar double' means customer pays double but is still active/operational
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['aktif', 'bayar double']);
    }

    /**
     * Update ODP port usage based on active pelanggans
     */
    protected static function updateOdpPortUsage($odpId)
    {
        if (!$odpId) {
            return;
        }

        $odp = Odp::find($odpId);
        if ($odp) {
            // Count active pelanggans connected to this ODP (aktif or bayar double)
            // Use DB query to avoid triggering model events
            $activePelanggansCount = DB::table('pelanggans')
                ->where('odp_id', $odpId)
                ->whereIn('status', ['aktif', 'bayar double'])
                ->count();

            // Update without triggering events to avoid recursion
            DB::table('odps')
                ->where('id', $odpId)
                ->update(['port_terpakai' => $activePelanggansCount]);
        }
    }
}

