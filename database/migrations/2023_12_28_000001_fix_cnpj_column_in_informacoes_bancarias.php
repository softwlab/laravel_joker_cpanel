<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar se a coluna cnpj existe na tabela informacoes_bancarias
        if (Schema::hasColumn('informacoes_bancarias', 'cnpj')) {
            // A coluna já existe, não precisamos fazer nada
            // Apenas garantimos que qualquer migração existente que tente adicioná-la novamente será ignorada
            $this->markMigrationAsCompleted('migration_temp');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não é necessário fazer nada no método down
    }

    /**
     * Marca uma migração específica como concluída para evitar que ela seja executada novamente
     */
    protected function markMigrationAsCompleted($migration)
    {
        // Verifica se a migração já está marcada como concluída
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        
        // Se não estiver, adiciona-a como concluída
        if (!$exists) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => DB::table('migrations')->max('batch')
            ]);
        }
    }
};
