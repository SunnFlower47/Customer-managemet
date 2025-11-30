<?php

namespace App\Services;

use App\Models\Olt;
use App\Models\OltEvent;
use Illuminate\Support\Facades\Log;

class OltEventService
{
    /**
     * Log OLT event
     */
    public function logEvent(
        Olt $olt,
        string $eventType,
        string $severity = 'info',
        ?array $eventData = null,
        ?string $message = null
    ): OltEvent {
        return OltEvent::create([
            'olt_id' => $olt->id,
            'event_type' => $eventType,
            'severity' => $severity,
            'event_data' => $eventData,
            'message' => $message ?? $this->getDefaultMessage($eventType, $eventData),
        ]);
    }

    /**
     * Log temperature event
     */
    public function logTemperatureEvent(Olt $olt, float $temperature, float $threshold = 70.0): ?OltEvent
    {
        if ($temperature >= $threshold) {
            $severity = $temperature >= 85.0 ? 'critical' : 'warning';
            $eventType = $temperature >= 85.0 ? 'temperature_critical' : 'temperature_high';

            return $this->logEvent(
                $olt,
                $eventType,
                $severity,
                ['temperature' => $temperature, 'threshold' => $threshold],
                "OLT temperature tinggi: {$temperature}°C (threshold: {$threshold}°C)"
            );
        }

        return null;
    }

    /**
     * Log fan event
     */
    public function logFanEvent(Olt $olt, array $fanSpeed, string $eventType = 'fan_slow'): OltEvent
    {
        $severity = $eventType === 'fan_failure' ? 'critical' : 'warning';

        return $this->logEvent(
            $olt,
            $eventType,
            $severity,
            ['fan_speed' => $fanSpeed],
            "Fan OLT: " . ($eventType === 'fan_failure' ? 'Gagal' : 'Lambat')
        );
    }

    /**
     * Log power supply event
     */
    public function logPowerSupplyEvent(Olt $olt, string $status, string $eventType = 'power_supply_warning'): OltEvent
    {
        $severity = $status === 'critical' ? 'critical' : 'warning';
        $eventType = $status === 'critical' ? 'power_supply_failure' : 'power_supply_warning';

        return $this->logEvent(
            $olt,
            $eventType,
            $severity,
            ['power_supply_status' => $status],
            "Power supply OLT: " . ucfirst($status)
        );
    }

    /**
     * Resolve event
     */
    public function resolveEvent(OltEvent $event): bool
    {
        return $event->update(['resolved_at' => now()]);
    }

    /**
     * Get default message for event type
     */
    protected function getDefaultMessage(string $eventType, ?array $eventData = null): string
    {
        $messages = [
            'los' => 'Loss of Signal detected',
            'dying_gasp' => 'Dying Gasp detected',
            'temperature_high' => 'Temperature tinggi',
            'temperature_critical' => 'Temperature kritis',
            'fan_failure' => 'Fan failure',
            'fan_slow' => 'Fan speed lambat',
            'power_supply_failure' => 'Power supply failure',
            'power_supply_warning' => 'Power supply warning',
            'connection_lost' => 'Koneksi ke OLT hilang',
            'connection_restored' => 'Koneksi ke OLT pulih',
            'sync_started' => 'Sinkronisasi dimulai',
            'sync_completed' => 'Sinkronisasi selesai',
            'sync_failed' => 'Sinkronisasi gagal',
        ];

        return $messages[$eventType] ?? "Event: {$eventType}";
    }
}

