<?php
// Test the exact endpoint the Vue component is calling

$url = 'http://intranet.ambmx.com/admin/api/clubs';

echo "Testing Vue endpoint: $url\n";
echo str_repeat("=", 50) . "\n";

// Test with file_get_contents
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'Accept: application/json',
            'Content-Type: application/json'
        ]
    ]
]);

$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "Error: Could not fetch data from $url\n";
} else {
    echo "Response received:\n";
    echo "Length: " . strlen($response) . " bytes\n";
    echo "Content:\n";
    
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Valid JSON response:\n";
        echo "Data structure:\n";
        if (isset($data['data'])) {
            echo "- data: " . count($data['data']) . " items\n";
            if (count($data['data']) > 0) {
                echo "- First club: " . $data['data'][0]['name'] . "\n";
            }
        }
        if (isset($data['total'])) {
            echo "- total: " . $data['total'] . "\n";
        }
        if (isset($data['per_page'])) {
            echo "- per_page: " . $data['per_page'] . "\n";
        }
        if (isset($data['current_page'])) {
            echo "- current_page: " . $data['current_page'] . "\n";
        }
    } else {
        echo "Invalid JSON response. Raw content:\n";
        echo $response . "\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";

// Test with cURL to see headers
echo "Testing with cURL to check headers:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: $httpCode\n";
echo "Response:\n$response\n";
