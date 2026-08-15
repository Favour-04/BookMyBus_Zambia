<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Messaging Driver
    |--------------------------------------------------------------------------
    |
    | Controls how SMS & WhatsApp messages are delivered.
    |  - simulated:        log to storage/logs (dev / tests)
    |  - africas_talking:  real Africa's Talking API (prod)
    |
    */

    'driver' => env('MESSAGING_DRIVER', 'simulated'),

    'at_username' => env('AT_USERNAME'),
    'at_api_key' => env('AT_API_KEY'),
    'at_sender_id' => env('AT_SENDER_ID'),

    'at_sms_endpoint' => 'https://api.africastalking.com/version1/messaging',
    'at_whatsapp_endpoint' => 'https://chat.africastalking.com/chat',

];
