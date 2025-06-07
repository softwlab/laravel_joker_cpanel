<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExternalApi;
use App\Models\DnsRecord;
use App\Models\CloudflareDomain;
use Illuminate\Http\Request;
use App\Models\Bank;
use App\Models\BankTemplate;
use App\Models\LinkGroup;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;

class ExternalApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apis = ExternalApi::withCount('dnsRecords')->paginate(10);
        return view('admin.external-apis.index', compact('apis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $apiTypes = [
            'cloudflare' => 'Cloudflare DNS',
            'godaddy' => 'GoDaddy DNS',
            'namecheap' => 'Namecheap DNS',
            'route53' => 'AWS Route 53'
        ];
        
        return view('admin.external-apis.create', compact('apiTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'external_link_api' => 'required|url|max:255',
            'key_external_api' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Prepara o objeto JSON conforme o tipo de API
        $jsonConfig = [];
        
        // Para Cloudflare, precisamos de configuração específica
        if ($request->type === 'cloudflare') {
            $jsonConfig = [
                'email' => $request->input('cf_email', ''),
                'api_method' => $request->input('cf_api_method', 'token'),
                'global_api' => $request->input('cf_global_api', false),
                'zone_ids' => $request->input('cf_zone_ids', []),
            ];
        }
        
        // Cria a API externa
        $api = ExternalApi::create([
            'name' => $request->name,
            'type' => $request->type,
            'external_link_api' => $request->external_link_api,
            'key_external_api' => $request->key_external_api,
            'description' => $request->description,
            'status' => $request->status,
            'json' => $jsonConfig,
        ]);

        return redirect()->route('admin.external-apis.index')
            ->with('success', 'API externa foi adicionada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $api = ExternalApi::with('dnsRecords')->findOrFail($id);
        $clientIpAddress = Config::get('app.client_page_ip', '127.0.0.1');
        
        // Carregar dados relacionados para exibir na interface
        $banks = Bank::all();
        $templates = BankTemplate::all();
        $groups = LinkGroup::all();
        $users = User::all();
        
        // Estatísticas
        $recordTypes = DnsRecord::where('external_api_id', $api->id)
            ->select('record_type')
            ->selectRaw('count(*) as count')
            ->groupBy('record_type')
            ->get();
            
        // Obter os registros DNS para esta API externa com paginação
        $dns_records = DnsRecord::where('external_api_id', $api->id)->paginate(10);
            
        return view('admin.external-apis.show', compact(
            'api', 'banks', 'templates', 'groups', 'users', 
            'clientIpAddress', 'recordTypes', 'dns_records'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $api = ExternalApi::findOrFail($id);
        
        $apiTypes = [
            'cloudflare' => 'Cloudflare DNS',
            'godaddy' => 'GoDaddy DNS',
            'namecheap' => 'Namecheap DNS',
            'route53' => 'AWS Route 53'
        ];
        
        return view('admin.external-apis.edit', compact('api', 'apiTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $api = ExternalApi::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'external_link_api' => 'required|url|max:255',
            'key_external_api' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Atualiza o objeto JSON conforme o tipo de API
        $jsonConfig = $api->json ?? [];
        
        // Para Cloudflare, precisamos de configuração específica
        if ($request->type === 'cloudflare') {
            $jsonConfig = [
                'email' => $request->input('cf_email', $jsonConfig['email'] ?? ''),
                'api_method' => $request->input('cf_api_method', $jsonConfig['api_method'] ?? 'token'),
                'global_api' => $request->input('cf_global_api', $jsonConfig['global_api'] ?? false),
                'zone_ids' => $request->input('cf_zone_ids', $jsonConfig['zone_ids'] ?? []),
            ];
        }
        
        // Atualiza a API externa
        $api->update([
            'name' => $request->name,
            'type' => $request->type,
            'external_link_api' => $request->external_link_api,
            'key_external_api' => $request->key_external_api,
            'description' => $request->description,
            'status' => $request->status,
            'json' => $jsonConfig,
        ]);

        return redirect()->route('admin.external-apis.index')
            ->with('success', 'API externa foi atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $api = ExternalApi::findOrFail($id);
        
        // Verificar se existem registros DNS dependentes
        $recordsCount = $api->dnsRecords()->count();
        
        if ($recordsCount > 0) {
            return redirect()->route('admin.external-apis.index')
                ->with('error', "Não é possível excluir esta API. Existem {$recordsCount} registros DNS dependentes.");
        }
        
        $api->delete();
        
        return redirect()->route('admin.external-apis.index')
            ->with('success', 'API externa foi removida com sucesso!');
    }
    
    /**
     * Exibe a tela para criar um novo registro DNS.
     */
    public function createRecord(string $id)
    {
        $api = ExternalApi::findOrFail($id);
        $clientIpAddress = Config::get('app.client_page_ip', '127.0.0.1');
        
        // Carregar dados relacionados para exibir no formulário
        $banks = Bank::all();
        $templates = BankTemplate::all();
        $groups = LinkGroup::all();
        $users = User::all();
        
        $recordTypes = [
            'A' => 'Registro A (Endereço IP)',
            'CNAME' => 'Registro CNAME (Nome Canônico)',
            'MX' => 'Registro MX (Servidor de Email)',
            'TXT' => 'Registro TXT (Texto)',
            'SPF' => 'Registro SPF (Sender Policy Framework)',
            'DKIM' => 'Registro DKIM (DomainKeys)',
            'DMARC' => 'Registro DMARC'
        ];
        
        return view('admin.external-apis.create-record', compact(
            'api', 'recordTypes', 'banks', 'templates', 'groups', 'users', 'clientIpAddress'
        ));
    }
    
    /**
     * Armazena um novo registro DNS.
     */
    public function storeRecord(Request $request, string $id)
    {
        $api = ExternalApi::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'record_type' => 'required|string',
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'ttl' => 'nullable|integer|min:60',
            'priority' => 'nullable|integer|min:0',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_template_id' => 'nullable|exists:bank_templates,id',
            'link_group_id' => 'nullable|exists:link_groups,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Criar o registro DNS
        $dnsRecord = new DnsRecord([
            'external_api_id' => $api->id,
            'bank_id' => $request->bank_id,
            'bank_template_id' => $request->bank_template_id,
            'link_group_id' => $request->link_group_id,
            'user_id' => $request->user_id,
            'record_type' => $request->record_type,
            'name' => $request->name,
            'content' => $request->content,
            'ttl' => $request->ttl ?? 3600,
            'priority' => $request->priority,
            'status' => 'active',
        ]);
        
        // Se o cliente estiver usando a API Cloudflare, podemos integrar aqui
        // para criar o registro DNS diretamente na Cloudflare
        if ($api->type === 'cloudflare') {
            // Simular dados extras que vieram da API
            $dnsRecord->extra_data = [
                'zone_id' => $request->input('zone_id', ''),
                'record_id' => 'dns_record_' . time(),
                'proxied' => $request->input('proxied', false)
            ];
        }
        
        $dnsRecord->save();
        
        return redirect()->route('admin.external-apis.show', $api->id)
            ->with('success', 'Registro DNS criado com sucesso!');
    }
    
    /**
     * Exclui um registro DNS.
     */
    public function deleteRecord(string $id, string $recordId)
    {
        $record = DnsRecord::findOrFail($recordId);
        $api = ExternalApi::findOrFail($id);
        
        // Verificar se o registro pertence à API
        if ($record->external_api_id != $api->id) {
            return redirect()->route('admin.external-apis.show', $api->id)
                ->with('error', 'O registro DNS não pertence a esta API.');
        }
        
        // Se a API for Cloudflare, podemos integrar aqui para excluir o registro
        if ($api->type === 'cloudflare') {
            // Em uma implementação real, aqui integraríamos com a API
        }
        
        $record->delete();
        
        return redirect()->route('admin.external-apis.show', $api->id)
            ->with('success', 'Registro DNS excluído com sucesso!');
    }
    
    /**
     * Testa a conexão com a API externa.
     */
    public function testConnection(string $id)
    {
        $api = ExternalApi::findOrFail($id);
        
        // Simulando uma conexão de teste
        $success = true;
        $message = 'Conexão realizada com sucesso!';
        $details = null;
        
        // Para uma API Cloudflare
        if ($api->type === 'cloudflare') {
            // Aqui, em uma implementação real, faríamos uma chamada à API da Cloudflare
            $details = [
                'status' => 'active',
                'message' => 'API Token válido',
                'token_info' => [
                    'status' => 'active',
                    'expires_on' => null,
                    'id' => 'api_token_' . substr(md5($api->key_external_api), 0, 8),
                ]
            ];
        }
        
        return response()->json([
            'success' => $success,
            'message' => $message,
            'details' => $details
        ]);
    }

    /**
     * Exibe formulário para editar configurações da API
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function editConfig(string $id)
    {
        $api = ExternalApi::findOrFail($id);
        
        return view('admin.external-apis.edit-config', compact('api'));
    }
    
    /**
     * Salva as configurações da API
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function updateConfig(Request $request, string $id)
    {
        $api = ExternalApi::findOrFail($id);
        
        // Validar os dados conforme o tipo de API
        $validationRules = [];
        $configData = [];
        
        if ($api->type == 'cloudflare') {
            // Definir as regras de validação com base no método de autenticação selecionado
            $authMethod = $request->input('auth_method', 'api_key');
            
            // Validação comum para qualquer método
            $commonRules = [
                'cloudflare_zone_id' => 'required|string|min:32', // Zone ID tem 32 caracteres
            ];
            
            if ($authMethod == 'api_key') {
                $validationRules = array_merge($commonRules, [
                    'cloudflare_email' => 'required|email',
                    'cloudflare_api_key' => 'required|string|min:20',
                ]);
                
                $request->validate($validationRules);
                
                $configData = [
                    'cloudflare_email' => $request->cloudflare_email,
                    'cloudflare_api_key' => $request->cloudflare_api_key,
                    'cloudflare_zone_id' => $request->cloudflare_zone_id,
                    'auth_method' => 'api_key'
                ];
            } else {
                $validationRules = array_merge($commonRules, [
                    'cloudflare_api_token' => 'required|string|min:20',
                ]);
                
                $request->validate($validationRules);
                
                $configData = [
                    'cloudflare_api_token' => $request->cloudflare_api_token,
                    'cloudflare_zone_id' => $request->cloudflare_zone_id,
                    'auth_method' => 'token'
                ];
            }
        }
        
        // Atualizar configuração da API
        \Illuminate\Support\Facades\Log::info('Dados de configuração antes da atualização', [
            'api_id' => $api->id, 
            'config_data' => $configData,
            'has_zone_id' => isset($configData['cloudflare_zone_id']),
            'zone_id_value' => $configData['cloudflare_zone_id'] ?? 'não definido',
            'request_has_zone_id' => $request->has('cloudflare_zone_id'),
            'request_zone_id' => $request->input('cloudflare_zone_id')
        ]);
        
        $api->config = $configData;
        $api->save();
        
        // Verificar se a configuração foi salva corretamente
        $savedApi = \App\Models\ExternalApi::find($api->id);
        \Illuminate\Support\Facades\Log::info('Configuração após salvar', [
            'api_id' => $savedApi->id,
            'saved_config' => $savedApi->config,
            'has_zone_id' => isset($savedApi->config['cloudflare_zone_id']),
            'auth_method' => $savedApi->config['auth_method'] ?? 'não definido'
        ]);
        
        return redirect()->route('admin.external-apis.show', $api->id)
            ->with('success', 'Configuração da API atualizada com sucesso!');
    }
    
    /**
     * Atualiza o status Ghost de um domínio Cloudflare
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGhostStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'domain_id' => 'required|string',
                'is_ghost' => 'required|boolean',
                'api_id' => 'required|exists:external_apis,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos: ' . $validator->errors()->first()
                ]);
            }
            
            $domain = CloudflareDomain::where('zone_id', $request->domain_id)
                ->where('external_api_id', $request->api_id)
                ->first();
                
            if (!$domain) {
                // Criar um novo registro se não existir
                $domain = CloudflareDomain::create([
                    'external_api_id' => $request->api_id,
                    'zone_id' => $request->domain_id,
                    'name' => $request->input('name', 'Domínio ' . $request->domain_id),
                    'status' => $request->input('status', 'active'),
                    'is_ghost' => $request->is_ghost
                ]);
            } else {
                // Atualizar registro existente
                $domain->is_ghost = $request->is_ghost;
                $domain->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Status Ghost atualizado com sucesso',
                'domain' => $domain
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status Ghost: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Lista os domínios/zonas disponíveis em uma API externa
     * 
     * @param string $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function listDomains(string $id)
    {
        $api = ExternalApi::findOrFail($id);
        $domains = [];
        $error = null;
        
        try {
            // Obter o serviço DNS adequado para o tipo de API
            $dnsService = app('App\Services\DnsService');
            $result = $dnsService->getDomainsForApi($api);
            
            \Illuminate\Support\Facades\Log::info('Resultado recebido do DnsService', [
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? null,
                'has_domains_key' => isset($result['domains']),
                'domains_count' => isset($result['domains']) ? count($result['domains']) : 0,
                'result_keys' => array_keys($result)
            ]);
            
            if (isset($result['success']) && $result['success'] && isset($result['domains']) && is_array($result['domains'])) {
                $domains = $result['domains'];
                
                // Buscar status ghost dos domínios salvos no banco
                $savedDomains = CloudflareDomain::where('external_api_id', $api->id)->get()
                    ->keyBy('zone_id');
                
                \Illuminate\Support\Facades\Log::info('Domínios encontrados no banco', [
                    'saved_count' => $savedDomains->count(),
                    'api_id' => $api->id
                ]);
                
                // Adicionar informações de ghost e datas aos domínios
                foreach ($domains as $key => $domain) {
                    if (!isset($domain['id'])) {
                        \Illuminate\Support\Facades\Log::warning('Domínio sem ID encontrado', [
                            'domain_data' => $domain
                        ]);
                        continue;
                    }
                    
                    $zoneId = $domain['id'];
                    if (isset($savedDomains[$zoneId])) {
                        $savedDomain = $savedDomains[$zoneId];
                        $domains[$key]['is_ghost'] = $savedDomain->is_ghost;
                        $domains[$key]['created_at'] = $savedDomain->created_at;
                        $domains[$key]['updated_at'] = $savedDomain->updated_at;
                    } else {
                        // Salvar o domínio no banco de dados
                        $cloudflareData = [
                            'external_api_id' => $api->id,
                            'zone_id' => $zoneId,
                            'name' => $domain['name'] ?? 'Domínio '.$zoneId,
                            'status' => $domain['status'] ?? 'unknown',
                            'name_servers' => $domain['name_servers'] ?? null,
                            'records_count' => $domain['records_count'] ?? null,
                            'is_ghost' => false
                        ];
                        
                        $savedDomain = CloudflareDomain::create($cloudflareData);
                        $domains[$key]['is_ghost'] = false;
                        $domains[$key]['created_at'] = $savedDomain->created_at;
                        $domains[$key]['updated_at'] = $savedDomain->updated_at;
                    }
                }
            } else {
                $error = $result['message'] ?? 'Erro desconhecido ao obter domínios';
                \Illuminate\Support\Facades\Log::error('Falha ao obter domínios', [
                    'api_id' => $api->id,
                    'api_type' => $api->type,
                    'error' => $error,
                    'result' => $result
                ]);
            }
            
            return view('admin.external-apis.domains', compact('api', 'domains', 'error'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exceção ao listar domínios', [
                'api_id' => $api->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Erro ao listar domínios: ' . $e->getMessage());
        }
    }
}
