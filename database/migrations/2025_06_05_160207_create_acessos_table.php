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
        Schema::create('acessos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('ip');
            $table->text('user_agent')->nullable();
            $table->timestamp('data_acesso');
            $table->timestamp('ultimo_acesso')->nullable();
            $table->timestamps();
            
            $table->index('usuario_id');
            $table->index('data_acesso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acessos');
    }
};
