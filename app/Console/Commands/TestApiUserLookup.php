<?php

namespace App\Console\Commands;

use App\Models\Bank;
use App\Models\LinkGroup;
use App\Models\Usuario;
use Illuminate\Console\Command;

class TestApiUserLookup extends Command
{
    /**
     * Nome e assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'api:test-user-lookup {link? : URL ou domínio específico para buscar} {--all : Exibir todos os resultados} {--exact : Buscar correspondência exata do domínio}';

    /**
     * Descrição do comando.
     *
     * @var string
     */
    protected $description = 'Testa a busca de usuário por link conforme usado na API externa';

    /**
     * Executa o comando.
     */
    public function handle()
    {
        $link = $this->argument('link');
        $showAll = $this->option('all');
        
        $this->info('Testando busca de usuário por link na API externa');
        $this->line('');
        
        // Se um link específico foi fornecido
        if ($link) {
            $exactMatch = $this->option('exact');
            
            if ($exactMatch) {
                $this->info("Buscando usuário pelo domínio EXATO: " . $link);
            } else {
                $this->info("Buscando usuário que contenha o domínio: " . $link);
            }
            $this->line('');
            
            // Simular como a API realmente funciona - buscando pelo host exato da requisição
            $users = Usuario::whereHas('banks', function($query) use ($link, $exactMatch) {
                if ($exactMatch) {
                    // Busca em diferentes formatos possíveis dos links
                    $query->where(function($q) use ($link) {
                        // Formatos de string simples
                        $q->where('links', $link)
                          // Formato de string com vários links separados por vírgula
                          ->orWhere('links', 'LIKE', $link . ',%')
                          ->orWhere('links', 'LIKE', '%,' . $link . ',%')
                          ->orWhere('links', 'LIKE', '%,' . $link)
                          // Buscar em URLs JSON com diferentes formatos de protocolo
                          ->orWhere('links', 'LIKE', '%"' . $link . '"%')
                          // Buscar URLs com protocolo http/https no JSON
                          ->orWhere('links', 'LIKE', '%"http:\\/\\/' . $link . '"%')
                          ->orWhere('links', 'LIKE', '%"https:\\/\\/' . $link . '"%')
                          // Buscar apenas o domínio dentro do JSON sem protocolo
                          ->orWhere('links', 'LIKE', '%"' . str_replace('www.', '', $link) . '"%');
                        
                        // Buscar também apenas o nome do domínio base sem subdomínios
                        $parts = explode('.', $link);
                        if (count($parts) > 2) {
                            // Extrair domínio base (ex: de banking.nubank.com.br extrai nubank.com.br)
                            $baseDomain = $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
                            if ($baseDomain != $link) {
                                $q->orWhere('links', 'LIKE', '%' . $baseDomain . '%');
                            }
                        }
                    });
                } else {
                    // Busca ampla por substring
                    $query->where('links', 'LIKE', '%' . $link . '%');
                }
            })->get();
            
            if ($users->isEmpty()) {
                $this->error("Nenhum usuário encontrado para o link fornecido.");
                return 1;
            }
            
            foreach ($users as $user) {
                $this->outputUserDetails($user);
            }
            
            return 0;
        }
        
        // Se nenhum link foi fornecido, listar alguns exemplos
        if ($showAll) {
            $users = Usuario::has('banks')->get();
        } else {
            $users = Usuario::has('banks')->limit(5)->get();
            
            if ($users->count() < 5) {
                $users = Usuario::has('banks')->get();
            }
        }
        
        if ($users->isEmpty()) {
            $this->error("Nenhum usuário com links bancários encontrado no sistema.");
            return 1;
        }
        
        $this->info("Encontrados " . $users->count() . " usuários com links bancários:");
        $this->line('');
        
        foreach ($users as $user) {
            $this->outputUserDetails($user);
            $this->line(str_repeat('-', 50));
        }
        
        $this->info("Para testar um link específico, execute o comando com o argumento link:");
        $this->line("php artisan api:test-user-lookup exemplo.com.br");
        
        return 0;
    }
    
    /**
     * Exibe detalhes do usuário e seus links bancários.
     */
    private function outputUserDetails(Usuario $user)
    {
        $this->info("Usuário ID: " . $user->id);
        $this->info("Nome: " . $user->nome);
        $this->info("Email: " . $user->email);
        $this->line('');
        
        // Exibir links bancários
        $banks = Bank::where('usuario_id', $user->id)->get();
        $this->info("Links Bancários (" . $banks->count() . "):");
        
        foreach ($banks as $bank) {
            $this->line("  - ID: " . $bank->id);
            $this->line("    Nome: " . $bank->name);
            $this->line("    Instituição: " . ($bank->template ? $bank->template->name : 'Não definida'));
            
            // Extrair e exibir links/domínios
            $links = $bank->links ?? [];
            
            $this->line("    Links:");
            
            if (empty($links)) {
                $this->line("      * Nenhum link cadastrado");
            } elseif (is_string($links)) {
                // Se for uma string, pode ser um link único ou vários separados por vírgula
                $linkArray = explode(',', $links);
                foreach ($linkArray as $link) {
                    $this->line("      * " . trim($link));
                }
            } elseif (is_array($links)) {
                // Se for um array, pode ser um array simples ou um formato JSON com estrutura aninhada
                if (isset($links['urls']) && is_array($links['urls'])) {
                    foreach ($links['urls'] as $link) {
                        if (is_string($link)) {
                            $this->line("      * " . trim($link));
                        } elseif (is_array($link) && isset($link['url'])) {
                            $this->line("      * " . $link['url'] . (isset($link['label']) ? " (" . $link['label'] . ")" : ""));
                        } else {
                            $this->line("      * " . json_encode($link));
                        }
                    }
                } else {
                    // Tenta iterar sobre o array diretamente
                    foreach ($links as $key => $link) {
                        if (is_string($link)) {
                            $this->line("      * " . trim($link));
                        } elseif (is_array($link)) {
                            $this->line("      * [" . $key . "]: " . json_encode($link));
                        }
                    }
                }
            } else {
                $this->line("      * Formato de links não reconhecido: " . gettype($links));
            }
            
            $this->line('');
        }
        
        // Exibir grupos organizados
        $groups = LinkGroup::where('usuario_id', $user->id)->get();
        $this->info("Grupos Organizados (" . $groups->count() . "):");
        
        foreach ($groups as $group) {
            $this->line("  - ID: " . $group->id);
            $this->line("    Nome: " . $group->name);
            $this->line("    Links: " . $group->items->count());
            $this->line('');
        }
    }
}
