<?php

namespace App\Services;

use App\Models\Onu;
use App\Models\Olt;
use App\Services\OltDriverFactory;
use Illuminate\Support\Facades\Log;

class OnuManagementService
{
    /**
     * Reboot ONU
     */
    public function rebootOnu(Onu $onu): array
    {
        try {
            $olt = $onu->olt;
            $driver = OltDriverFactory::create($olt);
            
            $result = $driver->rebootOnu($onu->serial_number);
            
            if ($result['success']) {
                $onu->update([
                    'last_synced_at' => now(),
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error("Reboot ONU Error [{$onu->serial_number}]: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Reset ONU
     */
    public function resetOnu(Onu $onu): array
    {
        try {
            $olt = $onu->olt;
            $driver = OltDriverFactory::create($olt);
            
            $result = $driver->resetOnu($onu->serial_number);
            
            if ($result['success']) {
                $onu->update([
                    'last_synced_at' => now(),
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error("Reset ONU Error [{$onu->serial_number}]: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Disable ONU
     */
    public function disableOnu(Onu $onu): array
    {
        try {
            $olt = $onu->olt;
            $driver = OltDriverFactory::create($olt);
            
            $result = $driver->disableOnu($onu->serial_number);
            
            if ($result['success']) {
                $onu->update([
                    'is_active' => false,
                    'last_synced_at' => now(),
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error("Disable ONU Error [{$onu->serial_number}]: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Enable ONU
     */
    public function enableOnu(Onu $onu): array
    {
        try {
            $olt = $onu->olt;
            $driver = OltDriverFactory::create($olt);
            
            $result = $driver->enableOnu($onu->serial_number);
            
            if ($result['success']) {
                $onu->update([
                    'is_active' => true,
                    'last_synced_at' => now(),
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error("Enable ONU Error [{$onu->serial_number}]: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get ONU details from OLT
     */
    public function getOnuDetails(Onu $onu): array
    {
        try {
            $olt = $onu->olt;
            $driver = OltDriverFactory::create($olt);
            
            $details = $driver->getOnuDetails($onu->serial_number);
            
            // Update ONU with latest data
            if (!empty($details)) {
                $onu->update([
                    'status' => $details['status'] ?? $onu->status,
                    'rx_power' => $details['rx_power'] ?? $onu->rx_power,
                    'olt_rx_power' => $details['olt_rx_power'] ?? $onu->olt_rx_power,
                    'tx_power' => $details['tx_power'] ?? $onu->tx_power,
                    'ip_address' => $details['ip_address'] ?? $onu->ip_address,
                    'uptime_seconds' => $details['uptime_seconds'] ?? $onu->uptime_seconds,
                    'last_synced_at' => now(),
                ]);
            }
            
            return $details;
        } catch (\Exception $e) {
            Log::error("Get ONU Details Error [{$onu->serial_number}]: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Register new ONU manually
     */
    public function registerOnu(array $data): Onu
    {
        $onu = Onu::create([
            'olt_id' => $data['olt_id'],
            'olt_pon_port_id' => $data['olt_pon_port_id'] ?? null,
            'pelanggan_id' => $data['pelanggan_id'] ?? null,
            'odp_id' => $data['odp_id'] ?? null,
            'serial_number' => $data['serial_number'],
            'mac_address' => $data['mac_address'] ?? null,
            'nama' => $data['nama'] ?? null,
            'description' => $data['description'] ?? null,
            'ont_type' => $data['ont_type'] ?? null,
            'vendor' => $data['vendor'] ?? null,
            'model' => $data['model'] ?? null,
            'card' => $data['card'] ?? null,
            'port' => $data['port'] ?? null,
            'is_registered' => true,
            'is_active' => true,
        ]);

        return $onu;
    }
}

