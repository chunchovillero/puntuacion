<?php

require_once __DIR__ . '/vendor/autoload.php';

// Test complete registration flow
use App\Models\Matchday;
use App\Models\Pilot;
use App\Models\Category;
use App\Services\MockFlowService;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Complete Registration Flow\n";
echo "==================================\n\n";

// Get test data
$matchday = Matchday::where('public_registration_enabled', true)->first();
$pilot = Pilot::first();
$category = Category::first();

if (!$matchday || !$pilot || !$category) {
    echo "Missing test data:\n";
    echo "- Matchdays: " . Matchday::count() . "\n";
    echo "- Pilots: " . Pilot::count() . "\n";
    echo "- Categories: " . Category::count() . "\n";
    exit;
}

echo "Test Data:\n";
echo "- Matchday: {$matchday->name} (ID: {$matchday->id})\n";
echo "- Pilot: {$pilot->full_name} (ID: {$pilot->id})\n";
echo "- Category: {$category->name} (ID: {$category->id})\n\n";

// Test MockFlowService
$mockFlowService = new MockFlowService();
$orderId = 'TEST_' . time();
$amount = 5000;
$email = 'test@example.com';
$returnUrl = 'http://127.0.0.1:8000/test-return';

echo "Testing Mock Flow Service:\n";
$result = $mockFlowService->createPayment($orderId, $amount, $email, $returnUrl);

echo "Mock Payment Result:\n";
echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

if ($result['success']) {
    echo "✅ Mock payment creation successful!\n";
    echo "Payment URL: {$result['url']}\n";
    echo "Token: {$result['token']}\n";
    
    // Test payment status
    echo "\nTesting payment status check:\n";
    $statusResult = $mockFlowService->getPaymentStatus($result['token']);
    echo json_encode($statusResult, JSON_PRETTY_PRINT) . "\n";
    
    if ($statusResult['success']) {
        echo "✅ Mock payment status check successful!\n";
    } else {
        echo "❌ Mock payment status check failed!\n";
    }
} else {
    echo "❌ Mock payment creation failed!\n";
}

echo "\n🎉 Flow integration test completed!\n";
echo "\nNext steps:\n";
echo "1. Visit: http://127.0.0.1:8000/registro-flow/jornadas\n";
echo "2. Select a matchday\n";
echo "3. Search for pilot with RUT: {$pilot->rut}\n";
echo "4. Complete registration and test payment flow\n";
