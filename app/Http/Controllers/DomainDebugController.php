<?php

namespace App\Http\Controllers;

use App\Services\DomainService;
use Illuminate\Http\Request;

class DomainDebugController extends Controller
{
    protected $domainService;
    
    /**
     * Construtor do controller
     *
     * @param DomainService $domainService
     */
    public function __construct(DomainService $domainService)
    {
        $this->domainService = $domainService;
    }

    /**
     * Exibe informações de diagnóstico para um usuário específico
     *
     * @param int $userId ID do usuário
     * @return \Illuminate\View\View
     */
    public function showDebug($userId)
    {
        // Delega a obtenção dos dados de diagnóstico para o serviço
        $diagnostics = $this->domainService->getUserDomainDiagnostics($userId);
        
        // Extrai as variáveis individuais do array para manter compatibilidade com a view existente
        $user = $diagnostics['user'];
        $pivotData = $diagnostics['pivotData'];
        $allDomains = $diagnostics['allDomains'];
        $userDnsRecords = $diagnostics['userDnsRecords'];
        
        return view('admin.domain-debug', compact('user', 'pivotData', 'allDomains', 'userDnsRecords'));
    }
    
    /**
     * Associa um domínio a um usuário
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function associateDomain(Request $request)
    {
        // Validação
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'cloudflare_domain_id' => 'required|exists:cloudflare_domains,id',
        ]);
        
        $userId = $request->input('usuario_id');
        $domainId = $request->input('cloudflare_domain_id');
        
        // Delega a associação ao serviço
        $result = $this->domainService->associateDomain($userId, $domainId);
        
        // Retorna redirecionamento com mensagem adequada baseada no resultado
        $messageType = $result['success'] ? 'success' : 'info';
        return redirect()->back()->with($messageType, $result['message']);
    }
}
