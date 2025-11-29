<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class OnuService extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'onu_id',
        'service_id',
        'wan_mode',
        'pppoe_username',
        'pppoe_password',
        'static_ip',
        'static_gateway',
        'static_subnet',
        'static_dns1',
        'static_dns2',
        'vlan_id',
        'vlan_priority',
        'vlan_tagged',
        'speed_profile_id',
        'download_speed',
        'upload_speed',
        'veip_enabled',
        'veip_ip',
        'tr069_enabled',
        'tr069_url',
        'lan_port_config',
        'wifi_config',
        'remote_access_rules',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'service_id' => 'integer',
            'vlan_id' => 'integer',
            'vlan_priority' => 'integer',
            'vlan_tagged' => 'boolean',
            'download_speed' => 'integer',
            'upload_speed' => 'integer',
            'veip_enabled' => 'boolean',
            'tr069_enabled' => 'boolean',
            'is_active' => 'boolean',
            'lan_port_config' => 'array',
            'wifi_config' => 'array',
            'remote_access_rules' => 'array',
        ];
    }

    /**
     * Get the ONU that owns this service
     */
    public function onu()
    {
        return $this->belongsTo(Onu::class);
    }

    /**
     * Get the speed profile for this service
     */
    public function speedProfile()
    {
        return $this->belongsTo(SpeedProfile::class);
    }

    /**
     * Get speed formatted
     */
    public function getDownloadSpeedFormattedAttribute()
    {
        if ($this->download_speed >= 1000000) {
            return number_format($this->download_speed / 1000000, 2) . ' Gbps';
        } elseif ($this->download_speed >= 1000) {
            return number_format($this->download_speed / 1000, 2) . ' Mbps';
        }
        return $this->download_speed . ' Kbps';
    }

    /**
     * Get upload speed formatted
     */
    public function getUploadSpeedFormattedAttribute()
    {
        if ($this->upload_speed >= 1000000) {
            return number_format($this->upload_speed / 1000000, 2) . ' Gbps';
        } elseif ($this->upload_speed >= 1000) {
            return number_format($this->upload_speed / 1000, 2) . ' Mbps';
        }
        return $this->upload_speed . ' Kbps';
    }
}
