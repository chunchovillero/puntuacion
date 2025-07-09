<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlowPaymentService
{
    private $apiKey;
    private $secretKey;
    private $baseUrl;
    private $environment;

    public function __construct()
    {
        $this->apiKey = config('services.flow.api_key');
        $this->secretKey = config('services.flow.secret_key');
        $this->baseUrl = config('services.flow.base_url');
        $this->environment = config('services.flow.environment');
    }

    /**
     * Crear una orden de pago en Flow
     */
    public function createPayment($orderId, $amount, $currency = 'CLP', $subject = 'Pago jornada BMX', $email = null, $returnUrl = null)
    {
        $params = [
            'commerceOrder' => $orderId,
            'subject' => $subject,
            'currency' => $currency,
            'amount' => $amount,
            'email' => $email,
            'urlConfirmation' => $returnUrl,
            'urlReturn' => $returnUrl,
        ];

        // Generar firma
        $params['s'] = $this->generateSignature($params);

        try {
            // Para desarrollo local, deshabilitar SSL
            $httpClient = Http::withOptions([
                'verify' => app()->environment('production'),
                'timeout' => 60,
                'connect_timeout' => 30,
            ]);

            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withOptions([
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ]);
            }

            $response = $httpClient->post($this->baseUrl . '/payment/create', $params);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'token' => $data['token'],
                    'url' => $data['url'] . '?token=' . $data['token'],
                    'flowOrder' => $data['flowOrder']
                ];
            } else {
                Log::error('Flow payment creation failed', [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                
                return [
                    'success' => false,
                    'error' => 'Error al crear el pago en Flow: ' . $response->body()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Flow payment exception', [
                'message' => $e->getMessage(),
                'params' => $params
            ]);

            return [
                'success' => false,
                'error' => 'Error de conexión con Flow: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener el estado de un pago
     */
    public function getPaymentStatus($token)
    {
        $params = [
            'token' => $token,
        ];

        $params['s'] = $this->generateSignature($params);

        try {
            $httpClient = Http::withOptions([
                'verify' => app()->environment('production'),
                'timeout' => 60,
            ]);

            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withOptions([
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ]);
            }

            $response = $httpClient->get($this->baseUrl . '/payment/getStatus', $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Error al obtener estado del pago: ' . $response->body()
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generar firma para Flow
     */
    private function generateSignature($params)
    {
        // Ordenar parámetros alfabéticamente
        ksort($params);
        
        // Crear string para firmar
        $string = '';
        foreach ($params as $key => $value) {
            $string .= $key . $value;
        }
        
        // Agregar secret key
        $string .= $this->secretKey;
        
        // Generar hash SHA256
        return hash('sha256', $string);
    }

    /**
     * Verificar firma de respuesta de Flow
     */
    public function verifySignature($params, $signature)
    {
        unset($params['s']); // Remover la firma de los parámetros
        $expectedSignature = $this->generateSignature($params);
        
        return hash_equals($expectedSignature, $signature);
    }
}
