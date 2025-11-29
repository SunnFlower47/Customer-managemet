<?php

namespace App\Contracts;

use App\Models\Onu;
use App\Models\OnuService;

interface OltDriverInterface
{
    /**
     * Test connection to OLT
     */
    public function testConnection(): array;

    /**
     * Get OLT system information
     */
    public function getSystemInfo(): array;

    /**
     * Get all PON ports status
     */
    public function getPonPorts(): array;

    /**
     * Get ONU list connected to specific PON port
     */
    public function getOnuList(int $card, int $port): array;

    /**
     * Get ONU details by serial number
     */
    public function getOnuDetails(string $serialNumber): array;

    /**
     * Get OLT statistics (uptime, CPU, memory, etc)
     */
    public function getStatistics(): array;

    /**
     * Get bandwidth usage per port
     */
    public function getBandwidthUsage(?int $card = null, ?int $port = null): array;

    /**
     * Get alarms/errors
     */
    public function getAlarms(): array;

    /**
     * Reboot ONU
     */
    public function rebootOnu(string $serialNumber): array;

    /**
     * Reset ONU
     */
    public function resetOnu(string $serialNumber): array;

    /**
     * Disable ONU
     */
    public function disableOnu(string $serialNumber): array;

    /**
     * Enable ONU
     */
    public function enableOnu(string $serialNumber): array;

    /**
     * Get ONU configuration from OLT
     */
    public function getOnuConfig(string $serialNumber): array;

    /**
     * Configure ONU service (VLAN, WiFi, LAN port, remote access)
     */
    public function configureService(Onu $onu, OnuService $service, array $payload = []): array;
}

