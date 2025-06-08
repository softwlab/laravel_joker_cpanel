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
        Schema::create('template_user_configs', function (Blueprint $table) {
            $table->id();
            
            // Chaves estrangeiras
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('record_id')->nullable();
            
            // Configurações dos campos (ativos e ordem)
            $table->json('config');
            
            $table->timestamps();
            
            // Índices e chaves estrangeiras
            $table->index('user_id');
            $table->index('template_id');
            $table->index('record_id');
            
            // Chave única para evitar duplicações
            $table->unique(['user_id', 'template_id', 'record_id'], 'user_template_record_unique');
            
            // Relacionamentos
            $table->foreign('user_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('bank_templates')->onDelete('cascade');
            $table->foreign('record_id')->references('id')->on('dns_records')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_user_configs');
    }
};
