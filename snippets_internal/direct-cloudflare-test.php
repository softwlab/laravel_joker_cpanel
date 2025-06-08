<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalApi;

// Obter a API Cloudflare
$api = ExternalApi::where('type', 'cloudflare')->first();

if (!$api) {
    echo "API Cloudflare não encontrada no banco de dados.\n";
    exit(1);
}

$config = $api->config;

echo "Testando conexão direta com API Cloudflare via cURL...\n";
echo "Email: " . ($config['cloudflare_email'] ?? 'Não definido') . "\n";
echo "API Key: " . substr($config['cloudflare_api_key'] ?? 'Não definido', 0, 5) . "...\n\n";

// Configurar cURL para testar diretamente
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/user');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Auth-Email: ' . ($config['cloudflare_email'] ?? ''),
    'X-Auth-Key: ' . ($config['cloudflare_api_key'] ?? ''),
    'Content-Type: application/json'
]);

// Executar a requisição
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Status HTTP: " . $httpCode . "\n";
if ($error) {
    echo "Erro cURL: " . $error . "\n";
}

echo "Resposta:\n";
$data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Sucesso: " . ($data['success'] ? 'Sim' : 'Não') . "\n";
    
    if (isset($data['errors']) && !empty($data['errors'])) {
        echo "Erros:\n";
        foreach ($data['errors'] as $error) {
            echo "- " . $error['message'] . "\n";
            if (isset($error['error_chain'])) {
                foreach ($error['error_chain'] as $chainError) {
                    echo "  * " . $chainError['message'] . "\n";
                }
            }
        }
    }
    
    if (isset($data['result']) && !empty($data['result'])) {
        echo "\nDados do usuário:\n";
        print_r($data['result']);
    }
} else {
    echo "Resposta não é um JSON válido:\n" . $response . "\n";
}
