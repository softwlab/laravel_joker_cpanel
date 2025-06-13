<?php

// Script para criar a tabela cloudflare_domain_usuario
// Executar com: php criar_tabela_cloudflare_domain_usuario.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$db = \DB::connection();

// Verificar se a tabela já existe
$tableExists = $db->select("SELECT name FROM sqlite_master WHERE type='table' AND name='cloudflare_domain_usuario'");

if (count($tableExists) > 0) {
    echo "A tabela cloudflare_domain_usuario já existe.\n";
} else {
    echo "Criando tabela cloudflare_domain_usuario...\n";
    
    try {
        $db->statement('
            CREATE TABLE cloudflare_domain_usuario (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER NOT NULL,
                cloudflare_domain_id INTEGER NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT "active",
                config TEXT NULL,
                notes TEXT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
                FOREIGN KEY (cloudflare_domain_id) REFERENCES cloudflare_domains(id) ON DELETE CASCADE
            )
        ');
        
        // Criar índice único para evitar duplicatas
        $db->statement('
            CREATE UNIQUE INDEX cloudflare_domain_usuario_unique 
            ON cloudflare_domain_usuario(usuario_id, cloudflare_domain_id)
        ');
        
        echo "Tabela cloudflare_domain_usuario criada com sucesso!\n";
    } catch (\Exception $e) {
        echo "Erro ao criar tabela: " . $e->getMessage() . "\n";
    }
}

// Verificar se a tabela cloudflare_domains existe
$domainsTableExists = $db->select("SELECT name FROM sqlite_master WHERE type='table' AND name='cloudflare_domains'");

if (count($domainsTableExists) === 0) {
    echo "\nA tabela cloudflare_domains não existe. Criando...\n";
    
    try {
        $db->statement('
            CREATE TABLE cloudflare_domains (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                zone_id VARCHAR(255) NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT "active",
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )
        ');
        
        // Criar índice único para evitar duplicatas
        $db->statement('
            CREATE UNIQUE INDEX cloudflare_domains_zone_id_unique 
            ON cloudflare_domains(zone_id)
        ');
        
        echo "Tabela cloudflare_domains criada com sucesso!\n";
    } catch (\Exception $e) {
        echo "Erro ao criar tabela cloudflare_domains: " . $e->getMessage() . "\n";
    }
}

echo "\nVerificando estrutura da tabela cloudflare_domain_usuario...\n";
try {
    $columns = $db->select('PRAGMA table_info(cloudflare_domain_usuario);');
    foreach ($columns as $column) {
        echo "- {$column->name} ({$column->type})" . ($column->notnull ? " NOT NULL" : "") . "\n";
    }
} catch (\Exception $e) {
    echo "Erro ao verificar estrutura: " . $e->getMessage() . "\n";
}

echo "\nScript concluído!\n";
