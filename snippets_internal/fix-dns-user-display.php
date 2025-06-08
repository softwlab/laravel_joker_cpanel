<?php

require __DIR__.'/vendor/autoload.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Corrigindo a exibição do nome do usuário na listagem de registros DNS...');

// Caminho para a view de listagem de registros DNS
$viewPath = __DIR__ . '/resources/views/admin/dns-records/index.blade.php';

if (file_exists($viewPath)) {
    output('View encontrada, criando backup antes de modificar...');
    
    // Criar backup
    $backupPath = $viewPath . '.bak.' . time();
    copy($viewPath, $backupPath);
    output('Backup criado em: ' . $backupPath);
    
    // Ler o conteúdo da view
    $content = file_get_contents($viewPath);
    
    // Encontrar o bloco PHP de associações
    $pattern = '/@php\s*\$associations\s*=\s*\[\].*?@endphp/s';
    if (preg_match($pattern, $content, $matches)) {
        output('Bloco de associações encontrado, substituindo por versão corrigida...');
        
        // Definir o novo bloco com as aspas corretamente escapadas
        $newBlock = '@php
                                            $associations = [];
                                            if ($record->bank) $associations[] = \'<span class="badge bg-primary">Link</span>\';
                                            if ($record->bankTemplate) $associations[] = \'<span class="badge bg-info">Template</span>\';
                                            if ($record->linkGroup) $associations[] = \'<span class="badge bg-warning">Grupo</span>\';
                                            if ($record->user_id) {
                                                // Buscar o usuário pelo ID diretamente para garantir
                                                $userData = \App\Models\Usuario::find($record->user_id);
                                                if ($userData) {
                                                    $associations[] = \'<span class="badge bg-dark">Usuário: \' . $userData->name . \'</span>\';
                                                } else {
                                                    $associations[] = \'<span class="badge bg-dark">Usuário ID: \' . $record->user_id . \'</span>\';
                                                }
                                            }
                                        @endphp';
        
        // Substituir o bloco antigo pelo novo
        $newContent = preg_replace($pattern, $newBlock, $content);
        file_put_contents($viewPath, $newContent);
        output('Bloco de código substituído com sucesso.');
    } else {
        output('ATENÇÃO: Não foi possível encontrar o bloco de código das associações.');
        
        // Tentativa alternativa - substituir diretamente
        output('Tentando substituição direta do código...');
        
        // Substituir diretamente os trechos específicos
        $searchUserBadge = 'if ($record->user) $associations[] = \'<span class=\"badge bg-dark\">Usuário:</span>\';';
        $replaceUserBadge = 'if ($record->user_id) {
                                                // Buscar o usuário pelo ID diretamente para garantir
                                                $userData = \App\Models\Usuario::find($record->user_id);
                                                if ($userData) {
                                                    $associations[] = \'<span class="badge bg-dark">Usuário: \' . $userData->name . \'</span>\';
                                                } else {
                                                    $associations[] = \'<span class="badge bg-dark">Usuário ID: \' . $record->user_id . \'</span>\';
                                                }
                                            }';
        
        $newContent = str_replace($searchUserBadge, $replaceUserBadge, $content);
        
        if ($newContent !== $content) {
            file_put_contents($viewPath, $newContent);
            output('Código substituído com sucesso usando abordagem alternativa.');
        } else {
            output('ERRO: Não foi possível modificar o código usando abordagem alternativa.');
        }
    }
    
    // Limpar o cache de visualizações do Laravel para garantir que as mudanças sejam aplicadas
    output('Limpando o cache de visualizações do Laravel...');
    system('php artisan cache:clear');
    system('php artisan view:clear');
    
    output('Correção concluída. Por favor, recarregue a página de listagem de registros DNS.');
} else {
    output('ERRO: Arquivo da view não encontrado em: ' . $viewPath);
}
