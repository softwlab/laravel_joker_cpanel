<?php

// Script para corrigir o Zone ID na configuração da API Cloudflare
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Usar o Eloquent para atualizar a configuração
use App\Models\ExternalApi;
use Illuminate\Support\Facades\Log;

try {
    $api = ExternalApi::find(1);
    
    if (!$api) {
        echo "API não encontrada.\n";
        exit(1);
    }
    
    echo "Configuração atual: " . json_encode($api->config) . "\n";
    
    // Preservar configuração existente e adicionar Zone ID
    $config = $api->config ?: [];
    $config['cloudflare_zone_id'] = 'dde98dbb6ac93710412de79b3272acd8';
    $api->config = $config;
    
    $api->save();
    
    // Verificar se foi salvo corretamente
    $savedApi = ExternalApi::find(1)->fresh();
    echo "Configuração atualizada: " . json_encode($savedApi->config) . "\n";
    
    if (isset($savedApi->config['cloudflare_zone_id'])) {
        echo "Zone ID configurado com sucesso!\n";
    } else {
        echo "Falha ao configurar Zone ID.\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Concluído.\n";
