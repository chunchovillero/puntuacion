<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SSL Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains SSL configuration for development environments.
    | These settings help resolve SSL certificate issues with external APIs.
    |
    */

    'verify_ssl' => env('DISABLE_SSL_VERIFICATION', false) === false,
    
    'ca_bundle_path' => env('CURL_CA_BUNDLE_PATH', base_path('cacert.pem')),
    
    'curl_options' => [
        'verify_ssl' => env('DISABLE_SSL_VERIFICATION', false) === false,
        'timeout' => 60,
        'connect_timeout' => 30,
    ],
    
    'guzzle_options' => [
        'verify' => env('DISABLE_SSL_VERIFICATION', false) === false ? env('CURL_CA_BUNDLE_PATH', base_path('cacert.pem')) : false,
        'timeout' => 60,
        'connect_timeout' => 30,
        'http_errors' => true,
    ],
];
