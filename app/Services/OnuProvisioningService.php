<?php

namespace App\Services;

use App\Models\Olt;
use App\Models\OltPonPort;
use App\Models\Onu;
use App\Models\OnuService;
use App\Models\SpeedProfile;
use App\Services\OltDriverFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Throwable;

class OnuProvisioningService
{
    /**
     * Get list of unconfigured ONUs per OLT.
     *
     * @param Olt|null $filterOlt
     * @return array
     */
    public function getUnconfiguredOnus(?Olt $filterOlt = null): array
    {
        $olts = $filterOlt ? collect([$filterOlt]) : Olt::active()->get();
        $result = [];

        foreach ($olts as $olt) {
            try {
                $driver = OltDriverFactory::create($olt);
                $items = $driver->discoverUnconfiguredOnus();
                foreach ($items as $item) {
                    $result[] = array_merge([
                        'olt_id' => $olt->id,
                        'olt_name' => $olt->nama,
                    ], $item);
                }
            } catch (Throwable $e) {
                Log::warning('Failed to fetch unconfigured ONUs', [
                    'olt_id' => $olt->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    /**
     * Register/provision ONU on OLT and persist into database.
     */
    public function registerOnu(array $data): array
    {
        $olt = Olt::findOrFail($data['olt_id']);

        try {
            $driver = OltDriverFactory::create($olt);
            $driverResponse = $driver->provisionOnu($data);

            if (isset($driverResponse['success']) && $driverResponse['success'] === false) {
                return [
                    'success' => false,
                    'message' => $driverResponse['message'] ?? 'Registrasi ONU gagal di perangkat.',
                ];
            }
        } catch (Throwable $e) {
            Log::error('Failed provisioning ONU on device', [
                'olt_id' => $olt->id,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengirim perintah ke OLT: ' . $e->getMessage(),
            ];
        }

        $ponPort = $this->getOrCreatePonPort($olt, $data['card'] ?? null, $data['port'] ?? null);

        $onu = Onu::create([
            'olt_id' => $olt->id,
            'olt_pon_port_id' => $ponPort?->id,
            'pelanggan_id' => $data['pelanggan_id'] ?? null,
            'odp_id' => $data['odp_id'] ?? null,
            'serial_number' => $data['serial_number'],
            'mac_address' => $data['mac_address'] ?? null,
            'nama' => $data['nama'] ?? null,
            'description' => $data['description'] ?? null,
            'ont_type' => $data['ont_type'] ?? null,
            'vendor' => $data['vendor'] ?? $olt->vendor,
            'model' => $data['model'] ?? null,
            'status' => 'offline',
            'is_active' => true,
            'is_registered' => true,
            'card' => $data['card'] ?? null,
            'port' => $data['port'] ?? null,
            'onu_id' => $driverResponse['onu_id'] ?? null,
        ]);

        $this->createPrimaryService($onu, $data);

        return [
            'success' => true,
            'message' => $driverResponse['message'] ?? 'ONU berhasil diregistrasi.',
            'onu' => $onu->fresh(),
        ];
    }

    protected function getOrCreatePonPort(Olt $olt, ?int $card, ?int $port): ?OltPonPort
    {
        if (!$card || !$port) {
            return null;
        }

        return OltPonPort::firstOrCreate(
            ['olt_id' => $olt->id, 'card' => $card, 'port' => $port],
            [
                'status' => 'unknown',
                'onu_count' => 0,
                'last_synced_at' => now(),
            ]
        );
    }

    protected function createPrimaryService(Onu $onu, array $data): void
    {
        // Use vlan_id_manual if provided, otherwise use vlan_id from select
        $vlanId = !empty($data['vlan_id_manual']) ? $data['vlan_id_manual'] : ($data['vlan_id'] ?? null);
        
        if (!$vlanId && !$data['pppoe_username'] && !$data['wan_mode']) {
            return;
        }

        $speedProfile = isset($data['speed_profile_id']) ? SpeedProfile::find($data['speed_profile_id']) : null;

        OnuService::create([
            'onu_id' => $onu->id,
            'service_id' => 1,
            'wan_mode' => $data['wan_mode'] ?? 'pppoe',
            'pppoe_username' => $data['pppoe_username'] ?? null,
            'pppoe_password' => $data['pppoe_password'] ?? null,
            'static_ip' => $data['static_ip'] ?? null,
            'static_gateway' => $data['static_gateway'] ?? null,
            'static_subnet' => $data['static_subnet'] ?? null,
            'static_dns1' => $data['static_dns1'] ?? null,
            'static_dns2' => $data['static_dns2'] ?? null,
            'vlan_id' => $vlanId,
            'vlan_tagged' => $data['vlan_tagged'] ?? true,
            'speed_profile_id' => $data['speed_profile_id'] ?? null,
            'download_speed' => $speedProfile?->download_speed ?? null,
            'upload_speed' => $speedProfile?->upload_speed ?? null,
            'is_active' => true,
        ]);
    }
}

