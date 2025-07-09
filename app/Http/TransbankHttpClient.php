<?php

namespace App\Http;

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\RequestOptions;

class TransbankHttpClient extends BaseClient
{
    public function __construct(array $config = [])
    {
        // Forzar configuración SSL para desarrollo
        if (app()->environment('local', 'development')) {
            $config = array_merge($config, [
                RequestOptions::VERIFY => false,
                RequestOptions::TIMEOUT => 60,
                RequestOptions::CONNECT_TIMEOUT => 30,
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                    CURLOPT_TIMEOUT => 60,
                ],
                'headers' => [
                    'User-Agent' => 'TransbankSDK-PHP/4.0.0',
                ]
            ]);
        }
        
        parent::__construct($config);
    }
}
