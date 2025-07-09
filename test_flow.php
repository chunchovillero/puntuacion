<?php

require_once __DIR__ . '/vendor/autoload.php';

// Test Flow API integration
use App\Services\FlowService;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Initialize Flow service
$flowService = new FlowService();

// Test parameters
$orderId = 'TEST_' . time();
$amount = 5000;
$email = 'test@example.com';
$returnUrl = 'http://127.0.0.1:8000/test-return';

echo "Testing Flow API Integration\n";
echo "============================\n";
echo "Order ID: {$orderId}\n";
echo "Amount: {$amount}\n";
echo "Email: {$email}\n";
echo "Return URL: {$returnUrl}\n\n";

try {
    $result = $flowService->createPayment($orderId, $amount, $email, $returnUrl);
    
    echo "Flow API Response:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
