<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalApi;
use Illuminate\Support\Facades\Log;

echo "Configurando API Cloudflare para usar API Token\n";
echo "==============================================\n\n";

// Este é um exemplo de API Token. Você precisa:
// 1. Acessar o painel da Cloudflare: https://dash.cloudflare.com/profile/api-tokens
// 2. Criar um novo token clicando em "Create Token"
// 3. Selecionar "Create Custom Token"
// 4. Dar um nome ao token, como "Laravel DNS Management"
// 5. Conceder as permissões:
//    - Zone > Zone > Read
//    - Zone > DNS > Edit
// 6. Definir os recursos (pode ser específico para um domínio ou para todas as zonas)
// 7. Criar o token e copiar o valor gerado para substituir abaixo
$apiToken = 'SFK1Wdw202R-0rTayg9slsdBt1OwqnP_D7fRHAwg'; // <-- INSIRA SEU API TOKEN AQUI

if (empty($apiToken)) {
    echo "\nERRO: Você precisa fornecer um API Token válido.\n";
    echo "Edite este arquivo e adicione o token antes de executá-lo novamente.\n";
    exit(1);
}

try {
    // Buscar a API Cloudflare
    $api = ExternalApi::where('type', 'cloudflare')->first();
    
    if (!$api) {
        echo "Erro: API Cloudflare não encontrada no banco de dados.\n";
        exit(1);
    }
    
    echo "API Cloudflare encontrada (ID: {$api->id})\n";
    echo "Método atual: " . ($api->config['auth_method'] ?? 'não definido') . "\n\n";
    
    // Atualizar para usar API Token
    $api->config = [
        'cloudflare_api_token' => $apiToken,
        'auth_method' => 'token'
    ];
    
    $api->save();
    
    echo "Configuração atualizada com sucesso!\n";
    echo "API Cloudflare configurada para usar API Token.\n\n";
    
    echo "Testando conexão...\n";
    $cloudflareService = new \App\Services\CloudflareService($api);
    $result = $cloudflareService->testConnection();
    
    if ($result['success']) {
        echo "SUCESSO: Conexão com Cloudflare estabelecida corretamente!\n";
        
        // Listar algumas zonas para confirmar
        $zones = $cloudflareService->getZones();
        if ($zones['success']) {
            echo "\nDomínios disponíveis: " . $zones['total'] . "\n";
            foreach ($zones['zones'] as $zone) {
                echo "- " . $zone['name'] . " (status: " . $zone['status'] . ")\n";
            }
        }
    } else {
        echo "ERRO: " . $result['message'] . "\n";
        
        if (isset($result['data']['errors'])) {
            echo "Detalhes do erro:\n";
            foreach ($result['data']['errors'] as $error) {
                echo "- " . $error['message'] . "\n";
                if (isset($error['error_chain'])) {
                    foreach ($error['error_chain'] as $chainError) {
                        echo "  * " . $chainError['message'] . "\n";
                    }
                }
            }
        }
    }
} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\nProcesso concluído.\n";
