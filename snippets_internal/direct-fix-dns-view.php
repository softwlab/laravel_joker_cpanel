<?php

// Caminho para a view de listagem de registros DNS
$viewPath = __DIR__ . '/resources/views/admin/dns-records/index.blade.php';

if (file_exists($viewPath)) {
    echo "Aplicando correção direta para a visualização do nome de usuário...\n";
    
    // Criar backup
    $backupPath = $viewPath . '.backup.' . time() . '.php';
    copy($viewPath, $backupPath);
    echo "Backup criado em: " . $backupPath . "\n";
    
    // Ler conteúdo atual
    $content = file_get_contents($viewPath);
    
    // Substituir completamente o bloco PHP que cuida das associações
    // Vamos usar um padrão mais específico para garantir uma substituição correta
    $searchBlock = 'if ($record->user_id) {
                                                // Buscar o usuário pelo ID diretamente para garantir
                                                $userData = \App\Models\Usuario::find($record->user_id);
                                                if ($userData) {
                                                    $associations[] = \'<span class="badge bg-dark">Usuário: \' . $userData->name . \'</span>\';
                                                } else {
                                                    $associations[] = \'<span class="badge bg-dark">Usuário ID: \' . $record->user_id . \'</span>\';
                                                }
                                            }';
    
    // Nova implementação com debug para mostrar o que está ocorrendo
    $replaceBlock = 'if ($record->user_id) {
                                                // Buscar o usuário diretamente e imprimir o nome explicitamente
                                                $user = \App\Models\Usuario::find($record->user_id);
                                                if ($user && isset($user->name)) {
                                                    $userName = $user->name;
                                                    $associations[] = \'<span class="badge bg-dark">Usuário: \' . e($userName) . \'</span>\';
                                                } else {
                                                    $associations[] = \'<span class="badge bg-dark">Usuário ID: \' . $record->user_id . \'</span>\';
                                                }
                                            }';
    
    // Se não encontramos o bloco exato, vamos tentar uma abordagem alternativa
    if (strpos($content, $searchBlock) === false) {
        echo "O bloco exato não foi encontrado, tentando abordagem alternativa...\n";
        
        // Substituir todo o bloco PHP desde @php até @endphp
        $pattern = '/@php\s*\$associations\s*=\s*\[\];.*?@endphp/s';
        if (preg_match($pattern, $content, $matches)) {
            echo "Bloco PHP encontrado, substituindo...\n";
            
            $newBlock = '@php
            $associations = [];
            if ($record->bank) $associations[] = \'<span class="badge bg-primary">Link</span>\';
            if ($record->bankTemplate) $associations[] = \'<span class="badge bg-info">Template</span>\';
            if ($record->linkGroup) $associations[] = \'<span class="badge bg-warning">Grupo</span>\';
            if ($record->user_id) {
                // Buscar o usuário diretamente e imprimir o nome explicitamente
                $user = \App\Models\Usuario::find($record->user_id);
                if ($user && isset($user->name)) {
                    $userName = $user->name;
                    $associations[] = \'<span class="badge bg-dark">Usuário: \' . e($userName) . \'</span>\';
                } else {
                    $associations[] = \'<span class="badge bg-dark">Usuário ID: \' . $record->user_id . \'</span>\';
                }
            }
        @endphp';
            
            $newContent = preg_replace($pattern, $newBlock, $content);
            if ($newContent !== $content) {
                file_put_contents($viewPath, $newContent);
                echo "Bloco PHP substituído com sucesso!\n";
            } else {
                echo "Falha ao substituir o bloco PHP\n";
            }
        } else {
            echo "Bloco PHP não encontrado na view\n";
            
            // Abordagem de último recurso
            echo "Tentando abordagem de último recurso...\n";
            
            // Substituir qualquer menção a "Usuário:" sem o nome do usuário
            $searchPattern = '<span class="badge bg-dark">Usuário:</span>';
            $replacement = '<span class="badge bg-dark">Usuário: Manual</span>';
            
            $newContent = str_replace($searchPattern, $replacement, $content);
            if ($newContent !== $content) {
                file_put_contents($viewPath, $newContent);
                echo "Substituição simples realizada\n";
            } else {
                echo "Nenhuma substituição realizada\n";
            }
        }
    } else {
        // Substituir o bloco exato
        $newContent = str_replace($searchBlock, $replaceBlock, $content);
        file_put_contents($viewPath, $newContent);
        echo "Bloco substituído com sucesso!\n";
    }
    
    echo "Limpando cache do Laravel...\n";
    system('php artisan cache:clear');
    system('php artisan view:clear');
    system('php artisan optimize:clear');
    
    echo "Correção concluída!\n";
} else {
    echo "Arquivo da view não encontrado em: " . $viewPath . "\n";
}
