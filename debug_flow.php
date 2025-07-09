<?php

require_once __DIR__ . '/vendor/autoload.php';

// Debug Flow signature generation
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Flow API Debug\n";
echo "==============\n";

$apiKey = env('FLOW_API_KEY');
$secretKey = env('FLOW_SECRET_KEY');
$apiUrl = env('FLOW_API_URL');

echo "API Key: {$apiKey}\n";
echo "Secret Key: {$secretKey}\n";
echo "API URL: {$apiUrl}\n\n";

// Test parameters
$params = [
    'apiKey' => $apiKey,
    'commerceOrder' => 'TEST_' . time(),
    'subject' => 'Inscripción jornada BMX',
    'currency' => 'CLP',
    'amount' => 5000,
    'email' => 'test@example.com',
    'urlConfirmation' => 'http://127.0.0.1:8000/test-return',
    'urlReturn' => 'http://127.0.0.1:8000/test-return',
];

echo "Parameters before signature:\n";
foreach ($params as $key => $value) {
    echo "  {$key}: {$value}\n";
}

// Generate signature
$keys = array_keys($params);
sort($keys);

echo "\nSorted keys:\n";
foreach ($keys as $key) {
    echo "  {$key}\n";
}

$toSign = '';
foreach ($keys as $key) {
    $toSign .= $key . $params[$key];
}

echo "\nString to sign:\n{$toSign}\n";

$signature = hash_hmac('sha256', $toSign, $secretKey);
echo "\nGenerated signature: {$signature}\n";

$params['s'] = $signature;

echo "\nFinal parameters:\n";
foreach ($params as $key => $value) {
    echo "  {$key}: {$value}\n";
}
