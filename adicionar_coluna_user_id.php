<?php

// Script para adicionar a coluna user_id à tabela template_user_configs
// Executar com: php adicionar_coluna_user_id.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = \DB::connection();

// Desativar temporariamente chaves estrangeiras
$db->statement('PRAGMA foreign_keys = OFF;');

// Verificar se a tabela existe
$tableExists = $db->select("SELECT name FROM sqlite_master WHERE type='table' AND name='template_user_configs'");

if (count($tableExists) === 0) {
    echo "A tabela template_user_configs não existe. Criando tabela...\n";
    try {
        $db->statement('
            CREATE TABLE template_user_configs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                template_id INTEGER NOT NULL,
                record_id INTEGER NOT NULL,
                config TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
                FOREIGN KEY (template_id) REFERENCES bank_templates(id) ON DELETE CASCADE,
                FOREIGN KEY (record_id) REFERENCES dns_records(id) ON DELETE CASCADE
            )
        ');
        echo "Tabela template_user_configs criada com sucesso!\n";
    } catch (\Exception $e) {
        echo "Erro ao criar tabela: " . $e->getMessage() . "\n";
    }
} else {
    echo "Verificando estrutura atual da tabela template_user_configs...\n";
    $columns = $db->select('PRAGMA table_info(template_user_configs);');
    $columnNames = [];

    foreach ($columns as $column) {
        $columnNames[] = $column->name;
        echo "- {$column->name} ({$column->type})" . ($column->notnull ? " NOT NULL" : "") . "\n";
    }

    // Verificar e adicionar a coluna user_id se necessário
    if (!in_array('user_id', $columnNames)) {
        echo "Adicionando coluna user_id à tabela template_user_configs...\n";
        
        // Para SQLite, precisamos criar uma nova tabela com o schema atualizado e migrar dados
        try {
            // 1. Criar tabela temporária com o novo schema
            $db->statement('
                CREATE TABLE template_user_configs_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    template_id INTEGER NOT NULL,
                    record_id INTEGER NOT NULL,
                    config TEXT NULL,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE,
                    FOREIGN KEY (template_id) REFERENCES bank_templates(id) ON DELETE CASCADE,
                    FOREIGN KEY (record_id) REFERENCES dns_records(id) ON DELETE CASCADE
                )
            ');
            
            // 2. Copiar dados existentes, definindo user_id com base no record_id
            // Assumindo que cada record_id está associado a um único user_id em dns_records
            echo "Migrando dados existentes...\n";
            $configs = $db->select('SELECT * FROM template_user_configs');
            
            foreach ($configs as $config) {
                $userId = $db->table('dns_records')
                    ->where('id', $config->record_id)
                    ->value('user_id');
                
                if (!$userId) {
                    echo "Aviso: Não foi encontrado user_id para o record_id {$config->record_id}. Usando user_id = 1 como fallback.\n";
                    $userId = 1; // Fallback para admin
                }
                
                $db->table('template_user_configs_new')->insert([
                    'id' => $config->id,
                    'user_id' => $userId,
                    'template_id' => $config->template_id,
                    'record_id' => $config->record_id,
                    'config' => $config->config,
                    'created_at' => $config->created_at,
                    'updated_at' => $config->updated_at
                ]);
            }
            
            // 3. Remover tabela antiga
            $db->statement('DROP TABLE template_user_configs');
            
            // 4. Renomear a nova tabela para o nome original
            $db->statement('ALTER TABLE template_user_configs_new RENAME TO template_user_configs');
            
            echo "Coluna user_id adicionada com sucesso!\n";
        } catch (\Exception $e) {
            echo "Erro ao adicionar coluna user_id: " . $e->getMessage() . "\n";
        }
    } else {
        echo "A coluna user_id já existe na tabela template_user_configs.\n";
    }
}

// Reativar chaves estrangeiras
$db->statement('PRAGMA foreign_keys = ON;');

echo "\nVerificando estrutura final da tabela template_user_configs...\n";
$columns = $db->select('PRAGMA table_info(template_user_configs);');
foreach ($columns as $column) {
    echo "- {$column->name} ({$column->type})" . ($column->notnull ? " NOT NULL" : "") . "\n";
}

echo "\nScript concluído!\n";
