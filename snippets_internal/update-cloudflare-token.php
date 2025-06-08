<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "Atualizando configurações da API Cloudflare para usar API Token...\n";

try {
    // Obter a API Cloudflare
    $api = ExternalApi::where('type', 'cloudflare')->first();
    
    if (!$api) {
        echo "Erro: API Cloudflare não encontrada no banco de dados.\n";
        exit(1);
    }
    
    echo "API Cloudflare encontrada (ID: {$api->id}).\n";
    echo "Configuração atual:\n";
    echo "- Email: " . ($api->config['cloudflare_email'] ?? 'Não definido') . "\n";
    echo "- Auth method: " . ($api->config['auth_method'] ?? 'Não definido') . "\n\n";
    
    // Definir um novo token de API (método preferido pela Cloudflare)
    // Exemplo de token - substitua por um token real e válido
    $apiToken = 'Kh5WAoAhNYn1-abfpY9CQAauVQCJ5kmXmjUxA94t';
    
    // Atualizar a configuração
    $api->config = [
        'cloudflare_api_token' => $apiToken,
        'auth_method' => 'token'
    ];
    
    $api->save();
    
    echo "Configuração atualizada com sucesso!\n";
    echo "Nova configuração:\n";
    echo "- API Token: " . substr($apiToken, 0, 5) . "..." . substr($apiToken, -5) . "\n";
    echo "- Auth method: token\n";
    
    echo "\nAPI Cloudflare agora está configurada para usar API Token em vez de Email + API Key.\n";
    
} catch (\Exception $e) {
    echo "Erro ao atualizar configuração: " . $e->getMessage() . "\n";
}
