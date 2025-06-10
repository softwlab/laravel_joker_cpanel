<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\ExternalApi;

/*
 * Script para atualizar as credenciais da Cloudflare
 */

// Iniciar aplicação Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Procurar API externa do tipo Cloudflare
$api = ExternalApi::where('type', 'cloudflare')->first();

if (!$api) {
    // Se não existe, criar uma nova
    $api = new ExternalApi();
    $api->name = 'Cloudflare API';
    $api->type = 'cloudflare';
    $api->status = 'active'; // Garantir que a API está ativa
}

// Configurar as credenciais da Cloudflare
$config = [
    'cloudflare_email' => 'andressaworking1707@gmail.com',
    'cloudflare_api_key' => 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7',
    'cloudflare_api_token' => 'Kh5WAoAhNYn1-abfpY9CQAauVQCJ5kmXmjUxA94t',
    'cloudflare_zone_id' => 'dde98dbb6ac93710412de79b3272acd8',
];

$api->config = json_encode($config);
$api->status = 'active'; // Garantir que a API está ativa
$api->save();

echo "Credenciais da Cloudflare atualizadas com sucesso!\n";
echo "ID da API: " . $api->id . "\n";
echo "Nome: " . $api->name . "\n";
echo "Tipo: " . $api->type . "\n";
echo "Status: " . ($api->active ? 'Ativo' : 'Inativo') . "\n";
