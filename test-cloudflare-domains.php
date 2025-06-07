<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalApi;
use App\Services\CloudflareService;
use App\Services\DnsService;
use Illuminate\Support\Facades\Log;

echo "Testando listagem de domínios Cloudflare\n";
echo "======================================\n\n";

try {
    // Buscar a API Cloudflare
    $api = ExternalApi::where('type', 'cloudflare')->first();
    
    if (!$api) {
        echo "Erro: API Cloudflare não encontrada no banco de dados.\n";
        exit(1);
    }
    
    echo "API Cloudflare encontrada (ID: {$api->id})\n";
    echo "Método de autenticação: " . ($api->config['auth_method'] ?? 'não definido') . "\n\n";
    
    // Imprimir configuração atual
    echo "Configuração atual:\n";
    foreach ($api->config as $key => $value) {
        if ($key === 'cloudflare_api_token') {
            $maskedToken = substr($value, 0, 5) . '...' . substr($value, -5);
            echo "- {$key}: {$maskedToken}\n";
        } else {
            echo "- {$key}: {$value}\n";
        }
    }
    echo "\n";
    
    // Criar serviço diretamente
    echo "Criando instância do CloudflareService diretamente...\n";
    $service = new CloudflareService($api);
    
    // Testar conexão
    echo "Testando conexão...\n";
    $testResult = $service->testConnection();
    
    if ($testResult['success']) {
        echo "✅ Conexão bem-sucedida!\n\n";
        
        // Listar domínios
        echo "Listando domínios (zonas)...\n";
        $zonesResult = $service->getZones();
        
        if ($zonesResult['success']) {
            echo "✅ Zonas obtidas com sucesso!\n";
            echo "Total de zonas: " . $zonesResult['total'] . "\n\n";
            
            if ($zonesResult['total'] > 0) {
                echo "Zonas encontradas:\n";
                foreach ($zonesResult['zones'] as $zone) {
                    echo "- {$zone['name']} (status: {$zone['status']})\n";
                }
            } else {
                echo "Nenhuma zona/domínio encontrada na conta.\n";
                echo "\nPara adicionar um domínio de teste ao Cloudflare:\n";
                echo "1. Acesse o painel do Cloudflare: https://dash.cloudflare.com\n";
                echo "2. Clique em \"Add a Site\" ou \"+ Adicionar site\"\n";
                echo "3. Siga as instruções para adicionar um domínio\n";
            }
        } else {
            echo "❌ Falha ao obter zonas: " . $zonesResult['message'] . "\n";
            
            // Debugar o request
            echo "\nDepurando request de zonas...\n";
            // Simulando o request diretamente com cURL para diagnóstico
            $token = $api->config['cloudflare_api_token'];
            
            $ch = curl_init('https://api.cloudflare.com/client/v4/zones');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            echo "Código HTTP: $httpCode\n";
            if ($error) echo "Erro cURL: $error\n";
            echo "Resposta:\n" . json_encode(json_decode($response), JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "❌ Falha na conexão: " . $testResult['message'] . "\n";
        
        if (isset($testResult['data']['errors'])) {
            echo "Detalhes do erro:\n";
            foreach ($testResult['data']['errors'] as $error) {
                echo "- " . $error['message'] . "\n";
            }
        }
    }
    
    // Teste usando o DnsService (como o controlador faz)
    echo "\n\nTestando via DnsService (como o controlador usa)...\n";
    $dnsService = new DnsService();
    
    $connectionTest = $dnsService->testConnection($api);
    
    if ($connectionTest['success']) {
        echo "✅ Conexão via DnsService bem-sucedida!\n\n";
        
        // Obter o serviço específico para o tipo de API
        $apiService = $dnsService->getApiService($api);
        
        // Obter as zonas/domínios
        $result = $apiService->getZones();
        
        if ($result['success']) {
            echo "✅ Zonas obtidas via DnsService com sucesso!\n";
            echo "Total de zonas: " . $result['total'] . "\n\n";
            
            if ($result['total'] > 0) {
                echo "Zonas encontradas via DnsService:\n";
                foreach ($result['zones'] as $zone) {
                    echo "- {$zone['name']} (status: {$zone['status']})\n";
                }
            } else {
                echo "Nenhuma zona encontrada via DnsService.\n";
            }
        } else {
            echo "❌ Falha ao obter zonas via DnsService: " . $result['message'] . "\n";
        }
    } else {
        echo "❌ Falha na conexão via DnsService: " . $connectionTest['message'] . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}

echo "\nTeste concluído.\n";
