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
        Schema::create('cloudflare_domain_usuario', function (Blueprint $table) {
            $table->id();
            
            // Chaves estrangeiras
            $table->unsignedBigInteger('cloudflare_domain_id');
            $table->unsignedBigInteger('usuario_id');
            
            // Campos da associação
            $table->string('status')->default('active');
            $table->json('config')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Índices e Chaves
            $table->foreign('cloudflare_domain_id')->references('id')->on('cloudflare_domains')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            
            // Evitar duplicação de associações
            $table->unique(['cloudflare_domain_id', 'usuario_id'], 'domain_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloudflare_domain_usuario');
    }
};
