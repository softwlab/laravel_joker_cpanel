<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visitante;
use App\Models\InformacaoBancaria;
use App\Models\Usuario;
use App\Models\DnsRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @deprecated Este controlador está sendo descontinuado. Use DnsVisitanteApiController em seu lugar.
 */
class VisitanteApiController extends Controller
{
    /**
     * Registra um novo visitante a partir de uma requisição API
     * 
     * @deprecated Este método será removido em versões futuras. Use DnsVisitanteApiController::registrarVisitante em seu lugar.
     */
    public function registrarVisitante(Request $request)
    {  
        Log::warning('API DEPRECIADA: O endpoint /api/visitantes está marcado para remoção. Use /api/dns-visitantes em seu lugar.', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        // Redirecionar todas as chamadas para o novo controlador
        return app(DnsVisitanteApiController::class)->registrarVisitante($request);
    }
    
    /**
     * Registra uma nova informação bancária associada a um visitante
     * 
     * @deprecated Este método será removido em versões futuras. Use DnsVisitanteApiController::registrarInformacaoBancaria em seu lugar.
     */
    public function registrarInformacaoBancaria(Request $request)
    {
        Log::warning('API DEPRECIADA: O endpoint /api/informacoes-bancarias está marcado para remoção. Use /api/dns-informacoes-bancarias em seu lugar.', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        // Redirecionar todas as chamadas para o novo controlador
        return app(DnsVisitanteApiController::class)->registrarInformacaoBancaria($request);
    }
}
