<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\ExternalApi;
use Illuminate\Support\Facades\DB;

// Verificar se a coluna config já existe
if (!Schema::hasColumn('external_apis', 'config')) {
    echo "Adicionando coluna 'config' à tabela external_apis...\n";
    Schema::table('external_apis', function (Blueprint $table) {
        $table->json('config')->nullable()->after('status');
    });
    echo "Coluna 'config' adicionada com sucesso!\n\n";
} else {
    echo "A coluna 'config' já existe na tabela external_apis.\n\n";
}

// Verificar se já existe uma API Cloudflare
$existingApi = ExternalApi::where('type', 'cloudflare')->first();

if ($existingApi) {
    echo "API Cloudflare já existe com ID: " . $existingApi->id . "\n";
    echo "Atualizando dados...\n";
    
    $existingApi->name = 'Cloudflare DNS';
    $existingApi->external_link_api = 'https://api.cloudflare.com/client/v4/';
    $existingApi->key_external_api = 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7';
    $existingApi->status = 'active';

    // Se a coluna config existe, atualizar com os dados de configuração
    try {
        $configData = [
            'cloudflare_email' => 'andressaworking1707@gmail.com',
            'cloudflare_api_key' => 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7',
            'auth_method' => 'api_key'
        ];
        $existingApi->config = $configData; // Não precisa de json_encode aqui, o Laravel fará isso automaticamente
        $existingApi->save();
        echo "API Cloudflare atualizada com sucesso!\n";
    } catch (Exception $e) {
        echo "Erro ao atualizar API Cloudflare: " . $e->getMessage() . "\n";
    }
} else {
    echo "Criando nova API Cloudflare...\n";
    try {
        $api = new ExternalApi();
        $api->name = 'Cloudflare DNS';
        $api->type = 'cloudflare';
        $api->external_link_api = 'https://api.cloudflare.com/client/v4/';
        $api->key_external_api = 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7';
        $api->status = 'active';
        
        // Se a coluna config existe, adicionar dados de configuração
        $configData = [
            'cloudflare_email' => 'andressaworking1707@gmail.com',
            'cloudflare_api_key' => 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7',
            'auth_method' => 'api_key'
        ];
        $api->config = $configData; // Não precisa de json_encode aqui, o Laravel fará isso automaticamente
        $api->save();
        echo "Nova API Cloudflare criada com ID: " . $api->id . "\n";
    } catch (Exception $e) {
        echo "Erro ao criar API Cloudflare: " . $e->getMessage() . "\n";
    }
}

echo "\nConfiguração da API Cloudflare concluída!\n";
