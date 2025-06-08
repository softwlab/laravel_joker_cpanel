<?php

require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Corrigindo referência da chave estrangeira em user_id...\n";

try {
    // Verificar as chaves estrangeiras atuais
    echo "Verificando chaves estrangeiras atuais:\n";
    $foreignKeys = DB::select("PRAGMA foreign_key_list(dns_records)");
    
    foreach ($foreignKeys as $fk) {
        echo "- {$fk->from} -> {$fk->table}({$fk->to})\n";
    }
    
    // Desativar restrições de chave estrangeira
    DB::statement('PRAGMA foreign_keys = OFF;');
    
    // Corrigir a referência de user_id para apontar para a tabela usuarios
    echo "\nRecriando a tabela dns_records com a referência correta...\n";
    
    // Criar tabela temporária para backup
    Schema::create('dns_records_backup', function (Blueprint $table) {
        $table->id();
        $table->foreignId('external_api_id')->nullable()->constrained('external_apis');
        $table->foreignId('bank_id')->nullable()->constrained('banks');
        $table->foreignId('bank_template_id')->nullable()->constrained('bank_templates');
        $table->foreignId('link_group_id')->nullable()->constrained('link_groups');
        $table->unsignedBigInteger('user_id')->nullable(); // Vamos adicionar a restrição depois
        $table->string('record_type');
        $table->string('name');
        $table->text('content');
        $table->integer('ttl')->default(3600);
        $table->integer('priority')->default(0);
        $table->string('status')->default('active');
        $table->json('extra_data')->nullable();
        $table->timestamps();
    });
    
    // Copiar os dados para a tabela de backup
    DB::statement('INSERT INTO dns_records_backup SELECT * FROM dns_records');
    
    // Remover a tabela original
    Schema::drop('dns_records');
    
    // Recriar a tabela com a referência correta
    Schema::create('dns_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('external_api_id')->nullable()->constrained('external_apis');
        $table->foreignId('bank_id')->nullable()->constrained('banks');
        $table->foreignId('bank_template_id')->nullable()->constrained('bank_templates');
        $table->foreignId('link_group_id')->nullable()->constrained('link_groups');
        $table->unsignedBigInteger('user_id')->nullable();
        $table->foreign('user_id')->references('id')->on('usuarios'); // Referência correta para usuarios
        $table->string('record_type');
        $table->string('name');
        $table->text('content');
        $table->integer('ttl')->default(3600);
        $table->integer('priority')->default(0);
        $table->string('status')->default('active');
        $table->json('extra_data')->nullable();
        $table->timestamps();
    });
    
    // Copiar os dados de volta
    DB::statement('INSERT INTO dns_records SELECT * FROM dns_records_backup');
    
    // Remover a tabela de backup
    Schema::drop('dns_records_backup');
    
    // Reativar restrições de chave estrangeira
    DB::statement('PRAGMA foreign_keys = ON;');
    
    echo "Tabela dns_records recriada com sucesso!\n";
    
    // Verificar as novas chaves estrangeiras
    echo "\nVerificando novas chaves estrangeiras:\n";
    $foreignKeys = DB::select("PRAGMA foreign_key_list(dns_records)");
    
    foreach ($foreignKeys as $fk) {
        echo "- {$fk->from} -> {$fk->table}({$fk->to})\n";
    }
    
    echo "\nRestrição de chave estrangeira corrigida com sucesso!\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    
    // Em caso de erro, tentar reativar as chaves estrangeiras
    try {
        DB::statement('PRAGMA foreign_keys = ON;');
    } catch (Exception $inner) {
        // Ignorar erros ao reativar
    }
}
