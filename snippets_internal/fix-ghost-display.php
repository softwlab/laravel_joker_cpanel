<?php

require __DIR__.'/vendor/autoload.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Corrigindo a exibição da funcionalidade Ghost na listagem de domínios...');

// Caminho para a view que exibe os domínios
$viewPath = __DIR__ . '/resources/views/admin/external-apis/domains.blade.php';

if (file_exists($viewPath)) {
    output('View encontrada, criando backup antes de modificar...');
    
    // Criar backup
    $backupPath = $viewPath . '.bak.' . time();
    copy($viewPath, $backupPath);
    output('Backup criado em: ' . $backupPath);
    
    // Ler o conteúdo da view
    $content = file_get_contents($viewPath);
    
    // Encontrar o bloco da coluna Ghost
    $ghostColumnPattern = '<td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                            <input class="form-check-input ghost-toggle" type="checkbox" role="switch" 
                                                id="ghost-{{ \$domain[\'id\'] }}" 
                                                data-domain-id="{{ \$domain[\'id\'] }}" 
                                                {{ isset\(\$domain[\'is_ghost\'\]\) && \$domain[\'is_ghost\'] \? \'checked\' : \'\' }}>
                                            <label class="form-check-label ms-2" for="ghost-{{ \$domain[\'id\'] }}">
                                                <i class="fas fa-ghost {{ isset\(\$domain[\'is_ghost\'\]\) && \$domain[\'is_ghost\'] \? \'text-danger\' : \'text-secondary\' }}"></i>
                                            </label>
                                        </div>
                                    </td>';
    
    // Novo bloco com informações de subdomínios e usuários
    $newGhostColumn = '<td>
        <div class="d-flex justify-content-between">
            <div class="form-check form-switch d-flex align-items-center">
                <input class="form-check-input ghost-toggle" type="checkbox" role="switch" 
                    id="ghost-{{ $domain[\'id\'] }}" 
                    data-domain-id="{{ $domain[\'id\'] }}" 
                    {{ isset($domain[\'is_ghost\']) && $domain[\'is_ghost\'] ? \'checked\' : \'\' }}>
                <label class="form-check-label ms-2" for="ghost-{{ $domain[\'id\'] }}">
                    <i class="fas fa-ghost {{ isset($domain[\'is_ghost\']) && $domain[\'is_ghost\'] ? \'text-danger\' : \'text-secondary\' }}"></i>
                </label>
            </div>
            @php
                // Buscar informações de subdomínios e usuários
                $domainInfo = App\\Models\\CloudflareDomain::where(\'zone_id\', $domain[\'id\'])->first();
                $subdomainCount = 0;
                $userCount = 0;
                
                if ($domainInfo) {
                    $subdomainCount = $domainInfo->dnsRecords()->count();
                    $userCount = $domainInfo->usuarios()->count();
                }
            @endphp
            <div class="badge bg-info me-1" title="Subdomínios">
                <i class="fas fa-sitemap"></i> {{ $subdomainCount }}
            </div>
            <div class="badge bg-primary" title="Usuários">
                <i class="fas fa-users"></i> {{ $userCount }}
            </div>
        </div>
    </td>';
    
    // Substituir o bloco
    $newContent = str_replace($ghostColumnPattern, $newGhostColumn, $content);
    
    // Se a substituição exata não funcionou, tentar uma abordagem alternativa
    if ($newContent === $content) {
        output('O padrão exato não foi encontrado, tentando abordagem alternativa...');
        
        // Buscar pela tag td que contenha "ghost-toggle"
        $pattern = '/<td[^>]*>.*?ghost-toggle.*?<\/td>/s';
        if (preg_match($pattern, $content, $matches)) {
            output('Encontrado bloco alternativo para substituição.');
            $newContent = preg_replace($pattern, $newGhostColumn, $content);
            
            if ($newContent !== $content) {
                output('Substituição realizada com sucesso usando padrão alternativo.');
                file_put_contents($viewPath, $newContent);
            } else {
                output('Falha ao substituir usando padrão alternativo.');
            }
        } else {
            output('Falha ao localizar padrão alternativo.');
        }
    } else {
        output('Substituição realizada com sucesso!');
        file_put_contents($viewPath, $newContent);
    }
    
    // Verificar se a substituição foi efetiva
    $updatedContent = file_get_contents($viewPath);
    if (strpos($updatedContent, 'subdomainCount') !== false) {
        output('A view foi atualizada com sucesso!');
    } else {
        output('AVISO: A view parece não ter sido atualizada corretamente.');
        
        // Tentar uma abordagem manual
        output('Tentando aplicar uma abordagem manual para garantir a correção...');
        
        // Verificar se existe uma tabela com a coluna Ghost
        $pattern = '/<th>Ghost<\/th>/';
        if (preg_match($pattern, $updatedContent, $matches)) {
            output('Coluna Ghost encontrada na tabela.');
            
            // Script para ser adicionado no topo da view
            $ghostCountScript = "
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Buscar informações de Ghost para cada domínio
    const ghostCells = document.querySelectorAll('td[data-domain-id]');
    ghostCells.forEach(cell => {
        const domainId = cell.getAttribute('data-domain-id');
        if (domainId) {
            fetch(`/admin/domains/${domainId}/ghost-info`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const subdomainBadge = document.createElement('div');
                        subdomainBadge.className = 'badge bg-info me-1';
                        subdomainBadge.title = 'Subdomínios';
                        subdomainBadge.innerHTML = `<i class=\"fas fa-sitemap\"></i> \${data.subdomains}`;
                        
                        const userBadge = document.createElement('div');
                        userBadge.className = 'badge bg-primary';
                        userBadge.title = 'Usuários';
                        userBadge.innerHTML = `<i class=\"fas fa-users\"></i> \${data.users}`;
                        
                        const container = document.createElement('div');
                        container.className = 'd-flex mt-2';
                        container.appendChild(subdomainBadge);
                        container.appendChild(userBadge);
                        
                        cell.appendChild(container);
                    }
                })
                .catch(error => console.error('Erro ao buscar informações do Ghost:', error));
        }
    });
});
</script>
@endpush
";
            
            // Adicionar o script no final da view
            $updatedContent = str_replace('@endsection', "@endsection\n" . $ghostCountScript, $updatedContent);
            file_put_contents($viewPath, $updatedContent);
            
            output('Script adicionado para buscar informações de Ghost via JavaScript.');
            
            // Agora vamos adicionar atributo data-domain-id às células da tabela
            $cellPattern = '<td class="text-center">\s*<div class="form-check form-switch d-flex justify-content-center align-items-center">';
            $newCellStart = '<td class="text-center" data-domain-id="{{ $domain[\'id\'] }}">' . PHP_EOL . '    <div class="form-check form-switch d-flex justify-content-center align-items-center">';
            
            $updatedContent = preg_replace($cellPattern, $newCellStart, $updatedContent);
            file_put_contents($viewPath, $updatedContent);
            
            output('Atributos data-domain-id adicionados às células da tabela.');
        } else {
            output('Não foi possível encontrar a coluna Ghost na tabela.');
        }
    }
    
    // Limpar cache do Laravel
    output('Limpando cache do Laravel...');
    system('php artisan cache:clear');
    system('php artisan view:clear');
    
    output('Correção concluída! Por favor, recarregue a página de domínios para ver as alterações.');

    // Verificar se já existe uma rota para a API de informações do Ghost
    $routesPath = __DIR__ . '/routes/web.php';
    if (file_exists($routesPath)) {
        $routesContent = file_get_contents($routesPath);
        
        if (strpos($routesContent, 'ghost-info') === false) {
            output('Adicionando rota para a API de informações do Ghost...');
            
            // Buscar o grupo de rotas do admin
            $adminRoutePattern = "Route::prefix\('admin'\)->middleware\(\['auth', 'admin'\]\)->name\('admin.'\)->group\(function \(\) {";
            if (preg_match($adminRoutePattern, $routesContent, $matches, PREG_OFFSET_CAPTURE)) {
                $position = $matches[0][1] + strlen($matches[0][0]);
                
                // Adicionar a rota
                $ghostInfoRoute = "\n    // Rota para obter informações do Ghost
    Route::get('/domains/{domain}/ghost-info', [App\\Http\\Controllers\\Admin\\ExternalApiController::class, 'getGhostInfo'])->name('domains.ghost-info');";
                
                $routesContent = substr($routesContent, 0, $position) . $ghostInfoRoute . substr($routesContent, $position);
                file_put_contents($routesPath, $routesContent);
                
                output('Rota adicionada com sucesso!');
            } else {
                output('Não foi possível encontrar o grupo de rotas do admin.');
            }
        } else {
            output('A rota para a API de informações do Ghost já existe.');
        }
    }

    // Adicionar o método getGhostInfo ao controller
    $controllerPath = __DIR__ . '/app/Http/Controllers/Admin/ExternalApiController.php';
    if (file_exists($controllerPath)) {
        $controllerContent = file_get_contents($controllerPath);
        
        if (strpos($controllerContent, 'function getGhostInfo') === false) {
            output('Adicionando método getGhostInfo ao controller...');
            
            // Buscar o final da classe para adicionar o método
            $endPattern = "}\s*$";
            
            $ghostInfoMethod = "
    /**
     * Obtém informações do Ghost para um domínio específico
     *
     * @param string \$domain ID da zona/domínio
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGhostInfo(\$domain)
    {
        try {
            \$domainInfo = \\App\\Models\\CloudflareDomain::where('zone_id', \$domain)->first();
            
            if (!\$domainInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Domínio não encontrado'
                ]);
            }
            
            \$subdomainCount = \$domainInfo->dnsRecords()->count();
            \$userCount = \$domainInfo->usuarios()->count();
            
            return response()->json([
                'success' => true,
                'subdomains' => \$subdomainCount,
                'users' => \$userCount
            ]);
        } catch (\\Exception \$e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter informações: ' . \$e->getMessage()
            ]);
        }
    }
";
            
            $controllerContent = preg_replace($endPattern, $ghostInfoMethod . "\n}", $controllerContent);
            file_put_contents($controllerPath, $controllerContent);
            
            output('Método getGhostInfo adicionado com sucesso ao controller!');
        } else {
            output('O método getGhostInfo já existe no controller.');
        }
    }

} else {
    output('ERRO: Arquivo da view não encontrado em: ' . $viewPath);
}
