<?php

namespace App\Http\Controllers\Admin;

use App\Models\CloudflareDomain;
use App\Models\DnsRecord;
use App\Models\ExternalApi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $apis = ExternalApi::paginate(15); // Using pagination with 15 items per page
        return view('admin.external-apis.index', compact('apis'));
    }
    
    /**
     * Exibe o formulário para criar uma nova API externa
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.external-apis.create');
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
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:cloudflare',
            'api_key' => 'required|string',
            'api_email' => 'required_if:type,cloudflare|nullable|string|email',
            'api_token' => 'nullable|string',
            'account_id' => 'nullable|string',
            'active' => 'boolean'
        ]);
        
        $api = ExternalApi::create($validated);
        
        return redirect()->route('admin.external-apis.index')
            ->with('success', 'API Externa criada com sucesso!');
    }
    
    /**
     * Exibe uma API externa específica
     *
     * @param  \App\Models\ExternalApi  $externalApi
     * @return \Illuminate\View\View
     */
    public function show(ExternalApi $externalApi)
    {
        // Renomeando variável para 'api' para combinar com o que é esperado na view
        $api = $externalApi;
        
        // Carregando os registros DNS associados a esta API
        $dns_records = $externalApi->dnsRecords()->paginate(15);
        
        return view("admin.external-apis.show", compact("api", "dns_records"));
    }
    
    /**
     * Exibe o formulário para editar uma API externa
     *
     * @param  \App\Models\ExternalApi  $externalApi
     * @return \Illuminate\View\View
     */
    public function edit(ExternalApi $externalApi)
    {
        return view('admin.external-apis.edit', compact('externalApi'));
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
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:cloudflare',
            'api_key' => 'required|string',
            'api_email' => 'required_if:type,cloudflare|nullable|string|email',
            'api_token' => 'nullable|string',
            'account_id' => 'nullable|string',
            'active' => 'boolean'
        ]);
        
        $externalApi->update($validated);
        
        return redirect()->route('admin.external-apis.index')
            ->with('success', 'API Externa atualizada com sucesso!');
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
        
        return redirect()->route('admin.external-apis.index')
            ->with('success', 'API Externa removida com sucesso!');
    }
    
    /**
     * Lista os domínios de uma API externa (Cloudflare)
     *
     * @param  \App\Models\ExternalApi  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function listDomains(ExternalApi $id)
    {
        try {
            if ($id->type !== 'cloudflare') {
                return redirect()->back()->with('error', 'Esta funcionalidade é apenas para APIs Cloudflare');
            }
            
            Log::info("Iniciando sincronização com Cloudflare para API {$id->id}");
            
            // Configurar o serviço Cloudflare usando as credenciais da API
            $cloudflareService = new \App\Services\CloudflareService($id);
            
            // Tentar sincronizar com a Cloudflare para obter domínios atualizados
            try {
                Log::info("Tentando obter domínios atuais da API Cloudflare");
                $response = $cloudflareService->getZones();
                
                if (!isset($response['success']) || !$response['success']) {
                    throw new \Exception("Erro ao obter domínios: " . ($response['message'] ?? 'Erro desconhecido'));
                }
                
                $apiDomains = $response['zones'] ?? [];
                Log::info("Domínios obtidos da API: " . count($apiDomains) . " domínios");
                
                // Sincronizar os domínios obtidos com o banco de dados
                foreach ($apiDomains as $apiDomain) {
                    CloudflareDomain::updateOrCreate(
                        [
                            'external_api_id' => $id->id,
                            'zone_id' => $apiDomain['id']
                        ],
                        [
                            'name' => $apiDomain['name'],
                            'status' => $apiDomain['status'],
                            'name_servers' => isset($apiDomain['name_servers']) ? $apiDomain['name_servers'] : [],
                            'updated_at' => now()
                        ]
                    );
                }
                
                Log::info("Sincronização de domínios concluída com sucesso");
            } catch (\Exception $syncException) {
                Log::error("Erro na sincronização com Cloudflare: " . $syncException->getMessage());
                // Continuamos mesmo com erro, para mostrar os dados locais
            }
            
            // Obter todos os domínios do banco de dados
            $cloudflareDomainsCollection = CloudflareDomain::where('external_api_id', $id->id)
                ->orderBy('name')
                ->get();
            
            Log::info("Domínios no banco de dados: " . $cloudflareDomainsCollection->count());
            
            // Buscar todos os registros DNS de uma vez para evitar múltiplas consultas
            $allDnsRecords = DnsRecord::where('external_api_id', $id->id)->get();
            
            $domains = [];
            $seedDomainNames = ['example.com', 'teste.com', 'test.com']; // Domínios que foram criados via seed
            $showSeedDomains = request()->has('show_all'); // Parâmetro da URL para mostrar todos os domínios
            
            foreach($cloudflareDomainsCollection as $savedDomain) {
                // Verificar se o domínio é um domínio de seed/teste
                $isSeedDomain = in_array($savedDomain->name, $seedDomainNames);
                
                // Pular domínios de seed se não estiver com show_all
                if ($isSeedDomain && !$showSeedDomains) {
                    Log::info("Pulando domínio seed: " . $savedDomain->name);
                    continue;
                }
                
                // Filtrar registros DNS que pertencem a este domínio
                $domainName = $savedDomain->name;
                $filteredRecords = $allDnsRecords->filter(function($record) use ($domainName) {
                    return str_ends_with($record->name, $domainName) || 
                           $record->name === $domainName ||
                           strpos($record->name, '.' . $domainName) !== false;
                });
                
                $recordsCount = $filteredRecords->count();
                
                // Informações de usuários associados
                $usersCount = $savedDomain->usuarios()->count();
                
                Log::info('Domain: ' . $savedDomain->name . ', Records count: ' . $recordsCount . ', Users: ' . $usersCount);
                
                $domains[] = [
                    'id' => $savedDomain->zone_id,
                    'name' => $savedDomain->name,
                    'status' => $savedDomain->status,
                    'nameservers' => $savedDomain->name_servers,
                    'records_count' => $recordsCount,
                    'users_count' => $usersCount,
                    'is_ghost' => $savedDomain->is_ghost,
                    'is_seed' => $isSeedDomain,
                    'created_at' => $savedDomain->created_at,
                    'updated_at' => $savedDomain->updated_at
                ];
            }
            
            // Garantimos que $domains é sempre um array PHP nativo
            $domains = is_array($domains) ? $domains : (is_object($domains) && method_exists($domains, 'toArray') ? $domains->toArray() : []);
            
            // Contar domínios ativos e inativos
            $activeDomainsCount = 0;
            $inactiveDomainsCount = 0;
            $seedDomainsCount = 0;
            
            foreach ($domains as $domain) {
                if (isset($domain['is_seed']) && $domain['is_seed']) {
                    $seedDomainsCount++;
                }
                
                if ($domain['status'] == 'active') {
                    $activeDomainsCount++;
                } else {
                    $inactiveDomainsCount++;
                }
            }
            
            return view('admin.external-apis.domains', [
                'api' => $id,
                'domains' => $domains,
                'activeDomainsCount' => $activeDomainsCount,
                'inactiveDomainsCount' => $inactiveDomainsCount,
                'seedDomainsCount' => $seedDomainsCount, 
                'showingSeedDomains' => $showSeedDomains,
                'error' => null // Inicializando $error como null para evitar erro de variável indefinida
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao listar domínios: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->back()->with('error', 'Erro ao listar domínios: ' . $e->getMessage());
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
            // Log completo para debug
            \Illuminate\Support\Facades\Log::info('Requisição Ghost recebida: ' . json_encode($request->all()));
            
            // Validar entrada
            $request->validate([
                'domain_id' => 'required',
                'is_ghost' => 'required'
            ]);
            
            // Extrair e converter valores
            $domain_id = $request->input('domain_id');
            $is_ghost = (bool) $request->input('is_ghost');
            
            \Illuminate\Support\Facades\Log::info("Processando atualização ghost para domínio ID: {$domain_id}, valor: " . ($is_ghost ? 'true' : 'false'));
            
            // Buscar domínio - primeiro por ID, depois por zone_id
            $domain = CloudflareDomain::find($domain_id);
            
            if (!$domain) {
                $domain = CloudflareDomain::where('zone_id', $domain_id)->first();
            }
            
            if (!$domain) {
                \Illuminate\Support\Facades\Log::warning("Domínio não encontrado: {$domain_id}");
                return response()->json([
                    'success' => false,
                    'message' => "Domínio não encontrado com ID/zone_id: {$domain_id}"
                ], 404);
            }
            
            \Illuminate\Support\Facades\Log::info("Domínio encontrado: {$domain->name} (ID: {$domain->id}, Zone ID: {$domain->zone_id})");
            
            // Registrar valor atual antes da mudança
            $oldValue = $domain->is_ghost ? 'true' : 'false';
            
            // Atualizar e salvar
            $domain->is_ghost = $is_ghost;
            $saved = $domain->save();
            
            // Verificar se foi salvo e registrar resultado
            if ($saved) {
                \Illuminate\Support\Facades\Log::info("Atualização do status ghost bem-sucedida para {$domain->name} - Antigo: {$oldValue}, Novo: " . ($domain->is_ghost ? 'true' : 'false'));
                
                // Recarregar domínio do banco para confirmar que a alteração foi persistida
                $refreshed = CloudflareDomain::find($domain->id);
                $refreshValue = $refreshed->is_ghost ? 'true' : 'false';
                
                \Illuminate\Support\Facades\Log::info("Valor após recarregar do banco: {$refreshValue}");
                
                return response()->json([
                    'success' => true,
                    'message' => 'Status Ghost atualizado com sucesso para ' . ($is_ghost ? 'ativado' : 'desativado'),
                    'status' => $refreshed->is_ghost,
                    'domain_name' => $domain->name,
                    'domain_id' => $domain->id
                ]);
            } else {
                \Illuminate\Support\Facades\Log::warning("Falha ao salvar alteração do status ghost para {$domain->name}");
                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao salvar alteração do status ghost'
                ], 500);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao processar atualização de ghost: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar a solicitação: ' . $e->getMessage()
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
            $domainInfo = CloudflareDomain::where('zone_id', $domain)->first();
            
            if (!$domainInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Domínio não encontrado'
                ]);
            }
            
            $subdomainCount = $domainInfo->dnsRecords()->count();
            $userCount = $domainInfo->usuarios()->count();
            
            return response()->json([
                'success' => true,
                'subdomains' => $subdomainCount,
                'users' => $userCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter informações: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Exibe o formulário para criar um novo registro DNS para uma API externa
     *
     * @param  \App\Models\ExternalApi  $externalApi
     * @return \Illuminate\View\View
     */
    public function createRecord(ExternalApi $externalApi)
    {
        $api = $externalApi; // Renomear para combinar com as views
        return view('admin.external-apis.create-record', compact('api'));
    }
}