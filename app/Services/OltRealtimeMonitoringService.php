<?php

namespace App\Services;

use App\Models\Olt;
use App\Models\Onu;
use App\Services\OltDriverFactory;
use App\Services\OltEventService;
use App\Services\OnuEventService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OltRealtimeMonitoringService
{
    protected $oltEventService;
    protected $onuEventService;
    protected $pollingInterval; // in seconds

    public function __construct(OltEventService $oltEventService, OnuEventService $onuEventService)
    {
        $this->oltEventService = $oltEventService;
        $this->onuEventService = $onuEventService;
        $this->pollingInterval = config('olt.realtime_polling_interval', 30); // Default 30 seconds
    }

    /**
     * Monitor all OLTs (realtime monitoring)
     */
    public function monitorAllOlts(): void
    {
        $olts = Olt::active()->get();

        foreach ($olts as $olt) {
            try {
                $this->monitorOlt($olt);
            } catch (\Exception $e) {
                Log::error("Realtime monitoring error for OLT {$olt->kode_olt}: " . $e->getMessage());
            }
        }
    }

    /**
     * Monitor single OLT
     */
    public function monitorOlt(Olt $olt): void
    {
        try {
            $driver = OltDriverFactory::create($olt);

            // Monitor hardware (temperature, FAN, power supply)
            $this->monitorHardware($olt, $driver);

            // Monitor ONUs
            $this->monitorOnus($olt, $driver);

            // Update last checked
            $olt->update(['last_checked_at' => now()]);

        } catch (\Exception $e) {
            Log::error("Realtime monitoring error for OLT {$olt->kode_olt}: " . $e->getMessage());

            // Log connection lost event
            $this->oltEventService->logEvent(
                $olt,
                'connection_lost',
                'warning',
                ['error' => $e->getMessage()],
                "Koneksi ke OLT hilang: " . $e->getMessage()
            );
        }
    }

    /**
     * Monitor OLT hardware (temperature, FAN, power supply)
     */
    protected function monitorHardware(Olt $olt, $driver): void
    {
        try {
            // Get temperature
            $temperature = $driver->getOltTemperature();
            if ($temperature > 0) {
                $olt->update([
                    'temperature' => $temperature,
                    'last_temperature_check' => now(),
                ]);

                // Log temperature event if threshold exceeded
                $this->oltEventService->logTemperatureEvent($olt, $temperature);
            }

            // Get FAN speed
            $fanSpeed = $driver->getFanSpeed();
            if (!empty($fanSpeed)) {
                $olt->update([
                    'fan_speed' => $fanSpeed,
                    'last_fan_check' => now(),
                ]);

                // Check for slow/failed fans
                foreach ($fanSpeed as $fanName => $speed) {
                    if ($speed < 1000) {
                        $this->oltEventService->logFanEvent($olt, $fanSpeed, 'fan_failure');
                    } elseif ($speed < 2000) {
                        $this->oltEventService->logFanEvent($olt, $fanSpeed, 'fan_slow');
                    }
                }
            }

            // Get power supply status
            $powerStatus = $driver->getPowerSupplyStatus();
            if (!empty($powerStatus)) {
                $olt->update([
                    'power_supply_status' => $powerStatus['status'] ?? 'unknown',
                    'last_power_check' => now(),
                ]);

                // Log power supply event if not normal
                if (($powerStatus['status'] ?? 'unknown') !== 'normal') {
                    $this->oltEventService->logPowerSupplyEvent(
                        $olt,
                        $powerStatus['status'],
                        $powerStatus['status'] === 'critical' ? 'power_supply_failure' : 'power_supply_warning'
                    );
                }
            }

        } catch (\Exception $e) {
            Log::error("Hardware monitoring error for OLT {$olt->kode_olt}: " . $e->getMessage());
        }
    }

    /**
     * Monitor ONUs (RX power, status, events)
     */
    protected function monitorOnus(Olt $olt, $driver): void
    {
        try {
            $onus = Onu::where('olt_id', $olt->id)->get();

            foreach ($onus as $onu) {
                try {
                    $this->monitorOnu($onu, $driver);
                } catch (\Exception $e) {
                    Log::warning("Error monitoring ONU {$onu->serial_number}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("ONU monitoring error for OLT {$olt->kode_olt}: " . $e->getMessage());
        }
    }

    /**
     * Monitor single ONU
     */
    protected function monitorOnu(Onu $onu, $driver): void
    {
        try {
            $details = $driver->getOnuDetails($onu->serial_number);

            if (empty($details)) {
                return;
            }

            $oldStatus = $onu->status;
            $oldRxPower = $onu->rx_power;

            // Update ONU data
            $onu->fill([
                'status' => $details['status'] ?? $onu->status,
                'rx_power' => $details['rx_power'] ?? $onu->rx_power,
                'tx_power' => $details['tx_power'] ?? $onu->tx_power,
                'last_online_at' => ($details['status'] ?? 'unknown') === 'online' ? now() : $onu->last_online_at,
            ]);

            // Log status change
            if ($oldStatus !== $onu->status) {
                $this->onuEventService->logStatusChange($onu, $oldStatus, $onu->status);

                // Log LOS or Dying Gasp
                if ($onu->status === 'los') {
                    $this->onuEventService->logLos($onu);
                } elseif ($onu->status === 'dying_gasp') {
                    $this->onuEventService->logDyingGasp($onu);
                }
            }

            // Log RX power change (if significant)
            if ($oldRxPower !== null && $onu->rx_power !== null) {
                $powerDiff = abs($oldRxPower - $onu->rx_power);
                if ($powerDiff >= 1.0) { // Significant change (1 dBm or more)
                    $this->onuEventService->logRxPowerChange($onu, $oldRxPower, $onu->rx_power);
                }
            }

            $onu->save();

        } catch (\Exception $e) {
            Log::warning("Error monitoring ONU {$onu->serial_number}: " . $e->getMessage());
        }
    }

    /**
     * Get polling interval
     */
    public function getPollingInterval(): int
    {
        return $this->pollingInterval;
    }

    /**
     * Set polling interval
     */
    public function setPollingInterval(int $seconds): void
    {
        $this->pollingInterval = $seconds;
    }
}

