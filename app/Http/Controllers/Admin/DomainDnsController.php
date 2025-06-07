<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DnsRecord;
use App\Models\ExternalApi;
use App\Models\CloudflareDomain;
use App\Services\DnsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DomainDnsController extends Controller
{
    /**
     * Exibe os registros DNS de um domínio específico
     * 
     * @param string $apiId
     * @param string $zoneId
     * @return \Illuminate\View\View
     */
    public function show($apiId, $zoneId)
    {
        $api = ExternalApi::findOrFail($apiId);
        $dnsService = app('App\Services\DnsService');
        
        // Buscar informações do domínio
        $domainInfo = CloudflareDomain::where('external_api_id', $apiId)
            ->where('zone_id', $zoneId)
            ->first();
            
        if (!$domainInfo) {
            // Se não encontrar no banco, busca na API
            $result = $dnsService->getDomainsForApi($api);
            
            if (isset($result['success']) && $result['success'] && isset($result['domains'])) {
                foreach ($result['domains'] as $domain) {
                    if ($domain['id'] == $zoneId) {
                        $domainInfo = new CloudflareDomain([
                            'external_api_id' => $apiId,
                            'zone_id' => $zoneId,
                            'name' => $domain['name'],
                            'status' => $domain['status'],
                            'is_ghost' => false
                        ]);
                        break;
                    }
                }
            }
        }
        
        if (!$domainInfo) {
            return redirect()->route('admin.external-apis.domains', $apiId)
                ->with('error', 'Domínio não encontrado.');
        }
        
        // Buscar os registros DNS deste domínio
        $dnsRecords = DnsRecord::where('external_api_id', $apiId)
            ->where(function($query) use ($zoneId, $domainInfo) {
                $query->whereJsonContains('extra_data->cloudflare_zone_id', $zoneId)
                      ->orWhere('name', 'like', '%' . $domainInfo->name);
            })
            ->paginate(15);
        
        // Tipos de registro para o formulário de criação
        $recordTypes = [
            'A' => 'Registro A (Endereço IP)',
            'CNAME' => 'Registro CNAME (Nome Canônico)',
            'MX' => 'Registro MX (Servidor de Email)',
            'TXT' => 'Registro TXT (Texto)',
            'SPF' => 'Registro SPF (Sender Policy Framework)',
            'DKIM' => 'Registro DKIM (DomainKeys)',
            'DMARC' => 'Registro DMARC'
        ];
        
        return view('admin.domains.records', compact('api', 'domainInfo', 'dnsRecords', 'recordTypes'));
    }
    
    /**
     * Sincroniza os registros DNS de um domínio específico
     * 
     * @param string $apiId
     * @param string $zoneId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sync($apiId, $zoneId)
    {
        $api = ExternalApi::findOrFail($apiId);
        $dnsService = app('App\Services\DnsService');
        
        try {
            $service = $dnsService->getApiService($api);
            
            if ($api->type === 'cloudflare') {
                $result = $service->syncRecordsFromCloudflare($zoneId);
                
                if ($result['success']) {
                    return redirect()->route('admin.domains.records', ['apiId' => $apiId, 'zoneId' => $zoneId])
                        ->with('success', 'Registros DNS sincronizados com sucesso: ' . ($result['records_synced'] ?? 0) . ' registros.');
                } else {
                    return redirect()->route('admin.domains.records', ['apiId' => $apiId, 'zoneId' => $zoneId])
                        ->with('error', 'Falha ao sincronizar registros DNS: ' . ($result['message'] ?? 'Erro desconhecido'));
                }
            } else {
                return redirect()->route('admin.domains.records', ['apiId' => $apiId, 'zoneId' => $zoneId])
                    ->with('error', 'Sincronização não implementada para o tipo de API: ' . $api->type);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar registros DNS do domínio', [
                'api_id' => $apiId,
                'zone_id' => $zoneId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.domains.records', ['apiId' => $apiId, 'zoneId' => $zoneId])
                ->with('error', 'Erro ao sincronizar registros DNS: ' . $e->getMessage());
        }
    }
}
