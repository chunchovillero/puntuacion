<?php
// Test the exact HTTP API endpoint that Vue is calling

$url = 'http://intranet.ambmx.com/admin/api/clubs';

// Test 1: Simple GET request
echo "=== Testing /admin/api/clubs HTTP Endpoint ===\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "cURL Error: $error\n";
    exit;
}

echo "HTTP Status Code: $httpCode\n";

// Split headers and body
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
// Since we already closed the handle, let's split manually
$parts = explode("\r\n\r\n", $response, 2);
$headers = $parts[0] ?? '';
$body = $parts[1] ?? $response;

echo "Response Headers:\n$headers\n\n";
echo "Response Body:\n$body\n";

// Try to decode JSON
$data = json_decode($body, true);
if ($data === null) {
    echo "Failed to decode JSON. Raw response:\n";
    echo $body . "\n";
} else {
    echo "\nParsed JSON:\n";
    if (isset($data['data'])) {
        echo "Data count: " . count($data['data']) . "\n";
        echo "Total: " . ($data['total'] ?? 'not set') . "\n";
        echo "Current page: " . ($data['current_page'] ?? 'not set') . "\n";
        echo "Per page: " . ($data['per_page'] ?? 'not set') . "\n";
        
        if (empty($data['data'])) {
            echo "Data array is empty!\n";
        } else {
            echo "First club: " . ($data['data'][0]['name'] ?? 'name not found') . "\n";
        }
    } else {
        echo "No 'data' key found in response\n";
        print_r($data);
    }
}

echo "\n=== End Test ===\n";
