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

    /*
    |--------------------------------------------------------------------------
    | Fonnte (WhatsApp Gateway)
    |--------------------------------------------------------------------------
    */
    'fonnte' => [
        'token'  => env('FONNTE_TOKEN'),
        'device' => env('FONNTE_DEVICE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging
    |--------------------------------------------------------------------------
    */
    'firebase' => [
        // Server-side (FCM send)
        'project_id'       => env('FIREBASE_PROJECT_ID'),
        'credentials_path' => env('FIREBASE_CREDENTIALS_PATH', storage_path('app/firebase-credentials.json')),
        'credentials_json' => env('FIREBASE_CREDENTIALS_JSON', env('FIREBASE_CREDENTIALS')), // Support both names

        // Client-side (browser FCM)
        'api_key'             => env('VITE_FIREBASE_API_KEY'),
        'auth_domain'         => env('VITE_FIREBASE_AUTH_DOMAIN'),
        'storage_bucket'      => env('VITE_FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('VITE_FIREBASE_MESSAGING_SENDER_ID'),
        'app_id'              => env('VITE_FIREBASE_APP_ID'),
        'vapid_key'           => env('VITE_FIREBASE_VAPID_KEY'),
    ],

];
