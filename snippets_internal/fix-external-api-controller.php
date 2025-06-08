<?php

require __DIR__.'/vendor/autoload.php';

// Função para exibir mensagem de status
function output($message) {
    echo $message . PHP_EOL;
}

output('Corrigindo o problema com o ExternalApiController...');

// Caminho para o controller
$controllerPath = __DIR__ . '/app/Http/Controllers/Admin/ExternalApiController.php';

// Verificar se o controlador existe e criar backup
if (file_exists($controllerPath)) {
    $backupPath = $controllerPath . '.bak.' . time();
    copy($controllerPath, $backupPath);
    output("Backup do controller criado em: " . $backupPath);
} else {
    output("Controller não encontrado! Criando novo arquivo.");
}

// Conteúdo do novo controller
$controllerContent = '<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExternalApi;
use App\Models\CloudflareDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExternalApiController extends Controller
{
    /**
     * Lista as APIs externas disponíveis
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $apis = ExternalApi::all();
        return view("admin.external-apis.index", compact("apis"));
    }
    
    /**
     * Exibe o formulário para criar uma nova API externa
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view("admin.external-apis.create");
    }
    
    /**
     * Armazena uma nova API externa
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "type" => "required|string|in:cloudflare",
            "api_key" => "required|string",
            "api_email" => "required_if:type,cloudflare|nullable|string|email",
            "api_token" => "nullable|string",
            "account_id" => "nullable|string",
            "active" => "boolean"
        ]);
        
        $api = ExternalApi::create($validated);
        
        return redirect()->route("admin.external-apis.index")
            ->with("success", "API Externa criada com sucesso!");
    }
    
    /**
     * Exibe uma API externa específica
     *
     * @param  \App\Models\ExternalApi  $externalApi
     * @return \Illuminate\View\View
     */
    public function show(ExternalApi $externalApi)
    {
        return view("admin.external-apis.show", compact("externalApi"));
    }
    
    /**
     * Exibe o formulário para editar uma API externa
     *
     * @param  \App\Models\ExternalApi  $externalApi
     * @return \Illuminate\View\View
     */
    public function edit(ExternalApi $externalApi)
    {
        return view("admin.external-apis.edit", compact("externalApi"));
    }
    
    /**
     * Atualiza uma API externa específica
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExternalApi  $externalApi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ExternalApi $externalApi)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "type" => "required|string|in:cloudflare",
            "api_key" => "required|string",
            "api_email" => "required_if:type,cloudflare|nullable|string|email",
            "api_token" => "nullable|string",
            "account_id" => "nullable|string",
            "active" => "boolean"
        ]);
        
        $externalApi->update($validated);
        
        return redirect()->route("admin.external-apis.index")
            ->with("success", "API Externa atualizada com sucesso!");
    }
    
    /**
     * Remove uma API externa
     *
     * @param  \App\Models\ExternalApi  $externalApi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ExternalApi $externalApi)
    {
        $externalApi->delete();
        
        return redirect()->route("admin.external-apis.index")
            ->with("success", "API Externa removida com sucesso!");
    }
    
    /**
     * Lista os domínios de uma API externa (Cloudflare)
     *
     * @param  \App\Models\ExternalApi  $externalApi
     * @return \Illuminate\View\View
     */
    public function listDomains(ExternalApi $externalApi)
    {
        try {
            if ($externalApi->type !== "cloudflare") {
                return redirect()->back()->with("error", "Esta funcionalidade é apenas para APIs Cloudflare");
            }
            
            // Inicializar SDK Cloudflare (exemplo)
            $key = $externalApi->api_key;
            $email = $externalApi->api_email;
            $token = $externalApi->api_token;
            
            // Simular a busca de domínios (em um projeto real, usaria o SDK do Cloudflare)
            // Neste exemplo, vamos buscar do banco de dados
            $savedDomains = CloudflareDomain::where("external_api_id", $externalApi->id)->get();
            
            $domains = [];
            foreach ($savedDomains as $savedDomain) {
                // Buscar registros DNS associados
                $recordsCount = $savedDomain->dnsRecords()->count();
                
                // Informações de usuários associados
                $usersCount = $savedDomain->usuarios()->count();
                
                $domains[] = [
                    "id" => $savedDomain->zone_id,
                    "name" => $savedDomain->name,
                    "status" => $savedDomain->status,
                    "nameservers" => $savedDomain->name_servers,
                    "records_count" => $recordsCount,
                    "users_count" => $usersCount,
                    "is_ghost" => $savedDomain->is_ghost,
                    "created_at" => $savedDomain->created_at,
                    "updated_at" => $savedDomain->updated_at
                ];
            }
            
            return view("admin.external-apis.domains", [
                "api" => $externalApi,
                "domains" => $domains
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao listar domínios: " . $e->getMessage());
            return redirect()->back()->with("error", "Erro ao listar domínios: " . $e->getMessage());
        }
    }
    
    /**
     * Atualiza o status Ghost de um domínio Cloudflare
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGhostStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                "domain_id" => "required|string",
                "is_ghost" => "required|boolean",
            ]);
            
            $domain = CloudflareDomain::where("zone_id", $validated["domain_id"])->first();
            
            if (!$domain) {
                return response()->json([
                    "success" => false,
                    "message" => "Domínio não encontrado"
                ], 404);
            }
            
            $domain->is_ghost = $request->is_ghost;
            $domain->save();
            
            return response()->json([
                "success" => true,
                "message" => "Status Ghost atualizado com sucesso",
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao atualizar status Ghost: " . $e->getMessage());
            return response()->json([
                "success" => false,
                "message" => "Erro ao atualizar status Ghost: " . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtém informações do Ghost para um domínio específico
     *
     * @param  string  $domain ID da zona/domínio
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGhostInfo($domain)
    {
        try {
            $domainInfo = \App\Models\CloudflareDomain::where("zone_id", $domain)->first();
            
            if (!$domainInfo) {
                return response()->json([
                    "success" => false,
                    "message" => "Domínio não encontrado"
                ]);
            }
            
            $subdomainCount = $domainInfo->dnsRecords()->count();
            $userCount = $domainInfo->usuarios()->count();
            
            return response()->json([
                "success" => true,
                "subdomains" => $subdomainCount,
                "users" => $userCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Erro ao obter informações: " . $e->getMessage()
            ]);
        }
    }
}';

// Escrever o conteúdo no arquivo
file_put_contents($controllerPath, $controllerContent);
output('Controller atualizado com sucesso!');

// Agora vamos verificar e atualizar as rotas
$routesPath = __DIR__ . '/routes/web.php';

if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    $backupPath = $routesPath . '.bak.' . time();
    copy($routesPath, $backupPath);
    output("Backup das rotas criado em: " . $backupPath);
    
    // Verificar se as rotas para ExternalApiController já existem
    if (strpos($routesContent, 'ExternalApiController') === false) {
        output('Adicionando rotas para o ExternalApiController...');
        
        // Buscar o grupo de rotas do admin
        $adminRoutePattern = "Route::middleware\(\['auth', [^)]+\]\)->prefix\('admin'\)->name\('admin.'\)->group\(function\(\) {";
        if (preg_match($adminRoutePattern, $routesContent, $matches, PREG_OFFSET_CAPTURE)) {
            $position = $matches[0][1] + strlen($matches[0][0]);
            
            // Adicionar as rotas
            $externalApiRoutes = "\n    // Gerenciamento de APIs Externas
    Route::resource('external-apis', \App\Http\Controllers\Admin\ExternalApiController::class);
    Route::get('external-apis/{externalApi}/domains', [\App\Http\Controllers\Admin\ExternalApiController::class, 'listDomains'])
        ->name('external-apis.domains');
    Route::post('external-apis/update-ghost', [\App\Http\Controllers\Admin\ExternalApiController::class, 'updateGhostStatus'])
        ->name('external-apis.update-ghost');
    Route::get('domains/{domain}/ghost-info', [\App\Http\Controllers\Admin\ExternalApiController::class, 'getGhostInfo'])
        ->name('domains.ghost-info');";
            
            $routesContent = substr_replace($routesContent, $externalApiRoutes, $position, 0);
            file_put_contents($routesPath, $routesContent);
            output('Rotas para ExternalApiController adicionadas com sucesso!');
        } else {
            output('Não foi possível encontrar o grupo de rotas do admin para adicionar as novas rotas.');
        }
    } else {
        output('Rotas para ExternalApiController já existem.');
    }
} else {
    output('ERRO: Arquivo de rotas não encontrado!');
}

// Limpar cache do Laravel
output('Limpando cache do Laravel...');
system('php artisan cache:clear');
system('php artisan view:clear');
system('php artisan route:clear');
system('php artisan config:clear');

output('Correção concluída! Por favor, recarregue a página para verificar se o problema foi resolvido.');
