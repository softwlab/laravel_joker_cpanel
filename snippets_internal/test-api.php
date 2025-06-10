<?php

$apiKey = 'a8f0b0f4d008481a1fb14cb01de7e9fb156ef905d0e3e3dc1e1221cc14254df5';
$domain = 'app.acessarchaveprime.com';
$url = "http://127.0.0.1:8000/api/public/domain_external/{$domain}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-API-KEY: {$apiKey}",
    "Accept: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: " . $httpCode . PHP_EOL;
echo "Response:" . PHP_EOL;
echo $response . PHP_EOL;
