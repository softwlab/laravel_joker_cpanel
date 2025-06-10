<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Serviço para estatísticas relacionadas a usuários
 * 
 * Centraliza todas as consultas e estatísticas relacionadas aos usuários,
 * incluindo relacionamentos com DNS records e informações bancárias.
 */
class UserStatisticsService extends StatisticsService
{
    /**
     * Obtém estatísticas relacionadas a um usuário específico
     *
     * @param User|int $user Usuário ou ID do usuário
     * @return array
     */
    public function getUserStats($user): array
    {
        $userId = $user instanceof User ? $user->id : $user;
        
        return [
            'totalDnsRecords' => $this->getTotalDnsRecords($userId),
            'totalVisitantes' => $this->getTotalVisitantes($userId),
            'totalInfoBancarias' => $this->getTotalInformacoesBancarias($userId),
            'visitantesPorMes' => $this->getVisitantesPorMes($userId),
            'trafegoPorDomain' => $this->getTrafegoPorDomain($userId),
        ];
    }
    
    /**
     * Obtém o número total de registros DNS associados a um usuário
     *
     * @param int $userId ID do usuário
     * @return int
     */
    public function getTotalDnsRecords(int $userId): int
    {
        return DB::table('dns_records')
            ->where('user_id', $userId)
            ->count();
    }
    
    /**
     * Obtém o número total de visitantes associados a um usuário
     * Inclui tanto visitantes do sistema legado quanto do novo (baseado em DNS)
     *
     * @param int $userId ID do usuário
     * @return int
     */
    public function getTotalVisitantes(int $userId): int
    {
        return DB::table('visitantes')
            ->join('dns_records', 'visitantes.dns_record_id', '=', 'dns_records.id')
            ->where('dns_records.user_id', $userId)
            ->count();
    }
    
    /**
     * Obtém o número total de informações bancárias associadas a um usuário
     *
     * @param int $userId ID do usuário
     * @return int
     */
    public function getTotalInformacoesBancarias(int $userId): int
    {
        return DB::table('informacoes_bancarias')
            ->join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
            ->join('dns_records', 'visitantes.dns_record_id', '=', 'dns_records.id')
            ->where('dns_records.user_id', $userId)
            ->count();
    }
    
    /**
     * Obtém as estatísticas de visitantes por mês para um usuário específico
     * 
     * @param int $userId ID do usuário
     * @param int $meses Número de meses a incluir
     * @return array
     */
    public function getVisitantesPorMes(int $userId, int $meses = 6): array
    {
        return DB::table('visitantes')
            ->select(DB::raw('strftime("%Y-%m", visitantes.created_at) as mes, COUNT(*) as total'))
            ->join('dns_records', 'visitantes.dns_record_id', '=', 'dns_records.id')
            ->where('dns_records.user_id', $userId)
            ->where('visitantes.created_at', '>=', now()->subMonths($meses))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->keyBy('mes')
            ->map(function($item) {
                return $item->total;
            })
            ->toArray();
    }
    
    /**
     * Obtém estatísticas de tráfego por domínio para um usuário específico
     * 
     * @param int $userId ID do usuário
     * @return array
     */
    public function getTrafegoPorDomain(int $userId): array
    {
        $result = DB::table('dns_records')
            ->select('dns_records.name', DB::raw('COUNT(visitantes.id) as total_visitantes'))
            ->leftJoin('visitantes', 'dns_records.id', '=', 'visitantes.dns_record_id')
            ->where('dns_records.user_id', $userId)
            ->groupBy('dns_records.name')
            ->orderByDesc('total_visitantes')
            ->get();
            
        return $result->mapWithKeys(function($item) {
            return [$item->name => $item->total_visitantes];
        })->toArray();
    }
    
    /**
     * Invalida todo o cache de estatísticas relacionado a um usuário específico
     *
     * @param int $userId ID do usuário
     * @return void
     */
    public function invalidateUserCache(int $userId): void
    {
        // Limpa os caches relacionados ao usuário específico
        $patterns = [
            "user:{$userId}",
            "user:{$userId}:*",
        ];
        
        foreach ($patterns as $pattern) {
            Cache::forget($this->getCacheKey($pattern));
        }
    }
}
