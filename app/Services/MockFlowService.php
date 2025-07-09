<?php

namespace App\Services;

class MockFlowService
{
    /**
     * Crear una orden de pago simulada (para desarrollo)
     */
    public function createPayment($orderId, $amount, $email, $returnUrl)
    {
        // Simular respuesta exitosa de Flow
        $mockToken = 'MOCK_TOKEN_' . time() . '_' . rand(1000, 9999);
        $mockFlowOrder = rand(100000, 999999);
        
        // Para propósitos de testing, simular el link de pago
        $mockPaymentUrl = url('/registro-flow/mock-payment?' . http_build_query([
            'token' => $mockToken,
            'order' => $orderId,
            'amount' => $amount
        ]));
        
        return [
            'success' => true,
            'url' => $mockPaymentUrl,
            'token' => $mockToken,
            'flowOrder' => $mockFlowOrder
        ];
    }

    /**
     * Confirmar el estado de un pago simulado
     */
    public function getPaymentStatus($token)
    {
        // Simular respuesta exitosa de confirmación
        return [
            'success' => true,
            'data' => [
                'status' => 2, // 2 = Pagado
                'flowOrder' => rand(100000, 999999),
                'amount' => 5000,
                'currency' => 'CLP',
                'paidAt' => now()->format('Y-m-d H:i:s')
            ]
        ];
    }
}
