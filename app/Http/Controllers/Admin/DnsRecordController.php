<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DnsRecord;
use App\Models\ExternalApi;
use App\Models\Bank;
use App\Models\BankTemplate;
use Illuminate\Support\Facades\Config;
use App\Services\BankingStatisticsService;
use App\Services\DnsRecordService;
use App\Services\DnsService;
use App\Services\DnsStatisticsService;
use App\Services\UserStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DnsRecordController extends Controller
{
    protected $dnsRecordService;
    
    /**
     * Construtor que injeta o serviço de DNS Records
     * 
     * @param DnsRecordService $dnsRecordService
     */
    public function __construct(DnsRecordService $dnsRecordService)
    {
        $this->dnsRecordService = $dnsRecordService;
    }
    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filters = [
            'type' => $request->input('type'),
            'api' => $request->input('api'),
            'search' => $request->input('search'),
        ];
        
        $records = $this->dnsRecordService->getPaginatedRecords($filters);
        return view('admin.dns-records.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $formData = $this->dnsRecordService->getCreateFormData();
        return view('admin.dns-records.create', $formData);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $result = $this->dnsRecordService->createRecord($request->all());
        
        if (!$result['success']) {
            return redirect()->back()
                ->withErrors($result['errors'] ?? ['message' => $result['message']])
                ->withInput();
        }
        
        $dnsRecord = $result['record'];
        $redirectResponse = redirect()->route('admin.dns-records.show', $dnsRecord->id)
            ->with('success', 'Registro DNS criado com sucesso!');
        
        if (isset($result['warning'])) {
            $redirectResponse->with('warning', $result['warning']);
        }
        
        return $redirectResponse;
    }

    /**
     * Display the specified resource.
     * 
     * @param string $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $record = $this->dnsRecordService->getRecord($id);

        if (!$record) {
            return redirect()->route('admin.dns-records.index')
                ->with('error', 'Registro DNS não encontrado.');
        }

        // Obter estatísticas de visitantes usando os métodos do serviço
        $totalVisitantes = $this->dnsRecordService->getTotalVisitantes($id);
        $visitantesPorDia = $this->dnsRecordService->getVisitantesPorDia($id);
        $infoBancarias = $this->dnsRecordService->getInformacoesBancarias($id);

        return view('admin.dns-records.show', compact('record', 'totalVisitantes', 'visitantesPorDia', 'infoBancarias'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param string $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(string $id)
    {
        // Usar o serviço para obter os dados necessários para edição
        $formData = $this->dnsRecordService->getEditData($id);
        
        if (!$formData['record']) {
            return redirect()->route('admin.dns-records.index')
                ->with('error', 'Registro DNS não encontrado.');
        }
        
        return view('admin.dns-records.edit', $formData);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        // Log para debug - verificar dados recebidos do formulário
        \Illuminate\Support\Facades\Log::info('DnsRecordController::update - Dados recebidos do formulário:', [
            'id' => $id,
            'todos_dados' => $request->all(),
            'bank_template_id' => $request->input('bank_template_id'),
            'bank_id' => $request->input('bank_id'),
            'user_id' => $request->input('user_id')
        ]);

        // Usar o serviço para atualizar o registro
        $result = $this->dnsRecordService->updateRecord($id, $request->all());
        
        if ($result['success']) {
            return redirect()->route('admin.dns-records.show', $id)
                ->with('success', 'Registro DNS atualizado com sucesso!');
        }
        
        // Se houve erro de validação
        if (isset($result['validation_errors'])) {
            return redirect()->back()
                ->withErrors($result['validation_errors'])
                ->withInput();
        }
        
        // Se foi atualizado no banco mas falhou na API externa
        return redirect()->route('admin.dns-records.show', $id)
            ->with('warning', $result['message'] ?? 'Registro DNS atualizado, mas houve um erro ao sincronizar com a API externa.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Usar o serviço para excluir o registro DNS
        $result = $this->dnsRecordService->deleteRecord($id);
        
        $apiDeleted = $result['apiDeleted'] ?? false;
        $message = $result['message'] ?? 'Registro DNS excluído.'; 
        
        if (!$result['success']) {
            return redirect()->route('admin.dns-records.index')
                ->with('error', $message);
        }
        
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
        // Delegar a sincronização ao serviço de registros DNS
        $result = $this->dnsRecordService->syncAllRecords($apiId);
        
        if ($result['success']) {
            $message = $result['message'] ?? 'Registros DNS sincronizados com sucesso!';
            return redirect()->route('admin.external-apis.show', $apiId)
                ->with('success', $message);
        } else {
            return redirect()->route('admin.external-apis.show', $apiId)
                ->with('error', 'Falha ao sincronizar registros DNS: ' . ($result['message'] ?? 'Erro desconhecido'));
        }
    }
    
    /**
     * Sincroniza um registro DNS específico com a API externa.
     */
    public function syncRecord(string $id)
    {
        // Delegar a sincronização do registro ao serviço
        $result = $this->dnsRecordService->syncRecord($id);
        
        if ($result['success']) {
            return redirect()->route('admin.dns-records.show', $id)
                ->with('success', 'Registro DNS sincronizado com sucesso!');
        } else {
            return redirect()->route('admin.dns-records.show', $id)
                ->with('error', 'Falha ao sincronizar registro DNS: ' . ($result['message'] ?? 'Erro desconhecido'));
        }
    }
}
