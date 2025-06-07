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
            $table->foreignId('cloudflare_domain_id')->constrained('cloudflare_domains')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('status')->default('active');
            $table->json('config')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Garante que um domínio não esteja associado mais de uma vez ao mesmo usuário
            $table->unique(['cloudflare_domain_id', 'usuario_id']);
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
