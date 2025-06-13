<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro, vamos desabilitar a verificação de chaves estrangeiras
        Schema::disableForeignKeyConstraints();
        
        try {
            // Criamos tabela temporária com a estrutura correta
            Schema::create('dns_records_temp', function (Blueprint $table) {
                $table->id();
                $table->foreignId('external_api_id')->constrained('external_apis')->onDelete('cascade');
                $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null')->comment('Link Bancário relacionado');
                $table->foreignId('bank_template_id')->nullable()->constrained('bank_templates')->onDelete('set null')->comment('Instituição Bancária relacionada');
                $table->foreignId('link_group_id')->nullable()->constrained('link_groups')->onDelete('set null')->comment('Grupo Organizado relacionado');
                
                // A mudança principal: agora user_id aponta para a tabela 'usuarios'
                $table->foreignId('user_id')->nullable()->constrained('usuarios')->onDelete('set null');
                
                $table->string('record_type')->comment('Tipo de registro DNS (A, MX, TXT, etc)');
                $table->string('name')->comment('Nome/host do registro');
                $table->text('content')->comment('Conteúdo do registro');
                $table->integer('ttl')->default(3600)->comment('Time to Live em segundos');
                $table->integer('priority')->nullable()->comment('Prioridade para registros MX');
                $table->string('status')->default('active');
                $table->json('extra_data')->nullable()->comment('Dados adicionais em formato JSON');
                $table->timestamps();
                
                // Índices para melhorar performance
                $table->index(['external_api_id', 'record_type']);
                $table->index(['bank_id']);
                $table->index(['bank_template_id']);
                $table->index(['link_group_id']);
                $table->index(['user_id']);
            });
            
            // Copiamos os dados da tabela original para a temporária
            // Excluindo o user_id que está causando o problema
            $records = DB::table('dns_records')
                ->select(
                    'id', 
                    'external_api_id', 
                    'bank_id', 
                    'bank_template_id', 
                    'link_group_id',
                    'record_type', 
                    'name', 
                    'content', 
                    'ttl', 
                    'priority', 
                    'status', 
                    'extra_data', 
                    'created_at', 
                    'updated_at'
                )
                ->get();
            
            foreach ($records as $record) {
                $data = (array)$record;
                // Inserimos com user_id nulo por enquanto
                $data['user_id'] = null;
                DB::table('dns_records_temp')->insert($data);
            }
            
            // Agora renomeamos as tabelas
            Schema::rename('dns_records', 'dns_records_old');
            Schema::rename('dns_records_temp', 'dns_records');
            
            // Registros em log para depuração
            \Illuminate\Support\Facades\Log::info('Tabela dns_records recriada com chave estrangeira correta para usuarios');
            
            // Apagamos a tabela antiga
            Schema::dropIfExists('dns_records_old');
        } finally {
            // Reabilitamos a verificação de chaves estrangeiras
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\Log::info('Não é possível reverter a recriação da tabela dns_records');
        // Não fazemos nada no down, pois seria complexo restaurar a estrutura anterior
    }
};
