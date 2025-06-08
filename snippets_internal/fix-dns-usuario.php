<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Iniciando correção de validação de usuários nos registros DNS...');

// Abrir o arquivo do controlador
$controllerPath = __DIR__ . '/app/Http/Controllers/Admin/DnsRecordController.php';
$content = file_get_contents($controllerPath);

// Substituição 1: Na validação do store() - 'user_id' => 'nullable|exists:users,id'
$content = str_replace(
    "'user_id' => 'nullable|exists:users,id',",
    "'user_id' => 'nullable|exists:usuarios,id',",
    $content
);

output('Validação de usuários no método store() corrigida.');

// Substituição 2: Na validação do update() - se houver
$updateValidation = str_replace(
    "'user_id' => 'nullable|exists:users,id',",
    "'user_id' => 'nullable|exists:usuarios,id',",
    $content
);

if ($updateValidation !== $content) {
    $content = $updateValidation;
    output('Validação de usuários no método update() corrigida.');
}

// Salvar as alterações de volta no arquivo
file_put_contents($controllerPath, $content);

output('Correções aplicadas com sucesso!');
output('Recarregue a página e tente associar um usuário novamente.');
