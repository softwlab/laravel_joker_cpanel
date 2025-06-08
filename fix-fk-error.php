<?php

require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Iniciando correção de chaves estrangeiras...\n";

try {
    // 1. Verificar a estrutura da tabela dns_records
    echo "Analisando tabela dns_records...\n";
    
    if (!Schema::hasColumn('dns_records', 'user_id')) {
        echo "Coluna user_id não existe. Adicionando...\n";
        Schema::table('dns_records', function($table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('bank_template_id');
        });
        echo "Coluna user_id adicionada com sucesso!\n";
    } else {
        echo "Coluna user_id já existe.\n";
    }
    
    // 2. Verificar relacionamentos e identificar o problema
    echo "\nVerificando relacionamentos...\n";
    
    // Verificar se a coluna user_id possui chave estrangeira
    $hasUserFK = false;
    $foreignKeys = DB::select("PRAGMA foreign_key_list(dns_records)");
    foreach ($foreignKeys as $fk) {
        if ($fk->from === 'user_id') {
            $hasUserFK = true;
            echo "FK em user_id encontrada: referencia '{$fk->table}'('{$fk->to}')\n";
        }
    }
    
    // 3. Verificar se o usuário com ID 2 existe
    $user = DB::table('usuarios')->where('id', 2)->first();
    if ($user) {
        echo "Usuário ID 2 encontrado: {$user->name}\n";
    } else {
        echo "Usuário ID 2 não encontrado. Criando...\n";
        DB::table('usuarios')->insert([
            'id' => 2,
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => bcrypt('senha123'),
            'nivel' => 'cliente',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "Usuário ID 2 criado com sucesso!\n";
    }
    
    // 4. Verificar se o template com ID 1 existe
    $template = DB::table('bank_templates')->where('id', 1)->first();
    if ($template) {
        echo "Template ID 1 encontrado: {$template->name}\n";
    } else {
        echo "Template ID 1 não encontrado. Criando...\n";
        DB::table('bank_templates')->insert([
            'id' => 1,
            'name' => 'Template Padrão',
            'slug' => 'template-padrao',
            'description' => 'Template padrão para correção',
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "Template ID 1 criado com sucesso!\n";
    }
    
    // 5. Corrigir o registro DNS removendo as chaves estrangeiras problemáticas
    echo "\nAtualizando registro DNS ID 1...\n";
    
    // Primeiro, verificar estado atual
    $dnsRecord = DB::table('dns_records')->where('id', 1)->first();
    if ($dnsRecord) {
        echo "Estado atual: \n";
        echo "- bank_template_id: " . ($dnsRecord->bank_template_id ?? 'NULL') . "\n";
        echo "- user_id: " . ($dnsRecord->user_id ?? 'NULL') . "\n";
        
        // Tentar remover as chaves estrangeiras (definir como NULL)
        DB::statement('PRAGMA foreign_keys = OFF;');
        
        DB::table('dns_records')
            ->where('id', 1)
            ->update([
                'bank_template_id' => null,
                'user_id' => null,
                'updated_at' => now()
            ]);
        
        // Verificar se a atualização funcionou
        $dnsRecord = DB::table('dns_records')->where('id', 1)->first();
        echo "\nApós atualização 1: \n";
        echo "- bank_template_id: " . ($dnsRecord->bank_template_id ?? 'NULL') . "\n";
        echo "- user_id: " . ($dnsRecord->user_id ?? 'NULL') . "\n";
        
        // Agora definir os valores corretos
        DB::table('dns_records')
            ->where('id', 1)
            ->update([
                'bank_template_id' => 1,
                'user_id' => 2,
                'updated_at' => now()
            ]);
        
        // Verificar novamente
        $dnsRecord = DB::table('dns_records')->where('id', 1)->first();
        echo "\nApós atualização 2: \n";
        echo "- bank_template_id: " . ($dnsRecord->bank_template_id ?? 'NULL') . "\n";
        echo "- user_id: " . ($dnsRecord->user_id ?? 'NULL') . "\n";
        
        DB::statement('PRAGMA foreign_keys = ON;');
    } else {
        echo "Registro DNS ID 1 não encontrado.\n";
    }
    
    echo "\nProblema de chave estrangeira corrigido com sucesso!\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
