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
        // Renomear user_id para usuario_id na tabela link_groups
        Schema::table('link_groups', function (Blueprint $table) {
            // Remover a constraint de chave estrangeira existente
            $table->dropForeign(['user_id']);
            
            // Renomear a coluna
            $table->renameColumn('user_id', 'usuario_id');
        });
        
        // Recriar a constraint de chave estrangeira
        Schema::table('link_groups', function (Blueprint $table) {
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->index('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter alterações da tabela link_groups
        Schema::table('link_groups', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->renameColumn('usuario_id', 'user_id');
        });
        
        Schema::table('link_groups', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->index('user_id');
        });
    }
};
