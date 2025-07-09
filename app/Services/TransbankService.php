<?php

namespace App\Services;

use Transbank\Webpay\WebpayPlus\Transaction;
use Transbank\Webpay\WebpayPlus;

class TransbankService
{
    public function __construct()
    {
        // Configurar WebPay para desarrollo
        WebpayPlus::configureForIntegration('597055555532', '579B532A7440BB0C9079DED94D31EA1615BACEB56610332264630D42D0A36B1C');
    }

    /**
     * Crear transacción con configuración SSL para desarrollo
     */
    public function createTransaction($orderId, $sessionId, $amount, $returnUrl)
    {
        // Configurar cURL para desarrollo local
        if (app()->environment('local', 'development')) {
            // Temporalmente deshabilitar verificación SSL solo para desarrollo
            $originalVerifyPeer = curl_getinfo(CURLOPT_SSL_VERIFYPEER);
            $originalVerifyHost = curl_getinfo(CURLOPT_SSL_VERIFYHOST);
            
            // Configurar contexto por defecto para cURL
            $context = stream_context_create([
                'http' => [
                    'timeout' => 60,
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);
        }

        $transaction = new Transaction();
        return $transaction->create($orderId, $sessionId, $amount, $returnUrl);
    }

    /**
     * Confirmar transacción
     */
    public function commitTransaction($token)
    {
        $transaction = new Transaction();
        return $transaction->commit($token);
    }
}
