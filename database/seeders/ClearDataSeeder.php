<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearDataSeeder extends Seeder
{
    /**
     * Limpar dados antigos das tabelas relacionadas à arquitetura banco-cliente.
     * Preserva os usuários e outras tabelas não relacionadas.
     */
    public function run(): void
    {
        $this->command->info('Limpando dados antigos...');
        
        // Desabilita as restrições de chave estrangeira para SQLite
        DB::statement('PRAGMA foreign_keys = OFF');
        
        // Tabelas a serem limpas
        $tables = [
            'link_group_banks', // Tabela pivô entre grupos e bancos
            'link_group_items', // Itens dos grupos de links
            'link_groups',      // Grupos de links
            'banks',            // Links bancários
            'bank_template_fields', // Campos dos templates
            'bank_templates',   // Templates de bancos
            'usuarios',         // Usuários do sistema
            'users'             // Tabela de usuários do Laravel
        ];
        
        // Limpa cada tabela se existir
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // No SQLite, DELETE FROM é equivalente ao TRUNCATE em outros DBs
                DB::statement("DELETE FROM {$table}");
                // Resetar o auto-incremento para SQLite
                DB::statement("DELETE FROM sqlite_sequence WHERE name='{$table}'");
                $this->command->info("Tabela {$table} limpa com sucesso!");
            } else {
                $this->command->warn("Tabela {$table} não encontrada. Pulando.");
            }
        }
        
        // Reabilita as restrições de chave estrangeira
        DB::statement('PRAGMA foreign_keys = ON');
        
        $this->command->info('✓ Dados antigos removidos com sucesso!');
    }
}
