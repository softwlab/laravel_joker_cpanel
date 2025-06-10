<?php

// Lista de migrações que sabemos que já foram aplicadas
$migrationsToMark = [
    '2025_06_08_080849_add_active_column_to_external_apis_table',
    '2025_06_08_081426_add_usuario_id_to_link_groups_table',
    '2025_06_08_084500_create_api_keys_table',
    '2025_06_10_125000_prepare_transition_from_links_to_dns'
];

// Excluir nossas próprias migrações novas que queremos executar
$migrationsToKeepPending = [
    '2025_06_10_130444_remove_legacy_link_tables',
    '2025_06_10_131805_create_migration_temp_dns_table',
    '2025_06_11_000000_create_deprecated_api_usage_table',
    '2025_07_15_000000_prepare_link_group_removal',
    '2025_12_31_000000_remove_legacy_link_system'
];

// Obter o maior batch atual
$maxBatch = DB::table('migrations')->max('batch') ?? 0;
$batch = $maxBatch + 1;

// Inserir as migrações marcadas como concluídas
$count = 0;
foreach ($migrationsToMark as $migration) {
    // Verificar se já existe
    if (!DB::table('migrations')->where('migration', $migration)->exists()) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
        $count++;
        echo "Migração {$migration} marcada como concluída.\n";
    } else {
        echo "Migração {$migration} já está marcada como concluída.\n";
    }
}

echo "\nTotal de {$count} migrações marcadas como concluídas.\n";
echo "As seguintes migrações permanecem pendentes e serão executadas normalmente:\n";
foreach ($migrationsToKeepPending as $migration) {
    echo "- {$migration}\n";
}
