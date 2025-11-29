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
        // Mock mode untuk testing (gunakan IP 127.0.0.1 atau check env)
        if (config('app.olt_mock_mode', false) || $olt->ip_address === '127.0.0.1' || $olt->ip_address === 'localhost') {
            return new MockOltDriver($olt);
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

