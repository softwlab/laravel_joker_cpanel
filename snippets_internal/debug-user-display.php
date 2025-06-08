<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Depurando problema na exibição do nome do usuário...');

// Verificar a relação entre DnsRecord e Usuario
$viewPath = __DIR__ . '/resources/views/admin/dns-records/index.blade.php';
$content = file_get_contents($viewPath);

// Fazer backup do arquivo original
$backupPath = $viewPath . '.backup.' . time() . '.php';
file_put_contents($backupPath, $content);

// Vamos primeiro verificar o conteúdo do código atual
$pattern = '/if \\(\\$record->user\\)(.*?)\\$/s';
preg_match($pattern, $content, $matches);

if (!empty($matches)) {
    output('Código atual para exibição do usuário:');
    output($matches[0]);
} else {
    output('Não foi possível encontrar o código para exibição do usuário.');
}

// Agora vamos verificar diretamente os usuários associados aos registros DNS
output('\nVerificando registros DNS com usuários associados:');

try {
    $records = App\Models\DnsRecord::with('user')->whereNotNull('user_id')->limit(5)->get();
    
    foreach ($records as $record) {
        output('Registro #' . $record->id);
        output('  user_id: ' . $record->user_id);
        
        if ($record->user) {
            output('  Usuario encontrado: Sim');
            output('  Usuario ID: ' . $record->user->id);
            output('  Usuario nome: ' . ($record->user->name ?? 'NOME NÃO ENCONTRADO'));
            
            // Verificar o objeto completo
            output('  Propriedades disponíveis no objeto Usuario:');
            foreach (get_object_vars($record->user) as $key => $value) {
                if (is_scalar($value)) {
                    output('    - ' . $key . ': ' . $value);
                } else {
                    output('    - ' . $key . ': [Objeto/Array]');
                }
            }
        } else {
            output('  Usuario encontrado: NÃO (relação nula mesmo com user_id)');
        }
        output('---');
    }
    
    // Corrigir o template - versão simplificada para debug
    output('\nAplicando correção aprimorada ao template...');
    
    // Implementar uma solução mais robusta
    $fixedTemplate = "
                                        @php
                                            \$associations = [];
                                            if (\$record->bank) \$associations[] = '<span class=\"badge bg-primary\">Link</span>';
                                            if (\$record->bankTemplate) \$associations[] = '<span class=\"badge bg-info\">Template</span>';
                                            if (\$record->linkGroup) \$associations[] = '<span class=\"badge bg-warning\">Grupo</span>';
                                            if (\$record->user_id) {
                                                \$userName = \$record->user ? \$record->user->name : 'ID: '.\$record->user_id;
                                                \$associations[] = '<span class=\"badge bg-dark\">Usuário: ' . \$userName . '</span>';
                                            }
                                        @endphp
    ";
    
    // Substituir o bloco PHP completo
    $pattern = '/@php\\s*\\$associations = \\[\\];.*?@endphp/s';
    $content = preg_replace($pattern, trim($fixedTemplate), $content);
    
    // Salvar o arquivo modificado
    file_put_contents($viewPath, $content);
    output('Template atualizado com nova lógica robusta para exibição do usuário.');
    
} catch (\Exception $e) {
    output('ERRO: ' . $e->getMessage());
}

output('Recarregue a página para verificar as alterações.');
