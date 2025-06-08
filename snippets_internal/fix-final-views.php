<?php

require __DIR__.'/vendor/autoload.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Iniciando correções finais de visualização...');

// 1. Primeiro vamos corrigir a visualização de usuários na listagem de registros DNS
$dnsListViewPath = __DIR__ . '/resources/views/admin/dns-records/index.blade.php';
if (file_exists($dnsListViewPath)) {
    output('Corrigindo a view de listagem de registros DNS...');
    
    // Backup do arquivo
    $backupPath = $dnsListViewPath . '.backup.' . time() . '.php';
    copy($dnsListViewPath, $backupPath);
    output('Backup criado em: ' . $backupPath);
    
    // Ler o conteúdo da view
    $content = file_get_contents($dnsListViewPath);
    
    // Localizar o bloco PHP responsável pelas associações
    $phpBlockPattern = '/@php\\s*\\$associations\\s*=\\s*\\[\\];.*?@endphp/s';
    
    if (preg_match($phpBlockPattern, $content, $matches)) {
        $originalBlock = $matches[0];
        output('Bloco PHP encontrado na view de listagem de registros DNS.');
        
        // Substituir o bloco inteiro por uma versão mais confiável
        $newBlock = '@php
                                            $associations = [];
                                            if ($record->bank) $associations[] = \'<span class=\"badge bg-primary\">Link</span>\';
                                            if ($record->bankTemplate) $associations[] = \'<span class=\"badge bg-info\">Template</span>\';
                                            if ($record->linkGroup) $associations[] = \'<span class=\"badge bg-warning\">Grupo</span>\';
                                            if ($record->user_id) {
                                                // Buscar o usuário pelo ID diretamente para garantir
                                                $userData = \\App\\Models\\Usuario::find($record->user_id);
                                                if ($userData) {
                                                    $associations[] = \'<span class=\"badge bg-dark\">Usuário: \' . $userData->name . \'</span>\';
                                                } else {
                                                    $associations[] = \'<span class=\"badge bg-dark\">Usuário ID: \' . $record->user_id . \'</span>\';
                                                }
                                            }
                                        @endphp';
                                        
        $content = str_replace($originalBlock, $newBlock, $content);
        file_put_contents($dnsListViewPath, $content);
        output('View de listagem DNS atualizada.');
    } else {
        output('AVISO: Não foi possível encontrar o bloco PHP na view de listagem DNS.');
    }
}

// 2. Agora vamos corrigir a exibição de domínios Cloudflare na view de usuário
$userViewPath = __DIR__ . '/resources/views/admin/users/show.blade.php';
if (file_exists($userViewPath)) {
    output('\nCorrigindo a view de detalhes do usuário...');
    
    // Backup do arquivo
    $backupPath = $userViewPath . '.backup.' . time() . '.php';
    copy($userViewPath, $backupPath);
    output('Backup criado em: ' . $backupPath);
    
    // Agora vamos criar o novo bloco para a seção de Domínios Cloudflare
    $content = file_get_contents($userViewPath);
    
    // Localizar a seção de domínios Cloudflare de forma mais simples
    $cloudflareHeaderPattern = '/<div class=\"card-header\">\\s*Domínios Cloudflare\\s*<\\/div>/';
    
    if (preg_match($cloudflareHeaderPattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        $headerPos = $matches[0][1];
        
        // Encontrar o início do card
        $cardStartPos = strrpos(substr($content, 0, $headerPos), '<div class=\"card');
        
        // Encontrar o final do card
        $endDivPos = strpos($content, '</div>', $headerPos);
        $endDivPos = strpos($content, '</div>', $endDivPos + 6); // +6 para passar o primeiro </div>
        
        if ($cardStartPos !== false && $endDivPos !== false) {
            output('Card de Domínios Cloudflare identificado na view de usuário.');
            
            // Capturar o card completo
            $completeCard = substr($content, $cardStartPos, $endDivPos - $cardStartPos + 6);
            
            // Criar o novo card
            $newCard = '<div class=\"card mb-4\">
                <div class=\"card-header\">
                    Domínios Cloudflare
                </div>
                <div class=\"card-body\">
                    @php
                        // Buscar registros DNS do Cloudflare associados ao usuário
                        $dnsRecords = \\App\\Models\\DnsRecord::where(\'user_id\', $usuario->id)
                            ->whereHas(\'externalApi\', function($q) {
                                $q->where(\'name\', \'like\', \'%Cloudflare%\');
                            })
                            ->get();
                    @endphp
                    
                    {{ $dnsRecords->count() }}
                    
                    @if($dnsRecords->count() > 0)
                        <div class=\"table-responsive mt-3\">
                            <table class=\"table table-sm\">
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
                                            <td>{{ Str::limit($record->content, 30) }}</td>
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
                    @else
                        <p class=\"text-muted\">Este usuário não possui domínios Cloudflare associados.</p>
                    @endif
                </div>
            </div>';
            
            // Substituir o card antigo pelo novo
            $content = str_replace($completeCard, $newCard, $content);
            file_put_contents($userViewPath, $content);
            output('Card de Domínios Cloudflare atualizado na view de usuário.');
        } else {
            output('AVISO: Não foi possível identificar o card completo de Domínios Cloudflare.');
        }
    } else {
        output('AVISO: Cabeçalho de Domínios Cloudflare não encontrado na view de usuário.');
    }
    
    // Verificar se o modelo Usuario tem o relacionamento com DNS records
    $usuarioModelPath = __DIR__ . '/app/Models/Usuario.php';
    if (file_exists($usuarioModelPath)) {
        $modelContent = file_get_contents($usuarioModelPath);
        
        if (strpos($modelContent, 'function dnsRecords') === false) {
            output('\nAdicionando relacionamento dnsRecords() ao modelo Usuario...');
            
            // Encontrar o último fechamento de chave para adicionar o método
            $lastBracePos = strrpos($modelContent, '}');
            
            if ($lastBracePos !== false) {
                $dnsRecordsMethod = '
    /**
     * Relacionamento com registros DNS
     */
    public function dnsRecords()
    {
        return $this->hasMany(\\App\\Models\\DnsRecord::class, \'user_id\');
    }
';
                // Inserir antes do fechamento da classe
                $modelContent = substr($modelContent, 0, $lastBracePos) . $dnsRecordsMethod . substr($modelContent, $lastBracePos);
                file_put_contents($usuarioModelPath, $modelContent);
                output('Relacionamento dnsRecords() adicionado ao modelo Usuario.');
            }
        } else {
            output('O modelo Usuario já possui o relacionamento dnsRecords().');
        }
    }
}

output('\nLimpando cache do Laravel...');
system('php artisan cache:clear');
system('php artisan view:clear');

output('\nCorreções finalizadas! Por favor, recarregue a página de usuário e a listagem de registros DNS.');
