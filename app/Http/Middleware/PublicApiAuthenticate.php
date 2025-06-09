<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\PublicApiKey;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se a chave de API está presente no cabeçalho (teste vários formatos)
        $apiKey = $request->header('X-API-KEY') ?: $request->header('X-API-Key') ?: $request->header('x-api-key');
        
        if (!$apiKey) {
            // Tentar obter a chave por parâmetro de consulta como fallback
            $apiKey = $request->get('api_key');
        }
        
        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'API key não fornecida'
            ], 401);
        }
        
        // Buscar e validar a chave de API
        $apiKeyModel = PublicApiKey::where('key', $apiKey)
                                 ->where('active', true)
                                 ->first();
                                 
        if (!$apiKeyModel) {
            return response()->json([
                'status' => 'error',
                'message' => 'API key inválida ou inativa'
            ], 401);
        }
        
        // Marcar chave como utilizada
        $apiKeyModel->markAsUsed();
        
        // Adicionar a chave como atributo da requisição para uso posterior
        $request->attributes->add(['api_key_model' => $apiKeyModel]);
        
        return $next($request);
    }
}
