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
        Schema::table('dns_records', function (Blueprint $table) {
            // Verificamos se a chave estrangeira existe antes de tentar removê-la
            if (Schema::hasColumn('dns_records', 'user_id')) {
                // Removemos qualquer chave estrangeira existente no user_id
                try {
                    // No SQLite precisamos desabilitar verificação de chaves estrangeiras primeiro
                    Schema::disableForeignKeyConstraints();
                    
                    // Tentativa de remover a chave estrangeira específica
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Log do erro, mas continuamos
                    \Illuminate\Support\Facades\Log::info('Erro ao remover foreign key user_id: ' . $e->getMessage());
                } finally {
                    // Garantimos que as chaves estrangeiras serão reativadas
                    Schema::enableForeignKeyConstraints();
                }
                
                // Adicionamos a chave estrangeira correta para a tabela 'usuarios'
                // Removemos a coluna primeiro para recriá-la com a referência correta
                $table->dropColumn('user_id');
                
                // Agora adicionamos a coluna novamente com a referência correta
                $table->foreignId('user_id')->nullable()->constrained('usuarios')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dns_records', function (Blueprint $table) {
            // No rollback, não restauramos a chave estrangeira incorreta
            // apenas logamos a operação
            \Illuminate\Support\Facades\Log::info('Rollback da migração de correção da foreign key user_id');
        });
    }
};
