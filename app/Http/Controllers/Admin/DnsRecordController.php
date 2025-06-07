<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DnsRecord;
use App\Models\ExternalApi;
use App\Models\Bank;
use App\Models\BankTemplate;
use App\Models\LinkGroup;
use App\Models\User;
use App\Services\DnsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DnsRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DnsRecord::with('externalApi');
        
        // Aplicar filtros
        if ($request->filled('type')) {
            $query->where('record_type', $request->input('type'));
        }
        
        if ($request->filled('api')) {
            $query->where('external_api_id', $request->input('api'));
        }
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        $records = $query->paginate(15);
        return view('admin.dns-records.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $apis = ExternalApi::where('status', 'active')->get();
        $banks = Bank::all();
        $templates = BankTemplate::all();
        $groups = LinkGroup::all();
        $users = User::all();
        $clientIpAddress = Config::get('app.client_page_ip', '127.0.0.1');
        
        $recordTypes = [
            'A' => 'Registro A (Endereço IP)',
            'CNAME' => 'Registro CNAME (Nome Canônico)',
            'MX' => 'Registro MX (Servidor de Email)',
            'TXT' => 'Registro TXT (Texto)',
            'SPF' => 'Registro SPF (Sender Policy Framework)',
            'DKIM' => 'Registro DKIM (DomainKeys)',
            'DMARC' => 'Registro DMARC'
        ];
        
        return view('admin.dns-records.create', compact(
            'apis', 'banks', 'templates', 'groups', 'users', 'clientIpAddress', 'recordTypes'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'external_api_id' => 'required|exists:external_apis,id',
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
        
        $api = ExternalApi::findOrFail($request->external_api_id);
        
        // Criar o registro DNS
        $dnsRecord = new DnsRecord([
            'external_api_id' => $request->external_api_id,
            'bank_id' => $request->bank_id,
            'bank_template_id' => $request->bank_template_id,
            'link_group_id' => $request->link_group_id,
            'user_id' => $request->user_id,
            'record_type' => $request->record_type,
            'name' => $request->name,
            'content' => $request->content,
            'ttl' => $request->ttl ?? 3600,
            'priority' => $request->priority,
            'status' => $request->input('status', 'active'),
        ]);
        
        // Configurar dados extras específicos do tipo de API
        if ($api->type === 'cloudflare') {
            $dnsRecord->extra_data = [
                'zone_id' => $request->input('zone_id', $api->config['cloudflare_zone_id'] ?? ''),
                'proxied' => $request->has('proxied')
            ];
        }
        
        $dnsRecord->save();
        
        // Tentar criar o registro na API externa
        try {
            $dnsService = new DnsService();
            $result = $dnsService->createRecord($dnsRecord);
            
            if ($result['success']) {
                return redirect()->route('admin.dns-records.index')
                    ->with('success', 'Registro DNS criado com sucesso na API externa!');
            } else {
                // O registro foi salvo localmente, mas não na API externa
                return redirect()->route('admin.dns-records.index')
                    ->with('warning', 'Registro DNS criado localmente, mas falhou na API externa: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao criar registro DNS na API: ' . $e->getMessage(), [
                'record' => $dnsRecord->toArray(),
                'api_id' => $api->id
            ]);
            
            return redirect()->route('admin.dns-records.index')
                ->with('warning', 'Registro DNS criado localmente, mas ocorreu um erro na API externa.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = DnsRecord::with(['externalApi', 'bank', 'bankTemplate', 'linkGroup', 'user'])->findOrFail($id);
        return view('admin.dns-records.show', compact('record'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $record = DnsRecord::findOrFail($id);
        $apis = ExternalApi::where('status', 'active')->get();
        $banks = Bank::all();
        $templates = BankTemplate::all();
        $groups = LinkGroup::all();
        $users = User::all();
        $clientIpAddress = Config::get('app.client_page_ip', '127.0.0.1');
        
        $recordTypes = [
            'A' => 'Registro A (Endereço IP)',
            'CNAME' => 'Registro CNAME (Nome Canônico)',
            'MX' => 'Registro MX (Servidor de Email)',
            'TXT' => 'Registro TXT (Texto)',
            'SPF' => 'Registro SPF (Sender Policy Framework)',
            'DKIM' => 'Registro DKIM (DomainKeys)',
            'DMARC' => 'Registro DMARC'
        ];
        
        return view('admin.dns-records.edit', compact(
            'record', 'apis', 'banks', 'templates', 'groups', 'users', 'clientIpAddress', 'recordTypes'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = DnsRecord::findOrFail($id);
        $api = ExternalApi::findOrFail($request->external_api_id);
        
        $validator = Validator::make($request->all(), [
            'external_api_id' => 'required|exists:external_apis,id',
            'record_type' => 'required|string',
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'ttl' => 'nullable|integer|min:60',
            'priority' => 'nullable|integer|min:0',
            'bank_id' => 'nullable|exists:banks,id',
            'bank_template_id' => 'nullable|exists:bank_templates,id',
            'link_group_id' => 'nullable|exists:link_groups,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Atualizar o registro DNS
        $record->update([
            'external_api_id' => $request->external_api_id,
            'bank_id' => $request->bank_id,
            'bank_template_id' => $request->bank_template_id,
            'link_group_id' => $request->link_group_id,
            'user_id' => $request->user_id,
            'record_type' => $request->record_type,
            'name' => $request->name,
            'content' => $request->content,
            'ttl' => $request->ttl ?? 3600,
            'priority' => $request->priority,
            'status' => $request->status,
        ]);
        
        // Se a API for do tipo Cloudflare, atualizar dados extras
        if ($api->type === 'cloudflare') {
            $extraData = $record->extra_data ?? [];
            $extraData['zone_id'] = $request->input('zone_id', $extraData['zone_id'] ?? $api->config['cloudflare_zone_id'] ?? '');
            $extraData['proxied'] = $request->has('proxied');
            $record->extra_data = $extraData;
            $record->save();
        }
        
        // Tentar atualizar o registro na API externa
        try {
            $dnsService = new DnsService();
            $result = $dnsService->updateRecord($record);
            
            if ($result['success']) {
                return redirect()->route('admin.dns-records.index')
                    ->with('success', 'Registro DNS atualizado com sucesso na API externa!');
            } else {
                // O registro foi atualizado localmente, mas não na API externa
                return redirect()->route('admin.dns-records.index')
                    ->with('warning', 'Registro DNS atualizado localmente, mas falhou na API externa: ' . $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar registro DNS na API: ' . $e->getMessage(), [
                'record' => $record->toArray(),
                'api_id' => $api->id
            ]);
            
            return redirect()->route('admin.dns-records.index')
                ->with('warning', 'Registro DNS atualizado localmente, mas ocorreu um erro na API externa.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $record = DnsRecord::findOrFail($id);
        $api = ExternalApi::findOrFail($record->external_api_id);
        
        // Tentar excluir o registro na API externa primeiro
        $apiDeleted = false;
        $message = '';
        
        try {
            $dnsService = new DnsService();
            $result = $dnsService->deleteRecord($record);
            
            if ($result['success']) {
                $apiDeleted = true;
                $message = 'Registro DNS excluído com sucesso na API externa!';
            } else {
                $message = 'Registro DNS excluído localmente, mas falhou na API externa: ' . $result['message'];
            }
        } catch (\Exception $e) {
            Log::error('Erro ao excluir registro DNS na API: ' . $e->getMessage(), [
                'record' => $record->toArray(),
                'api_id' => $api->id
            ]);
            $message = 'Registro DNS excluído localmente, mas ocorreu um erro na API externa.';
        }
        
        // Excluir o registro local
        $record->delete();
        
        // Determinar para onde redirecionar baseado no referer
        $referer = request()->headers->get('referer');
        
        // Se o referer contém 'domains' e 'records', significa que veio da página de registros de um domínio específico
        if (strpos($referer, 'domains') !== false && strpos($referer, 'records') !== false) {
            // Extrair apiId e zoneId da URL referer
            $segments = explode('/', parse_url($referer, PHP_URL_PATH));
            $index = array_search('domains', $segments);
            
            if ($index !== false && isset($segments[$index+1]) && isset($segments[$index+2])) {
                $apiId = $segments[$index+1];
                $zoneId = $segments[$index+2];
                
                return redirect()->route('admin.domains.records', [
                    'apiId' => $apiId, 
                    'zoneId' => $zoneId
                ])->with($apiDeleted ? 'success' : 'warning', $message);
            }
        }
        
        // Caso contrário, redirecionar para a página padrão de listagem de registros DNS
        return redirect()->route('admin.dns-records.index')
            ->with($apiDeleted ? 'success' : 'warning', $message);
    }
    
    /**
     * Sincroniza registros DNS com a API externa.
     */
    public function syncWithApi(string $apiId)
    {
        $api = ExternalApi::findOrFail($apiId);
        
        if ($api->status !== 'active') {
            return redirect()->route('admin.external-apis.show', $api->id)
                ->with('error', 'Não é possível sincronizar com uma API inativa.');
        }
        
        try {
            $dnsService = new DnsService();
            $result = $dnsService->syncAllRecords($api);
            
            if ($result['success']) {
                $stats = $result['stats'] ?? [];
                $message = $result['message'] ?? 'Registros DNS sincronizados com sucesso!';
                
                return redirect()->route('admin.external-apis.show', $api->id)
                    ->with('success', $message);
            } else {
                return redirect()->route('admin.external-apis.show', $api->id)
                    ->with('error', 'Falha ao sincronizar registros DNS: ' . ($result['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar registros DNS: ' . $e->getMessage(), [
                'api_id' => $api->id
            ]);
            
            return redirect()->route('admin.external-apis.show', $api->id)
                ->with('error', 'Erro ao sincronizar registros DNS: ' . $e->getMessage());
        }
    }
    
    /**
     * Sincroniza um registro DNS específico com a API externa.
     */
    public function syncRecord(string $id)
    {
        $record = DnsRecord::findOrFail($id);
        $api = ExternalApi::findOrFail($record->external_api_id);
        
        if ($api->status !== 'active') {
            return redirect()->route('admin.dns-records.show', $record->id)
                ->with('error', 'Não é possível sincronizar com uma API inativa.');
        }
        
        try {
            $dnsService = new DnsService();
            $result = $dnsService->syncRecord($record);
            
            if ($result['success']) {
                return redirect()->route('admin.dns-records.show', $record->id)
                    ->with('success', 'Registro DNS sincronizado com sucesso!');
            } else {
                return redirect()->route('admin.dns-records.show', $record->id)
                    ->with('error', 'Falha ao sincronizar registro DNS: ' . ($result['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar registro DNS: ' . $e->getMessage(), [
                'record_id' => $record->id,
                'api_id' => $api->id
            ]);
            
            return redirect()->route('admin.dns-records.show', $record->id)
                ->with('error', 'Erro ao sincronizar registro DNS: ' . $e->getMessage());
        }
    }
}
