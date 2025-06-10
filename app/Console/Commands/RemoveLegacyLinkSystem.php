<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RemoveLegacyLinkSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:legacy-links {--force : Força a remoção sem confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove completamente o sistema legado de links bancários';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Removendo Sistema Legado de Links Bancários ===');
        
        if (!$this->option('force') && !$this->confirm('Esta ação vai remover PERMANENTEMENTE todas as tabelas e dados relacionados ao sistema legado de links bancários. Deseja continuar?')) {
            $this->info('Operação cancelada pelo usuário.');
            return 0;
        }
        
        $this->info('Iniciando processo de remoção...');
        
        // 1. Remover índices da tabela visitantes relacionados aos campos legados
        $this->removeIndices();
        
        // 2. Remover colunas legadas da tabela visitantes
        $this->removeLegacyColumns();
        
        // 3. Remover tabelas legadas
        $this->removeLegacyTables();
        
        // 4. Remover configurações do sistema relacionadas ao sistema legado
        $this->removeSystemConfigs();
        
        // 5. Limpar cache
        $this->call('cache:clear');
        $this->call('config:clear');
        
        $this->info('Sistema legado de links bancários removido com sucesso!');
        return 0;
    }
    
    /**
     * Remove os índices da tabela visitantes relacionados aos campos legados
     */
    private function removeIndices()
    {
        $this->info('Removendo índices...');
        
        try {
            $driver = DB::connection()->getDriverName();
            
            if ($driver === 'mysql') {
                // Lógica para MySQL
                $indexes = DB::select("SHOW INDEX FROM visitantes WHERE Column_name IN ('link_id', 'migrated_to_dns')");
                
                $processedIndexes = [];
                foreach ($indexes as $index) {
                    if (!in_array($index->Key_name, $processedIndexes) && $index->Key_name !== 'PRIMARY') {
                        DB::statement("DROP INDEX {$index->Key_name} ON visitantes");
                        $processedIndexes[] = $index->Key_name;
                        $this->line(" - Índice <comment>{$index->Key_name}</comment> removido.");
                    }
                }
            } else if ($driver === 'sqlite') {
                // Lógica para SQLite
                $indexes = DB::select("PRAGMA index_list('visitantes')");
                
                foreach ($indexes as $index) {
                    $indexInfo = DB::select("PRAGMA index_info('{$index->name}')");
                    $isTargetIndex = false;
                    
                    foreach ($indexInfo as $column) {
                        if (in_array($column->name, ['link_id', 'migrated_to_dns'])) {
                            $isTargetIndex = true;
                            break;
                        }
                    }
                    
                    if ($isTargetIndex) {
                        DB::statement("DROP INDEX IF EXISTS {$index->name}");
                        $this->line(" - Índice <comment>{$index->name}</comment> removido.");
                    }
                }
            }
            
            // Tentativa direta de remover índices conhecidos (fallback)
            try {
                DB::statement("DROP INDEX IF EXISTS visitantes_link_id_index");
                DB::statement("DROP INDEX IF EXISTS visitantes_migrated_to_dns_index");
            } catch (Exception $e) {
                // Ignora erro caso o índice não exista
            }
            
            $this->info('Remoção de índices concluída.');
        } catch (Exception $e) {
            $this->warn("Erro ao remover índices: {$e->getMessage()}");
            Log::warning("Erro ao remover índices: {$e->getMessage()}");
            
            if (!$this->confirm('Deseja continuar mesmo com o erro?')) {
                throw $e;
            }
        }
    }
    
    /**
     * Remove colunas legadas da tabela visitantes
     */
    private function removeLegacyColumns()
    {
        $this->info('Removendo colunas da tabela visitantes...');
        
        try {
            // Remover chaves estrangeiras primeiro
            try {
                $driver = DB::connection()->getDriverName();
                
                if ($driver === 'mysql') {
                    $foreignKeys = DB::select(
                        "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                         WHERE TABLE_NAME = 'visitantes' 
                         AND COLUMN_NAME = 'link_id' 
                         AND CONSTRAINT_NAME != 'PRIMARY' 
                         AND REFERENCED_TABLE_NAME IS NOT NULL"
                    );
                    
                    foreach ($foreignKeys as $fk) {
                        DB::statement("ALTER TABLE visitantes DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
                        $this->line(" - Chave estrangeira <comment>{$fk->CONSTRAINT_NAME}</comment> removida.");
                    }
                }
            } catch (Exception $e) {
                $this->warn("Aviso ao remover chaves estrangeiras: {$e->getMessage()}");
            }
            
            // Remover colunas
            if (Schema::hasColumn('visitantes', 'link_id')) {
                Schema::table('visitantes', function ($table) {
                    $table->dropColumn('link_id');
                });
                $this->line(" - Coluna <comment>link_id</comment> removida.");
            } else {
                $this->line(" - Coluna <comment>link_id</comment> não encontrada.");
            }
            
            if (Schema::hasColumn('visitantes', 'migrated_to_dns')) {
                Schema::table('visitantes', function ($table) {
                    $table->dropColumn('migrated_to_dns');
                });
                $this->line(" - Coluna <comment>migrated_to_dns</comment> removida.");
            } else {
                $this->line(" - Coluna <comment>migrated_to_dns</comment> não encontrada.");
            }
            
            $this->info('Colunas removidas com sucesso.');
        } catch (Exception $e) {
            $this->warn("Erro ao remover colunas: {$e->getMessage()}");
            Log::warning("Erro ao remover colunas: {$e->getMessage()}");
            
            if (!$this->confirm('Deseja continuar mesmo com o erro?')) {
                throw $e;
            }
        }
    }
    
    /**
     * Remove tabelas legadas do sistema de links bancários
     */
    private function removeLegacyTables()
    {
        $this->info('Removendo tabelas legadas...');
        
        $tables = [
            'link_group_items',
            'link_group_banks',
            'link_groups'
        ];
        
        foreach ($tables as $table) {
            try {
                if (Schema::hasTable($table)) {
                    Schema::dropIfExists($table);
                    $this->line(" - Tabela <comment>{$table}</comment> removida com sucesso.");
                } else {
                    $this->line(" - Tabela <comment>{$table}</comment> não encontrada.");
                }
            } catch (Exception $e) {
                $this->warn("Erro ao remover tabela {$table}: {$e->getMessage()}");
                Log::error("Erro ao remover tabela {$table}: {$e->getMessage()}");
                
                if (!$this->confirm('Deseja continuar mesmo com o erro?')) {
                    throw $e;
                }
            }
        }
        
        $this->info('Tabelas removidas com sucesso.');
    }
    
    /**
     * Remove configurações do sistema relacionadas ao sistema legado
     */
    private function removeSystemConfigs()
    {
        $this->info('Removendo configurações do sistema...');
        
        try {
            $configs = [
                'has_legacy_links',
                'legacy_links_deprecated',
                'legacy_links_migration_status'
            ];
            
            foreach ($configs as $config) {
                $count = DB::table('system_configs')->where('key', $config)->delete();
                
                if ($count > 0) {
                    $this->line(" - Configuração <comment>{$config}</comment> removida.");
                }
            }
            
            $this->info('Configurações do sistema atualizadas.');
        } catch (Exception $e) {
            $this->warn("Erro ao atualizar configurações do sistema: {$e->getMessage()}");
            Log::warning("Erro ao atualizar configurações do sistema: {$e->getMessage()}");
            
            if (!$this->confirm('Deseja continuar mesmo com o erro?')) {
                throw $e;
            }
        }
    }
}
