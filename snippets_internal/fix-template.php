<?php

require __DIR__.'/vendor/autoload.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Corrigindo template para exibição do nome do usuário...');

// Caminho para o template
$viewPath = __DIR__ . '/resources/views/admin/dns-records/index.blade.php';

// Verificar se o arquivo existe
if (!file_exists($viewPath)) {
    output('ERRO: O arquivo do template não foi encontrado!');
    exit(1);
}

// Ler o conteúdo do arquivo
$content = file_get_contents($viewPath);

// Fazer backup do arquivo original
$backupPath = $viewPath . '.backup.' . time() . '.php';
file_put_contents($backupPath, $content);
output('Backup do arquivo original criado em: ' . $backupPath);

// Correção direta usando substituição de texto

// 1. Vamos encontrar e corrigir o bloco PHP específico que gera as associações
$oldBlock = '@php
                                            $associations = [];
                                            if ($record->bank) $associations[] = \'<span class=\"badge bg-primary\">Link</span>\';
                                            if ($record->bankTemplate) $associations[] = \'<span class=\"badge bg-info\">Template</span>\';
                                            if ($record->linkGroup) $associations[] = \'<span class=\"badge bg-warning\">Grupo</span>\';
                                            if ($record->user) $associations[] = \'<span class=\"badge bg-dark\">Usuário:</span>\';
                                        @endphp';

$newBlock = '@php
                                            $associations = [];
                                            if ($record->bank) $associations[] = \'<span class=\"badge bg-primary\">Link</span>\';
                                            if ($record->bankTemplate) $associations[] = \'<span class=\"badge bg-info\">Template</span>\';
                                            if ($record->linkGroup) $associations[] = \'<span class=\"badge bg-warning\">Grupo</span>\';
                                            if ($record->user_id) {
                                                $userData = \App\Models\Usuario::find($record->user_id);
                                                $userName = $userData ? $userData->name : \'ID: \'.$record->user_id;
                                                $associations[] = \'<span class=\"badge bg-dark\">Usuário: \' . $userName . \'</span>\';
                                            }
                                        @endphp';

// Substituir o bloco
$content = str_replace($oldBlock, $newBlock, $content);

// Verificar se a substituição funcionou
if ($content === file_get_contents($viewPath)) {
    output('AVISO: Não foi possível encontrar o bloco exato para substituição.');
    output('Tentando uma abordagem alternativa...');
    
    // Tentar uma abordagem baseada em expressão regular para localizar o bloco
    $pattern = '/@php\\s*\\$associations = \\[\\];.*?if \\(\\$record->user\\).*?@endphp/s';
    
    $updatedContent = preg_replace_callback($pattern, function($matches) {
        output('Bloco encontrado com regex. Substituindo...');
        return '@php
                                            $associations = [];
                                            if ($record->bank) $associations[] = \'<span class=\"badge bg-primary\">Link</span>\';
                                            if ($record->bankTemplate) $associations[] = \'<span class=\"badge bg-info\">Template</span>\';
                                            if ($record->linkGroup) $associations[] = \'<span class=\"badge bg-warning\">Grupo</span>\';
                                            if ($record->user_id) {
                                                $userData = \App\Models\Usuario::find($record->user_id);
                                                $userName = $userData ? $userData->name : \'ID: \'.$record->user_id;
                                                $associations[] = \'<span class=\"badge bg-dark\">Usuário: \' . $userName . \'</span>\';
                                            }
                                        @endphp';
    }, $content);
    
    if ($updatedContent !== $content) {
        $content = $updatedContent;
        output('Bloco substituído com regex.');
    } else {
        output('ERRO: Não foi possível encontrar o bloco para substituição mesmo com regex.');
        
        // Última tentativa - editar o arquivo manualmente
        $manualEditMessage = '
**ATENÇÃO: Correção Manual Necessária**

Por favor, edite o arquivo `' . $viewPath . '` manualmente.

Localize o bloco PHP que gera as associações de registros DNS (por volta da linha 135).
Deve ser parecido com isto:

```php
@php
    $associations = [];
    if ($record->bank) $associations[] = \'<span class="badge bg-primary">Link</span>\';
    if ($record->bankTemplate) $associations[] = \'<span class="badge bg-info">Template</span>\';
    if ($record->linkGroup) $associations[] = \'<span class="badge bg-warning">Grupo</span>\';
    if ($record->user) $associations[] = \'<span class="badge bg-dark">Usuário:</span>\';
@endphp
```

Substitua a linha do usuário por:

```php
if ($record->user_id) {
    $userData = \App\Models\Usuario::find($record->user_id);
    $userName = $userData ? $userData->name : \'ID: \'.$record->user_id;
    $associations[] = \'<span class="badge bg-dark">Usuário: \' . $userName . \'</span>\';
}
```

Depois de fazer a alteração, salve o arquivo e recarregue a página.
';
        
        output($manualEditMessage);
        
        // Escrever as instruções em um arquivo para referência
        file_put_contents(__DIR__ . '/manual-edit-instructions.txt', $manualEditMessage);
        output('Instruções de edição manual salvas em ' . __DIR__ . '/manual-edit-instructions.txt');
        
        exit(1);
    }
}

// Salvar as alterações no arquivo
file_put_contents($viewPath, $content);
output('Template atualizado com sucesso!');
output('Recarregue a página para ver os nomes dos usuários.');
