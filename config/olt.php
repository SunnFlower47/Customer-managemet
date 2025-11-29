<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OLT Vendor Configurations
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk setiap vendor OLT termasuk OID SNMP, parameter
    | koneksi, dan setting khusus per vendor.
    |
    */

    'vendors' => [
        'zte' => [
            'c300' => [
                'driver' => \App\Drivers\ZteC300Driver::class,
                'connection_types' => ['snmp'],
                'default_port' => 161,
                'snmp_oids' => [
                    'system_description' => '1.3.6.1.2.1.1.1.0',
                    'system_uptime' => '1.3.6.1.2.1.1.3.0',
                    'system_name' => '1.3.6.1.2.1.1.5.0',
                    'pon_port_count' => '1.3.6.1.4.1.3902.1015.1.1.1.1.1.0',
                    'onu_table' => '1.3.6.1.4.1.3902.1015.1.1.1.1.2',
                    // Tambahkan OID lainnya sesuai dokumentasi ZTE C300
                ],
                'default_snmp_community' => 'public',
            ],
            'c320' => [
                'driver' => \App\Drivers\ZteC320Driver::class,
                'connection_types' => ['snmp', 'telnet'],
                'default_port' => 23, // Telnet default
                'snmp_port' => 161,
                'snmp_oids' => [
                    'system_description' => '1.3.6.1.2.1.1.1.0',
                    'system_uptime' => '1.3.6.1.2.1.1.3.0',
                    'system_name' => '1.3.6.1.2.1.1.5.0',
                    // Tambahkan OID C320 spesifik
                ],
                'telnet_commands' => [
                    'show_version' => 'show version',
                    'show_pon_port' => 'show pon port',
                    'show_onu' => 'show onu',
                    'reboot_onu' => 'reboot onu {serial}',
                    'reset_onu' => 'reset onu {serial}',
                    'disable_onu' => 'disable onu {serial}',
                    'enable_onu' => 'enable onu {serial}',
                ],
            ],
        ],
        'huawei' => [
            'ma5600t' => [
                'driver' => \App\Drivers\GenericSnmpDriver::class, // Temporary, bisa dibuat driver khusus
                'connection_types' => ['snmp', 'telnet'],
                'default_port' => 161,
                'snmp_oids' => [
                    'system_description' => '1.3.6.1.2.1.1.1.0',
                    'system_uptime' => '1.3.6.1.2.1.1.3.0',
                    // Tambahkan OID Huawei spesifik
                ],
                'telnet_commands' => [
                    'show_version' => 'display version',
                    'show_pon_port' => 'display pon port',
                    'show_onu' => 'display ont info',
                ],
            ],
        ],
        'fiberhome' => [
            'an5516' => [
                'driver' => \App\Drivers\GenericSnmpDriver::class,
                'connection_types' => ['snmp'],
                'default_port' => 161,
                'snmp_oids' => [
                    'system_description' => '1.3.6.1.2.1.1.1.0',
                    'system_uptime' => '1.3.6.1.2.1.1.3.0',
                    // Tambahkan OID Fiberhome spesifik
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'snmp_community' => 'public',
        'snmp_timeout' => 1000000, // microseconds (1 second)
        'snmp_retries' => 3,
        'telnet_timeout' => 5, // seconds
        'connection_timeout' => 10, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Settings
    |--------------------------------------------------------------------------
    */

    'monitoring' => [
        'polling_interval' => 30, // seconds
        'cache_duration' => 30, // seconds
        'auto_sync_interval' => 300, // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | RX Power Range Settings
    |--------------------------------------------------------------------------
    */

    'rx_power_ranges' => [
        'excellent' => ['min' => -15, 'max' => -8],
        'good' => ['min' => -20, 'max' => -15],
        'fair' => ['min' => -25, 'max' => -20],
        'poor' => ['min' => -30, 'max' => -25],
        'critical' => ['min' => -999, 'max' => -30],
    ],
];

