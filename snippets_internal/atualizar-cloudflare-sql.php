<?php

require 'vendor/autoload.php';

// Iniciar aplicação Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Usar DB para atualização direta via SQL
use Illuminate\Support\Facades\DB;

echo "Atualizando API Cloudflare...\n";

// Verificar o registro atual
$api = DB::table('external_apis')->where('type', 'cloudflare')->first();

if (!$api) {
    echo "API Cloudflare não encontrada. Criando...\n";
    
    // Criar nova API Cloudflare
    $id = DB::table('external_apis')->insertGetId([
        'name' => 'Cloudflare API',
        'type' => 'cloudflare',
        'status' => 'active',
        'config' => json_encode([
            'cloudflare_email' => 'andressaworking1707@gmail.com',
            'cloudflare_api_key' => 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7',
            'cloudflare_api_token' => 'Kh5WAoAhNYn1-abfpY9CQAauVQCJ5kmXmjUxA94t',
            'cloudflare_zone_id' => 'dde98dbb6ac93710412de79b3272acd8',
        ]),
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "Nova API Cloudflare criada com ID: $id e status: active\n";
} else {
    echo "API Cloudflare encontrada com ID: {$api->id}\n";
    echo "Status atual: " . ($api->status ?? 'NULL') . "\n";
    
    // Atualizar o registro existente
    DB::table('external_apis')
        ->where('id', $api->id)
        ->update([
            'status' => 'active',
            'config' => json_encode([
                'cloudflare_email' => 'andressaworking1707@gmail.com',
                'cloudflare_api_key' => 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7',
                'cloudflare_api_token' => 'Kh5WAoAhNYn1-abfpY9CQAauVQCJ5kmXmjUxA94t',
                'cloudflare_zone_id' => 'dde98dbb6ac93710412de79b3272acd8',
            ]),
            'updated_at' => now()
        ]);
    
    // Verificar se foi atualizado
    $apiAtualizada = DB::table('external_apis')->find($api->id);
    echo "Status após atualização: " . ($apiAtualizada->status ?? 'NULL') . "\n";
}

echo "Processo concluído!\n";
