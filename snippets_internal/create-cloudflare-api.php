<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalApi;

// Verificar se já existe uma API com este nome
$existing = ExternalApi::where('type', 'cloudflare')->first();

if ($existing) {
    echo "API Cloudflare já existe com ID: " . $existing->id . "\n";
    echo "Atualizando dados...\n";
    
    $existing->name = 'Cloudflare DNS';
    $existing->external_link_api = 'https://api.cloudflare.com/client/v4/';
    $existing->key_external_api = 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7';
    $existing->status = 'active';
    $existing->config = [
        'cloudflare_email' => 'andressaworking1707@gmail.com',
        'cloudflare_api_key' => 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7',
        'auth_method' => 'api_key'
    ];
    $existing->save();
    
    echo "API Cloudflare atualizada com sucesso!\n";
} else {
    $api = new ExternalApi();
    $api->name = 'Cloudflare DNS';
    $api->type = 'cloudflare';
    $api->external_link_api = 'https://api.cloudflare.com/client/v4/';
    $api->key_external_api = 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7';
    $api->status = 'active';
    $api->config = [
        'cloudflare_email' => 'andressaworking1707@gmail.com',
        'cloudflare_api_key' => 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7',
        'auth_method' => 'api_key'
    ];
    $api->save();
    
    echo "Nova API Cloudflare criada com ID: " . $api->id . "\n";
}

echo "Configuração da API Cloudflare concluída!\n";
