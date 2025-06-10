<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Services\DeprecationMonitoringService;

class DeprecatedApiMiddleware
{
    /**
     * O serviço de monitoramento de depreciação
     * 
     * @var \App\Services\DeprecationMonitoringService
     */
    protected $monitoringService;
    
    /**
     * Construtor
     */
    public function __construct(DeprecationMonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Adicionar header para indicar API depreciada
        $response = $next($request);
        
        // Adicionar headers para indicar depreciação
        $response->headers->set('X-API-Deprecated', 'true');
        $response->headers->set('X-API-Deprecated-Message', 'Esta rota está depreciada e será removida em breve. Use a API de DNS em seu lugar.');
        
        // Definir qual endpoint é o recomendado com base no path atual
        $path = $request->path();
        $recommendedEndpoint = str_replace('api/', 'api/dns-', $path);
        
        // Usar o serviço de monitoramento para registrar uso
        $this->monitoringService->logDeprecatedEndpointUsage(
            $request,
            $path,
            $recommendedEndpoint
        );
        
        // Se for resposta JSON, adicionar informação ao corpo da resposta
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            
            // Adicionar aviso na resposta
            if (is_array($data)) {
                $data['deprecated'] = true;
                $data['deprecation_message'] = 'AVISO: Esta API está depreciada e será removida em versões futuras. Por favor, migre para a nova API de DNS.';
                $data['migration_help'] = 'Consulte a documentação em /docs para informações sobre a nova API.';
                $response->setData($data);
            }
        }
        
        return $response;
    }
}
