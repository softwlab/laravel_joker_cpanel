<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeprecationMonitoringService
{
    /**
     * Registra o uso de um endpoint depreciado
     *
     * @param Request $request
     * @param string $endpointName
     * @param string $recommendedEndpoint
     * @return void
     */
    public function logDeprecatedEndpointUsage(Request $request, string $endpointName, string $recommendedEndpoint)
    {
        // Registrar no log do sistema
        Log::channel('deprecated_api')->warning('Endpoint depreciado acessado', [
            'endpoint' => $endpointName,
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'api_key' => substr($request->header('X-API-KEY', 'none'), 0, 8) . '...',  // Apenas os primeiros caracteres por segurança
            'recommended' => $recommendedEndpoint,
            'timestamp' => Carbon::now()->toDateTimeString()
        ]);

        // Registrar estatísticas no banco para análise posterior
        try {
            DB::table('deprecated_api_usage')->insert([
                'endpoint' => $endpointName,
                'method' => $request->method(),
                'ip_hash' => hash('sha256', $request->ip()), // Armazenar hash por privacidade
                'user_agent_hash' => hash('sha256', $request->userAgent() ?? 'unknown'),
                'api_key_hash' => hash('sha256', $request->header('X-API-KEY', 'none')),
                'created_at' => Carbon::now()
            ]);
        } catch (\Exception $e) {
            // Se a tabela ainda não existir, apenas continue
            Log::error('Erro ao registrar estatística de uso de API depreciada: ' . $e->getMessage());
        }
    }

    /**
     * Gera relatório de uso de APIs depreciadas
     *
     * @param int $dias Número de dias para incluir no relatório
     * @return array
     */
    public function getDeprecatedApiUsageReport(int $dias = 30)
    {
        try {
            // Total de chamadas por endpoint
            $usageByEndpoint = DB::table('deprecated_api_usage')
                ->where('created_at', '>=', Carbon::now()->subDays($dias))
                ->select('endpoint', DB::raw('count(*) as total'))
                ->groupBy('endpoint')
                ->get();

            // Total de clientes únicos (baseado em hashes de API keys)
            $uniqueClients = DB::table('deprecated_api_usage')
                ->where('created_at', '>=', Carbon::now()->subDays($dias))
                ->select('api_key_hash')
                ->distinct()
                ->count();

            // Tendência de uso (últimos 30 dias)
            $dailyTrend = DB::table('deprecated_api_usage')
                ->where('created_at', '>=', Carbon::now()->subDays($dias))
                ->select(
                    DB::raw('DATE(created_at) as date'), 
                    DB::raw('count(*) as total')
                )
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();

            return [
                'period' => "{$dias} dias",
                'total_calls' => $usageByEndpoint->sum('total'),
                'unique_clients' => $uniqueClients,
                'calls_by_endpoint' => $usageByEndpoint,
                'daily_trend' => $dailyTrend
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório de uso de API depreciada: ' . $e->getMessage());
            return [
                'error' => 'Não foi possível gerar o relatório. Verifique se a tabela deprecated_api_usage existe.'
            ];
        }
    }
}
