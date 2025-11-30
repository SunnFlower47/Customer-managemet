<?php

namespace App\Services;

use App\Models\Onu;
use App\Models\OnuEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OnuEventService
{
    /**
     * Log ONU event
     */
    public function logEvent(
        Onu $onu,
        string $eventType,
        string $severity = 'info',
        ?string $oldValue = null,
        ?string $newValue = null,
        ?array $eventData = null,
        ?string $message = null,
        ?int $userId = null
    ): OnuEvent {
        // Update ONU last event
        $onu->update([
            'last_event_at' => now(),
            'last_event_type' => $eventType,
        ]);

        return OnuEvent::create([
            'onu_id' => $onu->id,
            'event_type' => $eventType,
            'severity' => $severity,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'event_data' => $eventData,
            'message' => $message ?? $this->getDefaultMessage($eventType, $oldValue, $newValue),
            'user_id' => $userId ?? Auth::id(),
        ]);
    }

    /**
     * Log LOS event
     */
    public function logLos(Onu $onu): OnuEvent
    {
        return $this->logEvent(
            $onu,
            'los',
            'critical',
            $onu->status,
            'los',
            null,
            "Loss of Signal detected untuk ONU {$onu->serial_number}"
        );
    }

    /**
     * Log Dying Gasp event
     */
    public function logDyingGasp(Onu $onu): OnuEvent
    {
        return $this->logEvent(
            $onu,
            'dying_gasp',
            'critical',
            $onu->status,
            'dying_gasp',
            null,
            "Dying Gasp detected untuk ONU {$onu->serial_number}"
        );
    }

    /**
     * Log status change
     */
    public function logStatusChange(Onu $onu, string $oldStatus, string $newStatus): OnuEvent
    {
        $severity = in_array($newStatus, ['los', 'dying_gasp']) ? 'critical' : 'info';

        return $this->logEvent(
            $onu,
            'status_change',
            $severity,
            $oldStatus,
            $newStatus,
            null,
            "Status ONU berubah dari {$oldStatus} ke {$newStatus}"
        );
    }

    /**
     * Log RX power change
     */
    public function logRxPowerChange(Onu $onu, ?float $oldPower, ?float $newPower): OnuEvent
    {
        $severity = 'info';
        if ($newPower !== null) {
            if ($newPower < -27) {
                $severity = 'critical';
            } elseif ($newPower < -25) {
                $severity = 'warning';
            }
        }

        return $this->logEvent(
            $onu,
            'rx_power_change',
            $severity,
            $oldPower !== null ? (string) $oldPower : null,
            $newPower !== null ? (string) $newPower : null,
            ['old_rx_power' => $oldPower, 'new_rx_power' => $newPower],
            "RX Power berubah dari " . ($oldPower ?? 'N/A') . " dBm ke " . ($newPower ?? 'N/A') . " dBm"
        );
    }

    /**
     * Log user action (reboot, reset, disable, etc)
     */
    public function logUserAction(Onu $onu, string $action, ?string $message = null): OnuEvent
    {
        $eventType = match($action) {
            'reboot' => 'reboot',
            'reset' => 'reset',
            'disable' => 'disabled',
            'enable' => 'enabled',
            default => 'config_changed',
        };

        return $this->logEvent(
            $onu,
            $eventType,
            'info',
            null,
            null,
            ['action' => $action],
            $message ?? "User melakukan {$action} pada ONU {$onu->serial_number}",
            Auth::id()
        );
    }

    /**
     * Resolve event
     */
    public function resolveEvent(OnuEvent $event): bool
    {
        return $event->update(['resolved_at' => now()]);
    }

    /**
     * Get default message for event type
     */
    protected function getDefaultMessage(string $eventType, ?string $oldValue = null, ?string $newValue = null): string
    {
        $messages = [
            'los' => 'Loss of Signal detected',
            'dying_gasp' => 'Dying Gasp detected',
            'status_change' => "Status berubah dari {$oldValue} ke {$newValue}",
            'rx_power_change' => "RX Power berubah dari {$oldValue} dBm ke {$newValue} dBm",
            'tx_power_change' => "TX Power berubah dari {$oldValue} dBm ke {$newValue} dBm",
            'online' => 'ONU online',
            'offline' => 'ONU offline',
            'reboot' => 'ONU di-reboot',
            'reset' => 'ONU di-reset',
            'disabled' => 'ONU di-disable',
            'enabled' => 'ONU di-enable',
            'config_changed' => 'Konfigurasi ONU berubah',
            'vlan_changed' => 'VLAN ONU berubah',
            'serial_changed' => 'Serial Number ONU berubah',
        ];

        return $messages[$eventType] ?? "Event: {$eventType}";
    }
}

