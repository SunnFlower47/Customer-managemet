<?php

namespace App\Services;

use App\Models\Olt;
use App\Models\OltSyncLog;
use App\Models\Onu;
use App\Models\OltPonPort;
use App\Services\OltDriverFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OltSyncService
{
    protected $monitoringService;

    public function __construct(OltMonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Sync OLT data (full sync)
     */
    public function sync(Olt $olt, string $syncType = 'full'): OltSyncLog
    {
        $syncLog = OltSyncLog::create([
            'olt_id' => $olt->id,
            'sync_type' => $syncType,
            'status' => 'running',
            'progress_percentage' => 0,
            'started_at' => now(),
        ]);

        try {
            $driver = OltDriverFactory::create($olt);
            
            // Test connection first
            $connectionTest = $driver->testConnection();
            if (!$connectionTest['success']) {
                throw new \Exception($connectionTest['message']);
            }

            // Get PON ports
            $syncLog->update(['progress_percentage' => 10]);
            $ponPorts = $driver->getPonPorts();
            $totalItems = count($ponPorts);
            $syncLog->update(['total_items' => $totalItems]);

            $newOnus = 0;
            $updatedOnus = 0;
            $processedItems = 0;

            // Sync each PON port
            foreach ($ponPorts as $portData) {
                $processedItems++;
                $progress = (int) (($processedItems / max($totalItems, 1)) * 80) + 10;
                $syncLog->update(['progress_percentage' => $progress, 'processed_items' => $processedItems]);

                // Get ONU list for this port
                $onuList = $driver->getOnuList($portData['card'] ?? 1, $portData['port'] ?? 1);
                
                // Sync ONUs
                foreach ($onuList as $onuData) {
                    $result = $this->syncOnu($olt, $portData, $onuData);
                    if ($result['new']) {
                        $newOnus++;
                    } else {
                        $updatedOnus++;
                    }
                }
            }

            // Update sync log
            $syncLog->update([
                'status' => 'completed',
                'progress_percentage' => 100,
                'processed_items' => $processedItems,
                'new_onus' => $newOnus,
                'updated_onus' => $updatedOnus,
                'completed_at' => now(),
            ]);

            // Update OLT last synced
            $olt->update(['last_checked_at' => now()]);

            return $syncLog;
        } catch (\Exception $e) {
            Log::error("OLT Sync Error [{$olt->kode_olt}]: " . $e->getMessage());
            
            $syncLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'errors' => $syncLog->errors + 1,
                'completed_at' => now(),
            ]);

            return $syncLog;
        }
    }

    /**
     * Sync single ONU
     */
    protected function syncOnu(Olt $olt, array $portData, array $onuData): array
    {
        $isNew = false;

        // Find or create PON port
        $ponPort = OltPonPort::firstOrCreate(
            [
                'olt_id' => $olt->id,
                'card' => $portData['card'] ?? 1,
                'port' => $portData['port'] ?? 1,
            ],
            [
                'port_name' => $portData['port_name'] ?? null,
                'status' => $portData['status'] ?? 'unknown',
                'onu_count' => 0,
                'last_synced_at' => now(),
            ]
        );

        // Find or create ONU
        $onu = Onu::firstOrNew([
            'olt_id' => $olt->id,
            'serial_number' => $onuData['serial_number'] ?? null,
        ]);

        if (!$onu->exists) {
            $isNew = true;
        }

        // Update ONU data
        $onu->fill([
            'olt_pon_port_id' => $ponPort->id,
            'mac_address' => $onuData['mac_address'] ?? null,
            'nama' => $onuData['nama'] ?? $onu->nama,
            'ont_type' => $onuData['ont_type'] ?? $onu->ont_type,
            'vendor' => $onuData['vendor'] ?? $olt->vendor,
            'model' => $onuData['model'] ?? $onu->model,
            'status' => $onuData['status'] ?? 'unknown',
            'card' => $portData['card'] ?? 1,
            'port' => $portData['port'] ?? 1,
            'onu_id' => $onuData['onu_id'] ?? null,
            'rx_power' => $onuData['rx_power'] ?? null,
            'olt_rx_power' => $onuData['olt_rx_power'] ?? null,
            'tx_power' => $onuData['tx_power'] ?? null,
            'ip_address' => $onuData['ip_address'] ?? null,
            'uptime_seconds' => $onuData['uptime_seconds'] ?? 0,
            'last_online_at' => ($onuData['status'] ?? 'unknown') === 'online' ? now() : $onu->last_online_at,
            'last_synced_at' => now(),
            'olt_config' => $onuData['config'] ?? null,
        ]);

        $onu->save();

        return ['new' => $isNew, 'onu' => $onu];
    }

    /**
     * Get sync progress
     */
    public function getSyncProgress(int $syncLogId): ?OltSyncLog
    {
        return OltSyncLog::find($syncLogId);
    }
}

