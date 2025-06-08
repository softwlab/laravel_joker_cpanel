<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalApi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Obter a API Cloudflare
$api = ExternalApi::where('type', 'cloudflare')->first();

if ($api) {
    echo "API Cloudflare encontrada (ID: {$api->id})\n";
    
    // Verificar o tipo de dados da coluna config
    echo "Tipo da configuração: " . gettype($api->config) . "\n";
    
    // Exibir os dados da configuração
    echo "Dados da configuração:\n";
    var_dump($api->config);
    
    // Verificar se as chaves esperadas existem
    echo "\nVerificando chaves específicas:\n";
    echo "cloudflare_email existe? " . (isset($api->config['cloudflare_email']) ? "SIM" : "NÃO") . "\n";
    echo "cloudflare_api_key existe? " . (isset($api->config['cloudflare_api_key']) ? "SIM" : "NÃO") . "\n";
    echo "auth_method existe? " . (isset($api->config['auth_method']) ? "SIM" : "NÃO") . "\n";
    
    // Atualizar as credenciais diretamente
    echo "\nAtualizando configuração diretamente...\n";
    $api->config = [
        'cloudflare_email' => 'andressaworking1707@gmail.com',
        'cloudflare_api_key' => 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7',
        'auth_method' => 'api_key'
    ];
    $api->save();
    
    echo "Configuração atualizada. Novos dados:\n";
    var_dump($api->config);
    
    // Verificar formato JSON armazenado no banco
    $rawApiData = DB::table('external_apis')->where('id', $api->id)->first();
    echo "\nJSON bruto armazenado no banco:\n";
    echo $rawApiData->config . "\n";
} else {
    echo "API Cloudflare não encontrada\n";
}
