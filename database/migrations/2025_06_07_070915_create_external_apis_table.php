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
        Schema::create('external_apis', function (Blueprint $table) {
            $table->id();
            $table->string('external_link_api')->comment('URL da API externa');
            $table->string('key_external_api')->comment('Chave de autenticação da API');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('json')->nullable()->comment('Configurações adicionais em formato JSON');
            $table->string('type')->comment('Tipo da API (cloudflare, etc)');
            $table->string('name')->comment('Nome amigável para a API');
            $table->text('description')->nullable()->comment('Descrição da API');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_apis');
    }
};
