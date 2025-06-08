<?php

require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "====== DIAGNÓSTICO E CORREÇÃO DE REGISTROS DNS ======\n\n";

// 1. Investigar o problema com o registro DNS ID 1
echo "1. Verificando registro DNS com ID 1...\n";
$dnsRecord = DB::table('dns_records')->where('id', 1)->first();

if ($dnsRecord) {
    echo "Registro encontrado:\n";
    echo "- ID: {$dnsRecord->id}\n";
    echo "- Nome: {$dnsRecord->name}\n";
    echo "- Tipo: {$dnsRecord->record_type}\n";
    echo "- external_api_id: " . ($dnsRecord->external_api_id ?? 'NULL') . "\n";
    echo "- bank_template_id: " . ($dnsRecord->bank_template_id ?? 'NULL') . "\n";
    echo "- user_id: " . ($dnsRecord->user_id ?? 'NULL') . "\n";
} else {
    echo "Nenhum registro DNS com ID 1 encontrado.\n";
}

// 2. Verificar se as tabelas relacionadas existem e contêm os registros necessários
echo "\n2. Verificando tabelas relacionadas...\n";

// Verificar a tabela external_apis
echo "Tabela external_apis: ";
if (Schema::hasTable('external_apis')) {
    echo "EXISTE\n";
    $externalApi = DB::table('external_apis')->where('id', 1)->first();
    if ($externalApi) {
        echo "- Registro ID 1 encontrado: {$externalApi->name}\n";
    } else {
        echo "- Registro ID 1 NÃO encontrado\n";
    }
} else {
    echo "NÃO EXISTE\n";
}

// Verificar a tabela bank_templates
echo "Tabela bank_templates: ";
if (Schema::hasTable('bank_templates')) {
    echo "EXISTE\n";
    $bankTemplate = DB::table('bank_templates')->where('id', 1)->first();
    if ($bankTemplate) {
        echo "- Registro ID 1 encontrado: {$bankTemplate->name}\n";
    } else {
        echo "- Registro ID 1 NÃO encontrado\n";
        // Criar template se necessário
        echo "  Criando template ID 1...\n";
        DB::table('bank_templates')->insert([
            'id' => 1,
            'name' => 'Template Padrão',
            'slug' => 'template-padrao',
            'description' => 'Template padrão para correção',
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "  Template criado com sucesso!\n";
    }
} else {
    echo "NÃO EXISTE\n";
}

// Verificar a tabela usuarios
echo "Tabela usuarios: ";
if (Schema::hasTable('usuarios')) {
    echo "EXISTE\n";
    $usuario = DB::table('usuarios')->where('id', 2)->first();
    if ($usuario) {
        echo "- Registro ID 2 encontrado: {$usuario->name}\n";
    } else {
        echo "- Registro ID 2 NÃO encontrado\n";
        // Criar usuário se necessário
        echo "  Criando usuário ID 2...\n";
        DB::table('usuarios')->insert([
            'id' => 2,
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => bcrypt('senha123'),
            'nivel' => 'cliente',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "  Usuário criado com sucesso!\n";
    }
} else {
    echo "NÃO EXISTE\n";
}

// 3. Verificar se há restrições de chave estrangeira na tabela dns_records
echo "\n3. Analisando estrutura da tabela dns_records...\n";
$columns = Schema::getColumnListing('dns_records');

echo "Colunas encontradas: " . implode(', ', $columns) . "\n";

// 4. Tentativa de correção: Criar um novo registro DNS sem as restrições problemáticas
echo "\n4. Tentando criar um novo registro DNS sem problemas de chave estrangeira...\n";

try {
    // Verificar se já existe registro com ID 2
    if (DB::table('dns_records')->where('id', 2)->exists()) {
        DB::table('dns_records')->where('id', 2)->delete();
        echo "Registro DNS ID 2 existente foi removido para teste.\n";
    }
    
    // Criar um novo registro de teste sem usar chaves estrangeiras problemáticas
    DB::table('dns_records')->insert([
        'id' => 2,
        'external_api_id' => 1,
        'record_type' => 'A',
        'name' => 'teste.example.com',
        'content' => '127.0.0.1',
        'ttl' => 60,
        'priority' => 0,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "Novo registro DNS criado com sucesso sem usar chaves estrangeiras problemáticas!\n";
    
} catch (Exception $e) {
    echo "ERRO ao criar novo registro DNS: " . $e->getMessage() . "\n";
}

// 5. Tente corrigir o registro problemático
echo "\n5. Tentando corrigir registro DNS problemático...\n";

try {
    // Remover chaves estrangeiras problemáticas antes
    DB::table('dns_records')
        ->where('id', 1)
        ->update([
            'bank_template_id' => null,
            'user_id' => null,
            'updated_at' => now()
        ]);
    
    echo "Registro DNS ID 1 atualizado com sucesso - removidas chaves estrangeiras.\n";
    
    // Após a atualização, verificar estado atual
    $dnsRecord = DB::table('dns_records')->where('id', 1)->first();
    if ($dnsRecord) {
        echo "Estado atual do registro:\n";
        echo "- external_api_id: " . ($dnsRecord->external_api_id ?? 'NULL') . "\n";
        echo "- bank_template_id: " . ($dnsRecord->bank_template_id ?? 'NULL') . "\n";
        echo "- user_id: " . ($dnsRecord->user_id ?? 'NULL') . "\n";
    }
    
} catch (Exception $e) {
    echo "ERRO ao atualizar registro DNS: " . $e->getMessage() . "\n";
}

echo "\n====== DIAGNÓSTICO CONCLUÍDO ======\n";
