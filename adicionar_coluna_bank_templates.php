<?php

// Script para adicionar a coluna is_multipage à tabela bank_templates
// Executar com: php adicionar_coluna_bank_templates.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = \DB::connection();

// Desativar temporariamente chaves estrangeiras
$db->statement('PRAGMA foreign_keys = OFF;');
    
echo "Verificando estrutura atual da tabela bank_templates...\n";
$columns = $db->select('PRAGMA table_info(bank_templates);');
$columnNames = [];

foreach ($columns as $column) {
    $columnNames[] = $column->name;
    echo "- {$column->name} ({$column->type})" . ($column->notnull ? " NOT NULL" : "") . "\n";
}

// Verificar e adicionar a coluna is_multipage
if (!in_array('is_multipage', $columnNames)) {
    echo "Adicionando coluna is_multipage à tabela bank_templates...\n";
    try {
        $db->statement("ALTER TABLE bank_templates ADD COLUMN is_multipage TINYINT DEFAULT 0");
        echo "Coluna is_multipage adicionada com sucesso!\n";
    } catch (\Exception $e) {
        echo "Erro ao adicionar a coluna is_multipage: " . $e->getMessage() . "\n";
    }
} else {
    echo "A coluna is_multipage já existe na tabela bank_templates.\n";
}

// Reativar chaves estrangeiras
$db->statement('PRAGMA foreign_keys = ON;');

echo "\nVerificando estrutura final da tabela bank_templates...\n";
$columns = $db->select('PRAGMA table_info(bank_templates);');
foreach ($columns as $column) {
    echo "- {$column->name} ({$column->type})" . ($column->notnull ? " NOT NULL" : "") . "\n";
}

echo "\nScript concluído!\n";
