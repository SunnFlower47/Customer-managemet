<?php

namespace App\Services;

use App\Models\Olt;
use App\Services\OltDriverFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OltMonitoringService
{
    /**
     * Monitor single OLT
     */
    public function monitor(Olt $olt, bool $updateDatabase = true): array
    {
        try {
            $driver = OltDriverFactory::create($olt);
            
            // Test connection
            $connectionTest = $driver->testConnection();
            
            if (!$connectionTest['success']) {
                if ($updateDatabase) {
                    $olt->update([
                        'status' => 'offline',
                        'last_error' => $connectionTest['message'],
                        'last_checked_at' => now(),
                    ]);
                }
                
                return $connectionTest;
            }

            // Get system info
            $systemInfo = $driver->getSystemInfo();
            
            // Get statistics
            $statistics = $driver->getStatistics();
            
            // Get PON ports
            $ponPorts = $driver->getPonPorts();
            
            // Get bandwidth usage
            $bandwidth = $driver->getBandwidthUsage();
            
            // Get alarms
            $alarms = $driver->getAlarms();
            
            // Calculate totals
            $totalOnu = 0;
            $portsUsed = 0;
            foreach ($ponPorts as $port) {
                if (($port['status'] ?? 'unknown') === 'up') {
                    $portsUsed++;
                }
                $totalOnu += $port['onu_count'] ?? 0;
            }

            if ($updateDatabase) {
                // Update OLT
                $olt->update([
                    'status' => 'online',
                    'onu_terhubung' => $totalOnu,
                    'ports_terpakai' => $portsUsed,
                    'last_checked_at' => now(),
                    'last_error' => null,
                ]);

                // Sync PON ports
                $this->syncPonPorts($olt, $ponPorts);
            }

            $monitoringData = [
                'success' => true,
                'system_info' => $systemInfo,
                'statistics' => $statistics,
                'pon_ports' => $ponPorts,
                'bandwidth' => $bandwidth,
                'alarms' => $alarms,
                'total_onu' => $totalOnu,
                'ports_used' => $portsUsed,
                'total_ports' => $olt->total_ports,
            ];

            // Cache data untuk quick access
            Cache::put("olt_data_{$olt->id}", $monitoringData, now()->addSeconds(config('olt.monitoring.cache_duration', 30)));

            return $monitoringData;
        } catch (\Exception $e) {
            Log::error("OLT Monitoring Error [{$olt->kode_olt}]: " . $e->getMessage());
            
            if ($updateDatabase) {
                $olt->update([
                    'status' => 'error',
                    'last_error' => $e->getMessage(),
                    'last_checked_at' => now(),
                ]);
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sync PON ports to database
     */
    protected function syncPonPorts(Olt $olt, array $ponPorts): void
    {
        foreach ($ponPorts as $portData) {
            \App\Models\OltPonPort::updateOrCreate(
                [
                    'olt_id' => $olt->id,
                    'card' => $portData['card'] ?? 1,
                    'port' => $portData['port'] ?? 1,
                ],
                [
                    'port_name' => $portData['port_name'] ?? null,
                    'status' => $portData['status'] ?? 'unknown',
                    'onu_count' => $portData['onu_count'] ?? 0,
                    'rx_power' => $portData['rx_power'] ?? null,
                    'tx_power' => $portData['tx_power'] ?? null,
                    'description' => $portData['description'] ?? null,
                    'last_synced_at' => now(),
                ]
            );
        }
    }

    /**
     * Monitor all OLTs
     */
    public function monitorAll(): void
    {
        $olts = Olt::active()->get();
        
        foreach ($olts as $olt) {
            $this->monitor($olt, true);
            
            // Small delay untuk avoid overwhelming OLT devices
            usleep(500000); // 0.5 second
        }
    }

    /**
     * Get cached monitoring data
     */
    public function getCachedData(Olt $olt): ?array
    {
        return Cache::get("olt_data_{$olt->id}");
    }
}

