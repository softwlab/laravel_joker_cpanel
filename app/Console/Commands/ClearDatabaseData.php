<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearDatabaseData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-database-data {--force : Executar sem pedir confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpar todos os dados de todas as tabelas do banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('ATENÇÃO: Esta ação irá excluir TODOS os dados do banco de dados. Deseja continuar?')) {
            $this->info('Operação cancelada.');
            return;
        }
        
        // Desabilitar verificação de chaves estrangeiras temporariamente
        DB::statement('PRAGMA foreign_keys = OFF');
        
        // Obter todas as tabelas do banco de dados
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name NOT LIKE 'migrations';");
        
        foreach ($tables as $table) {
            $tableName = $table->name;
            $this->info("Limpando tabela: {$tableName}");
            DB::table($tableName)->truncate();
        }
        
        // Reabilitar verificação de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = ON');
        
        $this->info('Todos os dados foram removidos com sucesso!');
    }
}
