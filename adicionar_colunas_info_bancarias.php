<?php

// Script para adicionar as colunas faltantes à tabela informacoes_bancarias
// Executar com: php adicionar_colunas_info_bancarias.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = \DB::connection();

// Desativar temporariamente chaves estrangeiras
$db->statement('PRAGMA foreign_keys = OFF;');
    
echo "Verificando estrutura atual da tabela informacoes_bancarias...\n";
$columns = $db->select('PRAGMA table_info(informacoes_bancarias);');
$columnNames = [];

foreach ($columns as $column) {
    $columnNames[] = $column->name;
    echo "- {$column->name} ({$column->type})" . ($column->notnull ? " NOT NULL" : "") . "\n";
}

// Verificar e adicionar as colunas faltantes
$columnsToAdd = [
    'cnpj' => 'varchar',
    'email' => 'varchar',
    'dni' => 'varchar'
];

foreach ($columnsToAdd as $columnName => $columnType) {
    if (!in_array($columnName, $columnNames)) {
        echo "Adicionando coluna {$columnName} à tabela informacoes_bancarias...\n";
        try {
            $db->statement("ALTER TABLE informacoes_bancarias ADD COLUMN {$columnName} {$columnType} NULL");
            echo "Coluna {$columnName} adicionada com sucesso!\n";
        } catch (\Exception $e) {
            echo "Erro ao adicionar a coluna {$columnName}: " . $e->getMessage() . "\n";
        }
    } else {
        echo "A coluna {$columnName} já existe na tabela informacoes_bancarias.\n";
    }
}

// Reativar chaves estrangeiras
$db->statement('PRAGMA foreign_keys = ON;');

echo "\nVerificando estrutura final da tabela informacoes_bancarias...\n";
$columns = $db->select('PRAGMA table_info(informacoes_bancarias);');
foreach ($columns as $column) {
    echo "- {$column->name} ({$column->type})" . ($column->notnull ? " NOT NULL" : "") . "\n";
}

echo "\nScript concluído!\n";
