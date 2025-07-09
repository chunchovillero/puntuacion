<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as GuzzleClient;

class HttpClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Configurar Guzzle HTTP Client para desarrollo
        $this->app->bind(GuzzleClient::class, function ($app) {
            $config = [
                'timeout' => 60,
                'connect_timeout' => 30,
            ];

            // En desarrollo, deshabilitar verificación SSL
            if ($app->environment('local', 'development')) {
                $config['verify'] = false;
                $config['curl'] = [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_TIMEOUT => 60,
                ];
            }

            return new GuzzleClient($config);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Configurar defaults de cURL para desarrollo
        if ($this->app->environment('local', 'development')) {
            // Configurar variables de entorno
            putenv('CURLOPT_SSL_VERIFYPEER=0');
            putenv('CURLOPT_SSL_VERIFYHOST=0');
            
            // Configurar ini settings
            ini_set('curl.cainfo', '');
            ini_set('openssl.cafile', '');
            
            // Configurar stream context
            stream_context_set_default([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
                'http' => [
                    'timeout' => 60,
                ]
            ]);
        }
    }
}
