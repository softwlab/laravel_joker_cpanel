<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Iniciando correção da chave estrangeira na tabela dns_records...');

// Como estamos usando SQLite, vamos executar comandos SQL diretos
$db = new PDO('sqlite:'.database_path('database.sqlite'));

// 1. Verificar como a tabela está definida atualmente
$tableInfo = $db->query("PRAGMA table_info(dns_records)")->fetchAll(PDO::FETCH_ASSOC);
$foreignKeys = $db->query("PRAGMA foreign_key_list(dns_records)")->fetchAll(PDO::FETCH_ASSOC);

output('Informações da tabela dns_records:');
print_r($tableInfo);

output('\nChaves estrangeiras da tabela dns_records:');
print_r($foreignKeys);

// 2. Criar uma nova tabela com a estrutura correta
output('\nCriando tabela temporária com a estrutura correta...');

// Primeiro, vamos criar uma tabela temporária
$db->exec("
    CREATE TABLE dns_records_temp (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        external_api_id INTEGER NOT NULL,
        bank_id INTEGER NULL,
        bank_template_id INTEGER NULL,
        link_group_id INTEGER NULL,
        user_id INTEGER NULL,
        record_type VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        ttl INTEGER DEFAULT 3600,
        priority INTEGER NULL,
        status VARCHAR(255) NOT NULL DEFAULT 'active',
        extra_data TEXT NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (external_api_id) REFERENCES external_apis(id) ON DELETE CASCADE,
        FOREIGN KEY (bank_id) REFERENCES banks(id) ON DELETE SET NULL,
        FOREIGN KEY (bank_template_id) REFERENCES bank_templates(id) ON DELETE SET NULL,
        FOREIGN KEY (link_group_id) REFERENCES link_groups(id) ON DELETE SET NULL,
        FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE SET NULL
    )
");

// 3. Copiar os dados da tabela original para a temporária
output('Copiando dados para a nova tabela...');
$db->exec("
    INSERT INTO dns_records_temp
    SELECT * FROM dns_records
");

// 4. Remover a tabela original
output('Removendo a tabela original...');
$db->exec("DROP TABLE dns_records");

// 5. Renomear a tabela temporária
output('Renomeando a tabela temporária...');
$db->exec("ALTER TABLE dns_records_temp RENAME TO dns_records");

// 6. Verificar as novas chaves estrangeiras
$newForeignKeys = $db->query("PRAGMA foreign_key_list(dns_records)")->fetchAll(PDO::FETCH_ASSOC);
output('\nNovas chaves estrangeiras da tabela dns_records:');
print_r($newForeignKeys);

output('\nCorreção concluída! Agora você deve conseguir associar usuários aos registros DNS.');
output('Recarregue a página e tente novamente.');
