<?php

namespace App\Services;

use App\Models\Olt;
use App\Contracts\OltDriverInterface;
use App\Drivers\ZteC300Driver;
use App\Drivers\ZteC320Driver;
use App\Drivers\GenericSnmpDriver;
use App\Drivers\MockOltDriver;
use Exception;

class OltDriverFactory
{
    /**
     * Create appropriate driver for OLT
     */
    public static function create(Olt $olt): OltDriverInterface
    {
        // Mock mode untuk testing - HANYA jika diaktifkan di config
        $mockMode = config('olt.mock_mode', false);
        $mockIps = config('olt.mock_ip_addresses', ['127.0.0.1', 'localhost']);

        // Gunakan mock driver jika:
        // 1. Mock mode aktif di config, DAN
        // 2. IP address termasuk dalam daftar mock IP
        if ($mockMode && in_array(strtolower($olt->ip_address), array_map('strtolower', $mockIps))) {
            return new MockOltDriver($olt);
        }

        // Di production, jika mock mode mati tapi ada IP mock, throw exception
        if (!$mockMode && in_array(strtolower($olt->ip_address), array_map('strtolower', $mockIps))) {
            throw new Exception(
                "Mock mode dinonaktifkan untuk production. " .
                "IP address '{$olt->ip_address}' hanya untuk testing. " .
                "Gunakan IP OLT yang sebenarnya atau aktifkan mock mode di config/olt.php untuk testing."
            );
        }

        $vendor = strtoupper($olt->vendor ?? '');
        $model = strtoupper($olt->model ?? '');

        // ZTE C300
        if ($vendor === 'ZTE' && $model === 'C300') {
            return new ZteC300Driver($olt);
        }

        // ZTE C320
        if ($vendor === 'ZTE' && $model === 'C320') {
            return new ZteC320Driver($olt);
        }

        // Generic SNMP driver untuk vendor lain
        if ($olt->connection_type === 'snmp') {
            return new GenericSnmpDriver($olt);
        }

        throw new Exception("Driver tidak ditemukan untuk vendor: {$vendor}, model: {$model}");
    }

    /**
     * Get list of supported vendors and models
     */
    public static function getSupportedVendors(): array
    {
        return [
            'ZTE' => [
                'models' => ['C300', 'C320'],
                'connection_types' => ['snmp', 'telnet'],
            ],
            'Huawei' => [
                'models' => ['MA5600T', 'MA5608T'],
                'connection_types' => ['snmp', 'telnet'],
            ],
            'Fiberhome' => [
                'models' => ['AN5516', 'AN6000'],
                'connection_types' => ['snmp'],
            ],
            'Generic' => [
                'models' => ['*'],
                'connection_types' => ['snmp'],
            ],
        ];
    }
}

