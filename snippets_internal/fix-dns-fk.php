<?php

require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Iniciando correção de chaves estrangeiras...\n";

try {
    // 1. Verificar as tabelas e suas estruturas
    echo "Verificando tabelas e estruturas...\n";
    
    // Verificar tabela dns_records
    if (!Schema::hasTable('dns_records')) {
        throw new Exception("Tabela dns_records não existe!");
    }
    
    // Verificando tabela usuarios
    if (!Schema::hasTable('usuarios')) {
        throw new Exception("Tabela usuarios não existe!");
    }
    
    // Verificando tabela bank_templates
    if (!Schema::hasTable('bank_templates')) {
        throw new Exception("Tabela bank_templates não existe!");
    }
    
    echo "Todas as tabelas existem.\n";
    
    // 2. Verificar usuários
    echo "\nVerificando usuário ID 2...\n";
    $usuario = DB::table('usuarios')->where('id', 2)->first();
    
    if ($usuario) {
        echo "Usuário ID 2 encontrado: {$usuario->nome}\n";
    } else {
        echo "Usuário ID 2 não encontrado. Criando...\n";
        DB::table('usuarios')->insert([
            'id' => 2,
            'nome' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'senha' => bcrypt('senha123'),
            'nivel' => 'cliente',
            'ativo' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "Usuário ID 2 criado com sucesso!\n";
    }
    
    // 3. Verificar template
    echo "\nVerificando template ID 1...\n";
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
    
    // 4. Analisar registro DNS ID 1
    echo "\nVerificando registro DNS ID 1...\n";
    $dnsRecord = DB::table('dns_records')->where('id', 1)->first();
    
    if (!$dnsRecord) {
        echo "Registro DNS ID 1 não encontrado.\n";
        exit;
    }
    
    echo "Registro DNS encontrado:\n";
    echo "- Nome: {$dnsRecord->name}\n";
    echo "- Tipo: {$dnsRecord->record_type}\n";
    echo "- external_api_id: " . ($dnsRecord->external_api_id ?? 'NULL') . "\n";
    echo "- bank_template_id: " . ($dnsRecord->bank_template_id ?? 'NULL') . "\n";
    echo "- user_id: " . ($dnsRecord->user_id ?? 'NULL') . "\n";
    
    // 5. Analisar restrições de chave estrangeira
    echo "\nAnalisando chaves estrangeiras em dns_records...\n";
    $foreignKeys = DB::select("PRAGMA foreign_key_list(dns_records)");
    
    foreach ($foreignKeys as $fk) {
        echo "FK: {$fk->from} -> {$fk->table}({$fk->to})\n";
    }
    
    // 6. Correção: Desativar restrições e atualizar o registro
    echo "\nCorrigindo registro DNS ID 1...\n";
    
    // Desativar restrições de chave estrangeira temporariamente
    DB::statement('PRAGMA foreign_keys = OFF;');
    
    try {
        // Limpar valores problemáticos
        DB::table('dns_records')
            ->where('id', 1)
            ->update([
                'bank_template_id' => null,
                'user_id' => null
            ]);
        
        echo "Valores limpos com sucesso.\n";
        
        // Agora inserir os valores corretos
        DB::table('dns_records')
            ->where('id', 1)
            ->update([
                'bank_template_id' => 1,
                'user_id' => 2
            ]);
        
        echo "Valores atualizados com sucesso.\n";
        
        // Verificar estado atual
        $dnsRecord = DB::table('dns_records')->where('id', 1)->first();
        echo "Estado atual do registro:\n";
        echo "- bank_template_id: " . ($dnsRecord->bank_template_id ?? 'NULL') . "\n";
        echo "- user_id: " . ($dnsRecord->user_id ?? 'NULL') . "\n";
        
    } catch (Exception $e) {
        echo "Erro ao atualizar registro: " . $e->getMessage() . "\n";
    } finally {
        // Reativar restrições de chave estrangeira
        DB::statement('PRAGMA foreign_keys = ON;');
        echo "Restrições de chave estrangeira reativadas.\n";
    }
    
    echo "\nProcesso de correção concluído!\n";
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
