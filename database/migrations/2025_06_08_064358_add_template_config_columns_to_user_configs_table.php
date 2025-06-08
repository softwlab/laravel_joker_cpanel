<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_configs', function (Blueprint $table) {
            // Renomear coluna usuario_id para user_id se ela existir
            if (Schema::hasColumn('user_configs', 'usuario_id')) {
                $table->renameColumn('usuario_id', 'user_id');
            } else {
                // Se não existir, criar a coluna user_id
                $table->unsignedBigInteger('user_id')->nullable()->index();
            }
            
            // Adicionar coluna para template_id
            $table->unsignedBigInteger('template_id')->nullable()->index();
            
            // Adicionar coluna para record_id
            $table->unsignedBigInteger('record_id')->nullable()->index();
            
            // Adicionar coluna para configuração de campos do template
            $table->json('config')->nullable();
            
            // Chave composta para garantir a unicidade da configuração por usuário, template e registro
            $table->unique(['user_id', 'template_id', 'record_id'], 'user_template_record_unique');
            
            // Relações com outras tabelas
            $table->foreign('template_id')->references('id')->on('bank_templates')->onDelete('cascade');
            $table->foreign('record_id')->references('id')->on('dns_records')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_configs', function (Blueprint $table) {
            // Remover chave estrangeira e chave única
            $table->dropForeign(['template_id']);
            $table->dropForeign(['record_id']);
            $table->dropUnique('user_template_record_unique');
            
            // Remover colunas adicionadas
            $table->dropColumn(['template_id', 'record_id', 'config']);
            
            // Renomear user_id de volta para usuario_id se necessário
            if (Schema::hasColumn('user_configs', 'user_id')) {
                $table->renameColumn('user_id', 'usuario_id');
            }
        });
    }
};
