<?php

require __DIR__.'/vendor/autoload.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Iniciando correções de visualização...');

// 1. Corrigindo a exibição completa dos nomes de usuários na listagem de registros DNS
$dnsListViewPath = __DIR__ . '/resources/views/admin/dns-records/index.blade.php';
if (file_exists($dnsListViewPath)) {
    output('Corrigindo listagem principal de registros DNS...');
    
    // Fazer backup do arquivo
    $backupPath = $dnsListViewPath . '.backup.' . time() . '.php';
    copy($dnsListViewPath, $backupPath);
    output('Backup criado em: ' . $backupPath);
    
    $content = file_get_contents($dnsListViewPath);
    
    // Vamos verificar o conteúdo atual da view
    if (strpos($content, 'Usuário: \' . $userName . \'') !== false) {
        output('A correção anterior já foi aplicada, mas parece não estar funcionando corretamente.');
        output('Aplicando abordagem alternativa...');
        
        // Em vez de usar a variável complexa $userName, vamos simplesmente mostrar o nome do usuário diretamente
        $pattern = '/if\\s*\\(\\$record->user_id\\)\\s*{[\\s\\S]*?\\$associations\\[\\]\\s*=\\s*\'<span class=\\"badge bg-dark\\"\\>Usuário: \' \\. \\$userName \\. \'<\\/span>\';\\s*}/';
        $replacement = 'if ($record->user_id) {
                                                // Buscar nome do usuário diretamente no output para garantir
                                                $userData = \\App\\Models\\Usuario::find($record->user_id);
                                                $associations[] = \'<span class="badge bg-dark">Usuário: \' . 
                                                    ($userData ? $userData->name : \'ID:\'.$record->user_id) . 
                                                \'</span>\';
                                            }';
        
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    file_put_contents($dnsListViewPath, $content);
    output('View de listagem corrigida.');
}

// 2. Corrigindo a exibição de domínios Cloudflare na view de detalhes do usuário
$userViewPath = __DIR__ . '/resources/views/admin/users/show.blade.php';
if (file_exists($userViewPath)) {
    output('\nCorrigindo view de detalhes do usuário para mostrar domínios Cloudflare...');
    
    // Fazer backup do arquivo
    $backupPath = $userViewPath . '.backup.' . time() . '.php';
    copy($userViewPath, $backupPath);
    output('Backup criado em: ' . $backupPath);
    
    $content = file_get_contents($userViewPath);
    
    // Vamos verificar como está implementada a seção de domínios Cloudflare
    $cloudflarePattern = '/<div class=\"card mb-4\">\\s*<div class=\"card-header\">\\s*Domínios Cloudflare\\s*<\\/div>\\s*<div class=\"card-body\">\\s*@if\\s*\\(\\$[^\\)]+\\)([\\s\\S]*?)@else([\\s\\S]*?)@endif\\s*<\\/div>\\s*<\\/div>/';
    
    if (preg_match($cloudflarePattern, $content, $matches)) {
        output('Encontrada seção de Domínios Cloudflare.');
        
        // Verificar qual relacionamento está sendo utilizado para os domínios
        $relatedCode = $matches[0];
        
        if (strpos($relatedCode, 'cloudflareAssociations') !== false || 
            strpos($relatedCode, 'cloudflare_associations') !== false ||
            strpos($relatedCode, 'cloudflare_domains') !== false ||
            strpos($relatedCode, 'cloudflare') !== false) {
            
            output('Encontrada referência a relacionamento Cloudflare.');
            
            // Verificar os registros DNS associados ao usuário diretamente
            $checkDnsRecordsCode = '
            <div class=\"card mb-4\">
                <div class=\"card-header\">
                    Domínios Cloudflare
                </div>
                <div class=\"card-body\">
                    @php
                        $dnsRecords = \\App\\Models\\DnsRecord::where(\'user_id\', $usuario->id)
                            ->where(function($query) {
                                $query->whereHas(\'externalApi\', function($q) {
                                    $q->where(\'name\', \'like\', \'%Cloudflare%\');
                                })->orWhere(\'external_api_id\', function($q) {
                                    $q->select(\'id\')
                                        ->from(\'external_apis\')
                                        ->where(\'name\', \'like\', \'%Cloudflare%\')
                                        ->limit(1);
                                });
                            })
                            ->get();
                    @endphp
                    
                    @if($dnsRecords->count() > 0)
                        <div class=\"table-responsive\">
                            <table class=\"table table-bordered table-striped\">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Tipo</th>
                                        <th>Conteúdo</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dnsRecords as $record)
                                        <tr>
                                            <td>{{ $record->id }}</td>
                                            <td>{{ $record->name }}</td>
                                            <td>{{ $record->record_type }}</td>
                                            <td>{{ $record->content }}</td>
                                            <td>
                                                <a href=\"{{ route(\'admin.dns-records.show\', $record->id) }}\" class=\"btn btn-sm btn-info\">
                                                    <i class=\"fas fa-eye\"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class=\"mt-2\">
                            <strong>{{ $dnsRecords->count() }}</strong> registro(s) encontrado(s)
                        </div>
                    @else
                        <div class=\"alert alert-info\">
                            Este usuário não possui domínios Cloudflare associados.
                        </div>
                    @endif
                </div>
            </div>';
            
            // Substituir a seção completa
            $content = preg_replace($cloudflarePattern, $checkDnsRecordsCode, $content);
            file_put_contents($userViewPath, $content);
            output('Seção de domínios Cloudflare atualizada para exibir registros DNS relacionados.');
        } else {
            output('Não foi possível identificar o relacionamento usado para domínios Cloudflare. Aplicando correção genérica...');
            
            // Se não conseguimos identificar a variável específica, vamos implementar nossa própria lógica
            $content = preg_replace($cloudflarePattern, $checkDnsRecordsCode, $content);
            file_put_contents($userViewPath, $content);
            output('Aplicada correção genérica à seção de domínios Cloudflare.');
        }
    } else {
        output('Não foi possível localizar a seção de Domínios Cloudflare na view.');
    }
}

output('\nCorreções aplicadas. Por favor, limpe o cache e recarregue a página:');
output('php artisan cache:clear');
output('php artisan view:clear');
