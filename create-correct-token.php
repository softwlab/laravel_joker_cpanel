<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalApi;
use Illuminate\Support\Facades\Log;

echo "Configurando API Cloudflare com token correto\n";
echo "===========================================\n\n";

// Este token é apenas um exemplo - você precisa criar um novo token no Cloudflare com as permissões corretas
// IMPORTANTE: O token deve ter no mínimo estas permissões:
// - Zone:Zone:Read (para listar zonas/domínios)
// - Zone:DNS:Read (para listar registros DNS)
// - Zone:DNS:Edit (para editar registros DNS, se necessário)

// Deixe em branco e execute o script - ele mostrará instruções claras
$newApiToken = 'Kh5WAoAhNYn1-abfpY9CQAauVQCJ5kmXmjUxA94t'; 

echo "Este script configura um novo token API para o Cloudflare com as permissões corretas.\n\n";

echo "INSTRUÇÕES PARA CRIAR UM TOKEN DE API CORRETO:\n";
echo "=============================================\n";
echo "1. Acesse o painel da Cloudflare: https://dash.cloudflare.com/profile/api-tokens\n";
echo "2. Clique em \"Create Token\" e depois \"Create Custom Token\"\n";
echo "3. Dê um nome ao token, como \"Laravel DNS Management\"\n";
echo "4. Configure as permissões:\n";
echo "   - Zone > Zone > Read\n";
echo "   - Zone > DNS > Read\n";
echo "   - Zone > DNS > Edit\n";
echo "5. Em \"Zone Resources\", selecione \"Include\" > \"All zones\"\n";
echo "6. Crie o token e copie o valor\n";
echo "7. Cole o token neste script e execute novamente\n\n";

if (empty($newApiToken)) {
    echo "⚠️ TOKEN NÃO ESPECIFICADO ⚠️\n";
    echo "Edite este arquivo e adicione o token na variável \$newApiToken\n";
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
    echo "Configuração anterior:\n";
    $oldConfig = $api->config;
    foreach ($oldConfig as $key => $value) {
        if ($key === 'cloudflare_api_token') {
            echo "- {$key}: " . substr($value, 0, 5) . '...' . substr($value, -5) . "\n";
        } else {
            echo "- {$key}: {$value}\n";
        }
    }
    
    // Atualizar para o novo token
    $api->config = [
        'cloudflare_api_token' => $newApiToken,
        'auth_method' => 'token'
    ];
    
    $api->save();
    
    echo "\n✅ Token atualizado com sucesso!\n";
    
    // Testar a conexão com o novo token
    echo "\nTestando conexão com o novo token...\n";
    $cloudflareService = new \App\Services\CloudflareService($api);
    $result = $cloudflareService->testConnection();
    
    if ($result['success']) {
        echo "✅ SUCESSO: Conexão com Cloudflare estabelecida corretamente!\n";
        
        // Listar algumas zonas para confirmar
        echo "\nTentando listar domínios...\n";
        $zones = $cloudflareService->getZones();
        if ($zones['success']) {
            echo "✅ SUCESSO: Domínios listados corretamente!\n";
            echo "Domínios disponíveis: " . $zones['total'] . "\n";
            
            if ($zones['total'] > 0) {
                foreach ($zones['zones'] as $zone) {
                    echo "- " . $zone['name'] . " (status: " . $zone['status'] . ")\n";
                }
            } else {
                echo "\nNenhum domínio encontrado. Para adicionar um domínio:\n";
                echo "1. Acesse o painel do Cloudflare: https://dash.cloudflare.com\n";
                echo "2. Clique em \"Add a Site\" e siga as instruções\n";
            }
        } else {
            echo "❌ ERRO ao listar domínios: " . $zones['message'] . "\n";
            
            // Restaurar configuração anterior
            $api->config = $oldConfig;
            $api->save();
            
            echo "⚠️ Configuração restaurada para o estado anterior devido a erro.\n";
        }
    } else {
        echo "❌ ERRO: " . $result['message'] . "\n";
        
        // Detalhes do erro
        if (isset($result['data']['errors'])) {
            echo "Detalhes do erro:\n";
            foreach ($result['data']['errors'] as $error) {
                echo "- " . $error['message'] . "\n";
            }
        }
        
        // Restaurar configuração anterior
        $api->config = $oldConfig;
        $api->save();
        
        echo "⚠️ Configuração restaurada para o estado anterior devido a erro.\n";
    }
} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

echo "\nProcesso concluído.\n";
