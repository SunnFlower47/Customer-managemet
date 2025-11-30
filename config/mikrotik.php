<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MikroTik Testing Mode
    |--------------------------------------------------------------------------
    |
    | Set to true to enable mock/testing mode. When enabled, the MikrotikService
    | will return mock data instead of connecting to real routers.
    | This is useful for development and testing without physical MikroTik devices.
    |
    */

    'testing_mode' => env('MIKROTIK_TESTING_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Mock Data
    |--------------------------------------------------------------------------
    |
    | Mock data returned when testing mode is enabled.
    |
    */

    'mock' => [
        'identity' => 'Test Router',
        'resource_usage' => [
            'uptime' => '5d 12h 30m 15s',
            'cpu-load' => '25%',
            'free-memory' => '512M',
            'total-memory' => '1G',
            'free-hdd-space' => '8G',
            'total-hdd-space' => '16G',
        ],
        'pppoe_users' => [
            [
                'name' => 'testuser1',
                'service' => 'pppoe',
                'caller-id' => '192.168.1.100',
                'address' => '192.168.1.100',
                'uptime' => '2d 5h 10m',
                'encoding' => '',
                'session-id' => '1',
            ],
            [
                'name' => 'testuser2',
                'service' => 'pppoe',
                'caller-id' => '192.168.1.101',
                'address' => '192.168.1.101',
                'uptime' => '1d 12h 5m',
                'encoding' => '',
                'session-id' => '2',
            ],
        ],
        'pppoe_secrets' => [
            'testuser1' => [
                'name' => 'testuser1',
                'service' => 'pppoe',
                'profile' => 'default',
                'remote-address' => '192.168.1.100',
                'disabled' => 'false',
            ],
            'testuser2' => [
                'name' => 'testuser2',
                'service' => 'pppoe',
                'profile' => 'default',
                'remote-address' => '192.168.1.101',
                'disabled' => 'false',
            ],
        ],
    ],
];

