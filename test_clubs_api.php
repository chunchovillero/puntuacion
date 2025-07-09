<?php
// Test script para verificar que la API de clubs funciona correctamente

// Realizar una solicitud GET simple a la API de clubs
$url = 'http://localhost:8001/gestionar/api/clubs';

// Inicializar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Ejecutar la solicitud
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "=== TEST CLUBS API ===\n";
echo "URL: $url\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response length: " . strlen($response) . " characters\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data) {
            echo "✓ JSON Response received\n";
            echo "Structure: " . json_encode(array_keys($data), JSON_PRETTY_PRINT) . "\n";
            
            if (isset($data['data'])) {
                echo "Clubs count: " . count($data['data']) . "\n";
                if (!empty($data['data'])) {
                    echo "Sample club: " . json_encode($data['data'][0], JSON_PRETTY_PRINT) . "\n";
                }
            }
        } else {
            echo "✗ Invalid JSON response\n";
            echo "Raw response (first 500 chars): " . substr($response, 0, 500) . "\n";
        }
    } else {
        echo "✗ HTTP Error $httpCode\n";
        echo "Response (first 500 chars): " . substr($response, 0, 500) . "\n";
    }
}
?>
