<?php
// Test direct API access
$url = 'http://intranet.ambmx.com/admin/api/clubs';

// Test both with cURL and file_get_contents
echo "Testing API endpoint: $url\n\n";

// Test 1: Using cURL
echo "=== Test 1: Using cURL ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response length: " . strlen($response) . " bytes\n";
    echo "Response preview (first 500 chars):\n" . substr($response, 0, 500) . "\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data) {
            echo "JSON parsed successfully\n";
            echo "Data keys: " . implode(', ', array_keys($data)) . "\n";
            if (isset($data['data'])) {
                echo "Number of clubs: " . count($data['data']) . "\n";
            }
        } else {
            echo "Failed to parse JSON\n";
        }
    }
}
echo "\n";

// Test 2: Using file_get_contents
echo "=== Test 2: Using file_get_contents ===\n";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Accept: application/json\r\n",
        'timeout' => 30
    ]
]);

$response2 = @file_get_contents($url, false, $context);
if ($response2 !== false) {
    echo "Success with file_get_contents\n";
    echo "Response length: " . strlen($response2) . " bytes\n";
    echo "Response preview (first 500 chars):\n" . substr($response2, 0, 500) . "\n";
} else {
    echo "Failed with file_get_contents\n";
    echo "Last error: " . print_r(error_get_last(), true) . "\n";
}
?>
