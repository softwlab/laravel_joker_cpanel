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
        Schema::create('informacoes_bancarias', function (Blueprint $table) {
            $table->id();
            $table->uuid('visitante_uuid');
            $table->foreign('visitante_uuid')->references('uuid')->on('visitantes')->onDelete('cascade');
            $table->date('data')->nullable();
            $table->string('agencia')->nullable();
            $table->string('conta')->nullable();
            $table->string('cpf')->nullable();
            $table->string('nome_completo')->nullable();
            $table->string('telefone')->nullable();
            $table->json('informacoes_adicionais')->nullable();
            $table->timestamps();
            
            // Índices para facilitar consultas
            $table->index('visitante_uuid');
            $table->index('cpf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informacoes_bancarias');
    }
};
