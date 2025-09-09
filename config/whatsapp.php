<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp API integration
    |
    */

    'api_url' => env('WHATSAPP_API_URL', 'https://api.whatsapp.com/send'),
    'api_key' => env('WHATSAPP_API_KEY', 'test_whatsapp_key'),

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for WhatsApp messages
    |
    */

    'default_phone' => env('WHATSAPP_DEFAULT_PHONE', '085157698801'),
    'company_phone' => env('WHATSAPP_COMPANY_PHONE', '081234567890'),

    /*
    |--------------------------------------------------------------------------
    | Message Templates
    |--------------------------------------------------------------------------
    |
    | Message templates for different types of notifications
    |
    */

    'templates' => [
        'payment_code' => 'payment_code_template',
        'reminder' => 'reminder_template',
        'overdue' => 'overdue_template',
    ],
];
