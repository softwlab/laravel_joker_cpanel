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
        Schema::create('dns_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_api_id')->constrained('external_apis')->onDelete('cascade');
            $table->foreignId('bank_id')->nullable()->constrained('banks')->onDelete('set null')->comment('Link Bancário relacionado');
            $table->foreignId('bank_template_id')->nullable()->constrained('bank_templates')->onDelete('set null')->comment('Instituição Bancária relacionada');
            $table->foreignId('link_group_id')->nullable()->constrained('link_groups')->onDelete('set null')->comment('Grupo Organizado relacionado');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns_records');
    }
};
