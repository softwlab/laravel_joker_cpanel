<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

$app = app();
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PublicApiKey;
use Illuminate\Support\Str;

// Criar uma nova chave API
$apiKey = new PublicApiKey();
$apiKey->key = Str::random(64); // Gera uma string aleatória de 64 caracteres
$apiKey->name = 'Chave de teste gerada em ' . date('Y-m-d H:i:s');
$apiKey->description = 'Chave criada para testes da API pública';
$apiKey->active = true;
$apiKey->save();

echo "Nova API key criada com sucesso!\n";
echo "Key: " . $apiKey->key . "\n";
echo "ID: " . $apiKey->id . "\n";
echo "Data de criação: " . $apiKey->created_at . "\n";

// Comandos curl para testes:
echo "\n--- Comandos para testar a API ---\n\n";

$baseUrl = "http://localhost";
$apiKey = $apiKey->key;

echo "1. Testar endpoint getDomainData:\n";
echo "curl -X GET \"{$baseUrl}/api/domain/example.com\" ^\n";
echo "  -H \"X-API-Key: {$apiKey}\" ^\n";
echo "  -H \"Content-Type: application/json\" ^\n";
echo "  -H \"Accept: application/json\"\n\n";

echo "2. Testar endpoint getTemplateConfig:\n";
echo "curl -X POST \"{$baseUrl}/api/template/config\" ^\n";
echo "  -H \"X-API-Key: {$apiKey}\" ^\n";
echo "  -H \"Content-Type: application/json\" ^\n";
echo "  -H \"Accept: application/json\" ^\n";
echo "  -d \"{\\\"domain\\\": \\\"example.com\\\"}\"\n";
