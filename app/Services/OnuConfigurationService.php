<?php

namespace App\Services;

use App\Models\OnuService;
use App\Services\OltDriverFactory;
use Illuminate\Support\Facades\Log;

class OnuConfigurationService
{
    public function applyConfiguration(OnuService $service): array
    {
        $service->loadMissing(['onu.olt']);
        $onu = $service->onu;

        if (!$onu || !$onu->olt) {
            return [
                'success' => false,
                'message' => 'Data OLT tidak ditemukan untuk ONU ini.',
            ];
        }

        try {
            $driver = OltDriverFactory::create($onu->olt);

            $payload = [
                'wan_mode' => $service->wan_mode,
                'vlan_id' => $service->vlan_id,
                'speed_profile_id' => $service->speed_profile_id,
                'download_speed' => $service->download_speed,
                'upload_speed' => $service->upload_speed,
                'pppoe_username' => $service->pppoe_username,
                'pppoe_password' => $service->pppoe_password,
                'static_ip' => $service->static_ip,
                'static_gateway' => $service->static_gateway,
                'static_subnet' => $service->static_subnet,
                'static_dns1' => $service->static_dns1,
                'static_dns2' => $service->static_dns2,
                'wifi' => $service->wifi_config,
                'lan_ports' => $service->lan_port_config,
                'remote_access_rules' => $service->remote_access_rules,
            ];

            $result = $driver->configureService($onu, $service, $payload);

            if (!$result['success']) {
                Log::warning('Gagal menerapkan konfigurasi ONU', [
                    'onu_id' => $onu->id,
                    'service_id' => $service->id,
                    'payload' => $payload,
                    'driver_message' => $result['message'] ?? null,
                ]);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('Exception saat konfigurasi ONU', [
                'onu_id' => $onu->id,
                'service_id' => $service->id,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menerapkan konfigurasi: ' . $e->getMessage(),
            ];
        }
    }
}

