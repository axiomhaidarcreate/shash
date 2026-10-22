<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your WhatsApp API provider credentials here.
    | Supported providers: UltraMsg, Twilio, WhatsApp Business Cloud API, WATI, etc.
    |
    | To enable, add these values to your .env file:
    |   WHATSAPP_API_URL=https://your-provider-api-url.com
    |   WHATSAPP_API_TOKEN=your-api-token-here
    |   WHATSAPP_INSTANCE_ID=your-instance-id (if applicable)
    |
    */
    'whatsapp' => [
        'api_url'     => env('WHATSAPP_API_URL', ''),
        'api_token'   => env('WHATSAPP_API_TOKEN', ''),
        'instance_id' => env('WHATSAPP_INSTANCE_ID', ''),
    ],

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID', 'shash-c7c31'),
    ],

];
