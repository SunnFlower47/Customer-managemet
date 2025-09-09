<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Valid API Keys
    |--------------------------------------------------------------------------
    |
    | List of valid API keys for WhatsApp and other external services
    |
    */

    'valid_keys' => [
        'test_api_key_123',
        'whatsapp_api_key_456',
        'payment_gateway_key_789',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting configuration for API endpoints
    |
    */

    'rate_limit' => [
        'max_attempts' => 60,
        'decay_minutes' => 1,
    ],
];
