<?php

namespace App\Services;

class FlowService
{
    private $apiUrl;
    private $apiKey;
    private $secretKey;

    public function __construct()
    {
        $this->apiUrl = env('FLOW_API_URL', 'https://sandbox.flow.cl/api');
        $this->apiKey = env('FLOW_API_KEY');
        $this->secretKey = env('FLOW_SECRET_KEY');
    }

    /**
     * Crear una orden de pago en Flow
     */
    public function createPayment($orderId, $amount, $email, $returnUrl)
    {
        $params = [
            'apiKey' => $this->apiKey,
            'commerceOrder' => $orderId,
            'subject' => 'Inscripción jornada BMX',
            'currency' => 'CLP',
            'amount' => $amount,
            'email' => $email,
            'urlConfirmation' => $returnUrl,
            'urlReturn' => $returnUrl,
        ];

        // Agregar signature
        $params['s'] = $this->sign($params);

        try {
            $response = $this->makeRequest('/payment/create', $params);
            
            // Flow devuelve diferentes formatos según el resultado
            if (isset($response['url']) && isset($response['token'])) {
                return [
                    'success' => true,
                    'url' => $response['url'],
                    'token' => $response['token'],
                    'flowOrder' => $response['flowOrder'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response['message'] ?? 'Error desconocido en Flow',
                    'response' => $response
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Confirmar el estado de un pago
     */
    public function getPaymentStatus($token)
    {
        $params = [
            'apiKey' => $this->apiKey,
            'token' => $token,
        ];

        $params['s'] = $this->sign($params);

        try {
            $response = $this->makeRequest('/payment/getStatus', $params);
            
            return [
                'success' => true,
                'data' => $response
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generar firma para Flow
     */
    private function sign($params)
    {
        $keys = array_keys($params);
        sort($keys);
        
        $toSign = '';
        foreach ($keys as $key) {
            $toSign .= $key . $params[$key];
        }
        
        return hash_hmac('sha256', $toSign, $this->secretKey);
    }

    /**
     * Hacer petición HTTP a Flow
     */
    private function makeRequest($endpoint, $params)
    {
        $url = $this->apiUrl . $endpoint;
        
        // Log para debugging
        \Log::info('Flow API Request', [
            'url' => $url,
            'params' => $params
        ]);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // Para desarrollo
            CURLOPT_SSL_VERIFYHOST => false, // Para desarrollo
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            curl_close($ch);
            throw new \Exception('Error cURL: ' . curl_error($ch));
        }
        
        curl_close($ch);

        // Log response para debugging
        \Log::info('Flow API Response', [
            'http_code' => $httpCode,
            'response' => $response
        ]);

        if ($httpCode !== 200) {
            throw new \Exception("Error HTTP {$httpCode}. Response: {$response}");
        }

        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error al decodificar respuesta JSON: ' . json_last_error_msg());
        }

        return $decoded;
    }
}
