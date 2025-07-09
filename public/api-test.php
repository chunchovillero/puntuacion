<?php
header('Content-Type: application/json');
echo json_encode([
    'message' => 'Direct PHP API test is working',
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'url' => $_SERVER['REQUEST_URI']
]);
