<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Iniciando correção da exibição de usuário na lista de registros DNS...');

// Abrir o arquivo de view
$viewPath = __DIR__ . '/resources/views/admin/dns-records/index.blade.php';
$content = file_get_contents($viewPath);

// Fazer backup do arquivo original
$backupPath = $viewPath . '.backup.' . time() . '.php';
file_put_contents($backupPath, $content);
output('Backup do arquivo original criado em: ' . $backupPath);

// Localizar a linha que mostra apenas 'Usuário'
$originalLine = "if (\$record->user) \$associations[] = '<span class=\"badge bg-dark\">Usuário</span>';";
$newLine = "if (\$record->user) \$associations[] = '<span class=\"badge bg-dark\">Usuário: ' . \$record->user->name . '</span>';";

$content = str_replace($originalLine, $newLine, $content);

// Salvar o arquivo modificado
file_put_contents($viewPath, $content);
output('Exibição de usuário corrigida com sucesso na view de registros DNS!');

// Também vamos verificar e corrigir a view de detalhes do registro
$detailViewPath = __DIR__ . '/resources/views/admin/dns-records/show.blade.php';
if (file_exists($detailViewPath)) {
    output('Verificando a view de detalhes do registro...');
    $detailContent = file_get_contents($detailViewPath);
    
    // Fazer backup
    $detailBackupPath = $detailViewPath . '.backup.' . time() . '.php';
    file_put_contents($detailBackupPath, $detailContent);
    
    // Procurar padrões como 'Usuário: <span>{{ $record->user ? 'Sim' : 'Não' }}</span>'
    // e substituir por exibições mais detalhadas
    $userPattern = '/Usuário:\\s*<[^>]+>\\s*{{\\s*\\$record->user \\? \'Sim\' : \'Não\'\\s*}}\\s*<\\/[^>]+>/';
    $userReplacement = 'Usuário: <span>{{ $record->user ? $record->user->name . \' (ID: \' . $record->user->id . \')\' : \'Não associado\' }}</span>';
    
    $detailContent = preg_replace($userPattern, $userReplacement, $detailContent);
    
    file_put_contents($detailViewPath, $detailContent);
    output('View de detalhes também foi atualizada para mostrar o nome do usuário.');
}

output('Recarregue a página para ver as alterações.');
