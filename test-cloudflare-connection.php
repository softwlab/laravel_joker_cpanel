<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalApi;
use App\Services\CloudflareService;
use Illuminate\Support\Facades\Log;

echo "Iniciando teste de conexão com a API Cloudflare...\n";

try {
    // Obter a API Cloudflare
    $api = ExternalApi::where('type', 'cloudflare')->first();
    
    if (!$api) {
        echo "Erro: API Cloudflare não encontrada no banco de dados.\n";
        exit(1);
    }
    
    echo "API Cloudflare encontrada (ID: {$api->id}).\n";
    echo "Configuração da API:\n";
    echo "- Email: " . $api->config['cloudflare_email'] . "\n";
    echo "- API Key: " . substr($api->config['cloudflare_api_key'], 0, 5) . "..." . substr($api->config['cloudflare_api_key'], -5) . "\n";
    echo "- Método de autenticação: " . $api->config['auth_method'] . "\n\n";
    
    // Inicializar o serviço Cloudflare
    echo "Inicializando CloudflareService...\n";
    $cloudflareService = new CloudflareService($api);
    
    // Testar conexão
    echo "Testando conexão...\n";
    $result = $cloudflareService->testConnection();
    
    echo "\nResultado do teste de conexão:\n";
    echo "- Sucesso: " . ($result['success'] ? 'SIM' : 'NÃO') . "\n";
    echo "- Mensagem: " . $result['message'] . "\n";
    
    if (isset($result['data']) && !empty($result['data'])) {
        echo "\nDados recebidos:\n";
        print_r($result['data']);
    }
    
    // Buscar zonas (domínios)
    if ($result['success']) {
        echo "\nBuscando zonas (domínios) disponíveis...\n";
        $zones = $cloudflareService->getZones();
        
        if ($zones['success']) {
            echo "Zonas encontradas: " . count($zones['zones']) . "\n";
            foreach ($zones['zones'] as $index => $zone) {
                echo "\n[Zona " . ($index+1) . "]\n";
                echo "- ID: " . $zone['id'] . "\n";
                echo "- Nome: " . $zone['name'] . "\n";
                echo "- Status: " . $zone['status'] . "\n";
                if (isset($zone['name_servers']) && !empty($zone['name_servers'])) {
                    echo "- Nameservers: " . implode(', ', $zone['name_servers']) . "\n";
                }
            }
        } else {
            echo "Erro ao buscar zonas: " . $zones['message'] . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Exceção: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . " (linha " . $e->getLine() . ")\n";
}

echo "\nTeste de conexão concluído!\n";
