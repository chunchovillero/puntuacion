<?php
// Script simple para probar la API de clubes
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simular una request GET a la API
$request = Illuminate\Http\Request::create('/admin/api/clubs', 'GET');
$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
echo "Response Length: " . strlen($response->getContent()) . " characters\n";

if ($response->getStatusCode() === 200) {
    $content = $response->getContent();
    $data = json_decode($content, true);
    
    if ($data && isset($data['data'])) {
        echo "Clubs returned: " . count($data['data']) . "\n";
        if (!empty($data['data'])) {
            echo "First club: " . json_encode($data['data'][0], JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "Raw response (first 1000 chars): " . substr($content, 0, 1000) . "\n";
    }
} else {
    echo "Error response: " . substr($response->getContent(), 0, 1000) . "\n";
}

$kernel->terminate($request, $response);
?>
