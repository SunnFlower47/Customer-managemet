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

        // Initialize event service outside try-catch so it's available in both blocks
        $oltEventService = new \App\Services\OltEventService();
        
        // Log sync started event
        $oltEventService->logEvent($olt, 'sync_started', 'info', null, 'Sinkronisasi OLT dimulai');

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

            // Log sync completed event
            $oltEventService->logEvent($olt, 'sync_completed', 'info', [
                'new_onus' => $newOnus,
                'updated_onus' => $updatedOnus,
            ], "Sinkronisasi selesai: {$newOnus} ONU baru, {$updatedOnus} ONU di-update");

            return $syncLog;
        } catch (\Exception $e) {
            Log::error("OLT Sync Error [{$olt->kode_olt}]: " . $e->getMessage());
            
            $syncLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'errors' => $syncLog->errors + 1,
                'completed_at' => now(),
            ]);

            // Log sync failed event (using the same $oltEventService instance)
            $oltEventService->logEvent($olt, 'sync_failed', 'critical', [
                'error' => $e->getMessage(),
            ], "Sinkronisasi gagal: " . $e->getMessage());

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

        // Check if ONU is unmapped to pelanggan
        $unmapped = empty($onu->pelanggan_id);

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
            'unmapped_to_pelanggan' => $unmapped, // Flag ONU belum dimapping
        ]);

        $onu->save();

        // Sync service configuration from OLT if ONU exists and has config
        if ($onu->exists && !empty($onuData['config'])) {
            $this->syncOnuServices($onu, $onuData['config']);
        }

        return ['new' => $isNew, 'onu' => $onu];
    }

    /**
     * Sync ONU service configuration from OLT
     */
    protected function syncOnuServices(Onu $onu, array $config): void
    {
        try {
            // Only sync if ONU doesn't have services yet (to avoid overwriting manual config)
            if ($onu->services()->count() > 0) {
                return;
            }

            // Extract service configuration from OLT config
            if (isset($config['services']) && is_array($config['services'])) {
                foreach ($config['services'] as $serviceId => $serviceConfig) {
                    \App\Models\OnuService::create([
                        'onu_id' => $onu->id,
                        'service_id' => $serviceId,
                        'wan_mode' => $serviceConfig['wan_mode'] ?? 'pppoe',
                        'vlan_id' => $serviceConfig['vlan_id'] ?? null,
                        'pppoe_username' => $serviceConfig['pppoe_username'] ?? null,
                        'pppoe_password' => $serviceConfig['pppoe_password'] ?? null,
                        'static_ip' => $serviceConfig['static_ip'] ?? null,
                        'static_gateway' => $serviceConfig['static_gateway'] ?? null,
                        'static_subnet' => $serviceConfig['static_subnet'] ?? null,
                        'download_speed' => $serviceConfig['download_speed'] ?? null,
                        'upload_speed' => $serviceConfig['upload_speed'] ?? null,
                        'wifi_config' => $config['wifi'] ?? $serviceConfig['wifi'] ?? null, // WiFi dari root config atau service config
                        'lan_port_config' => $config['lan_ports'] ?? $serviceConfig['lan_ports'] ?? null, // LAN ports dari root config atau service config
                        'is_active' => $serviceConfig['is_active'] ?? true,
                    ]);
                }
            } elseif (!empty($config['lan_ports']) || !empty($config['wifi'])) {
                // Jika tidak ada service tapi ada LAN/WiFi config, buat service default
                \App\Models\OnuService::create([
                    'onu_id' => $onu->id,
                    'service_id' => 1,
                    'wan_mode' => 'pppoe',
                    'vlan_id' => $config['services'][1]['vlan_id'] ?? null,
                    'wifi_config' => $config['wifi'] ?? null,
                    'lan_port_config' => $config['lan_ports'] ?? null,
                    'is_active' => true,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to sync ONU services", [
                'onu_id' => $onu->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get sync progress
     */
    public function getSyncProgress(int $syncLogId): ?OltSyncLog
    {
        return OltSyncLog::find($syncLogId);
    }
}

