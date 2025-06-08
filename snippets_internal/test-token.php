<?php

// Script para testar diretamente a conexão com a API Cloudflare usando um token específico
require __DIR__.'/vendor/autoload.php';

$apiToken = 'SFK1Wdw202R-0rTayg9slsdBt1OwqnP_D7fRHAwg'; // Token atual
$testUrl = 'https://api.cloudflare.com/client/v4/user/tokens/verify';

echo "Testando token da API Cloudflare...\n";
echo "==============================\n\n";

// Testar usando cURL diretamente para diagnóstico completo
$ch = curl_init($testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiToken,
    'Content-Type: application/json',
]);

echo "Enviando requisição para: $testUrl\n";
echo "Headers: Authorization: Bearer {$apiToken}\n";
echo "         Content-Type: application/json\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

if ($error) {
    echo "ERRO cURL: $error\n";
    exit(1);
}

echo "Código de status HTTP: $httpCode\n\n";
echo "Resposta completa:\n";
echo json_encode(json_decode($response), JSON_PRETTY_PRINT);
echo "\n\n";

// Verificar se o token é válido
$data = json_decode($response, true);
if (isset($data['success']) && $data['success'] === true) {
    echo "✅ TOKEN VÁLIDO!\n";
    echo "Status do token: " . $data['result']['status'] . "\n";
    
    if (isset($data['result']['expires_at'])) {
        echo "Expira em: " . $data['result']['expires_at'] . "\n";
    } else {
        echo "Este token não expira.\n";
    }
    
    // Testar listagem das zonas
    echo "\nTestando acesso às zonas (domínios)...\n";
    
    $ch = curl_init('https://api.cloudflare.com/client/v4/zones');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiToken,
        'Content-Type: application/json',
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $zonesData = json_decode($response, true);
    
    echo "Código de status HTTP: $httpCode\n";
    
    if (isset($zonesData['success']) && $zonesData['success'] === true) {
        echo "✅ ACESSO ÀS ZONAS OK!\n";
        echo "Zonas encontradas: " . count($zonesData['result']) . "\n\n";
        
        if (count($zonesData['result']) > 0) {
            echo "Primeiras zonas:\n";
            $count = 0;
            foreach ($zonesData['result'] as $zone) {
                echo "- {$zone['name']} (status: {$zone['status']})\n";
                $count++;
                
                if ($count >= 5) break; // Limitar a 5 zonas
            }
        }
    } else {
        echo "❌ ERRO AO ACESSAR ZONAS:\n";
        echo json_encode($zonesData, JSON_PRETTY_PRINT) . "\n";
        
        // Verificar mensagens de erro específicas
        if (isset($zonesData['errors'])) {
            foreach ($zonesData['errors'] as $error) {
                echo "- " . $error['message'] . "\n";
            }
        }
    }
} else {
    echo "❌ TOKEN INVÁLIDO!\n";
    
    if (isset($data['errors'])) {
        foreach ($data['errors'] as $error) {
            echo "- " . $error['message'] . "\n";
        }
    }
}
