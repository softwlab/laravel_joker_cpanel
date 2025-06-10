<?php

namespace App\Services;

use App\Models\DnsRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Serviço para estatísticas relacionadas a registros DNS
 * 
 * Centraliza todas as consultas e estatísticas relacionadas aos
 * registros DNS, considerando a nova arquitetura do sistema.
 */
class DnsStatisticsService extends StatisticsService
{
    /**
     * Obtém estatísticas para um registro DNS específico
     *
     * @param DnsRecord|int $dnsRecord Registro DNS ou ID
     * @return array
     */
    public function getDnsRecordStats($dnsRecord): array
    {
        $dnsId = $dnsRecord instanceof DnsRecord ? $dnsRecord->id : $dnsRecord;
        
        return [
            'totalVisitantes' => $this->getTotalVisitantes($dnsId),
            'totalInfoBancarias' => $this->getTotalInformacoesBancarias($dnsId),
            'visitantesPorDia' => $this->getVisitantesPorDia($dnsId),
            'visitantesPorOrigem' => $this->getVisitantesPorOrigem($dnsId),
        ];
    }
    
    /**
     * Obtém o número total de visitantes para um registro DNS específico
     *
     * @param int $dnsId ID do registro DNS
     * @return int
     */
    public function getTotalVisitantes(int $dnsId): int
    {
        return DB::table('visitantes')
            ->where('dns_record_id', $dnsId)
            ->count();
    }
    
    /**
     * Obtém o número total de informações bancárias vinculadas a um registro DNS
     *
     * @param int $dnsId ID do registro DNS
     * @return int
     */
    public function getTotalInformacoesBancarias(int $dnsId): int
    {
        return DB::table('informacoes_bancarias')
            ->join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
            ->where('visitantes.dns_record_id', $dnsId)
            ->count();
    }
    
    /**
     * Obtém estatísticas de visitantes por dia para um registro DNS específico
     *
     * @param int $dnsId ID do registro DNS
     * @param int $dias Número de dias a incluir
     * @return array
     */
    public function getVisitantesPorDia(int $dnsId, int $dias = 30): array
    {
        return DB::table('visitantes')
            ->select(DB::raw('strftime("%Y-%m-%d", visitantes.created_at) as data, COUNT(*) as total'))
            ->where('dns_record_id', $dnsId)
            ->where('visitantes.created_at', '>=', now()->subDays($dias))
            ->groupBy('data')
            ->orderBy('data')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->data => $item->total];
            })
            ->toArray();
    }
    
    /**
     * Obtém estatísticas de visitantes por referrer/origem
     *
     * @param int $dnsId ID do registro DNS
     * @param int $limit Limite de resultados
     * @return array
     */
    public function getVisitantesPorOrigem(int $dnsId, int $limit = 10): array
    {
        return DB::table('visitantes')
            ->select('referrer', DB::raw('COUNT(*) as total'))
            ->where('dns_record_id', $dnsId)
            ->whereNotNull('referrer')
            ->groupBy('referrer')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->referrer => $item->total];
            })
            ->toArray();
    }
    
    /**
     * Obtém o crescimento percentual de visitantes comparado ao período anterior
     *
     * @param int $dnsId ID do registro DNS
     * @param int $dias Período de dias para análise
     * @return array
     */
    public function getCrescimentoVisitantes(int $dnsId, int $dias = 7): array
    {
        $periodoAtual = now()->subDays($dias);
        $periodoAnterior = now()->subDays($dias * 2);
        
        $visitantesAtual = DB::table('visitantes')
            ->where('dns_record_id', $dnsId)
            ->where('visitantes.created_at', '>=', $periodoAtual)
            ->count();
            
        $visitantesAnterior = DB::table('visitantes')
            ->where('dns_record_id', $dnsId)
            ->whereBetween('visitantes.created_at', [$periodoAnterior, $periodoAtual])
            ->count();
            
        $percentual = $visitantesAnterior > 0 
            ? round((($visitantesAtual - $visitantesAnterior) / $visitantesAnterior) * 100, 2)
            : ($visitantesAtual > 0 ? 100 : 0);
            
        return [
            'atual' => $visitantesAtual,
            'anterior' => $visitantesAnterior,
            'percentual' => $percentual,
            'crescimento' => $percentual >= 0
        ];
    }
    
    /**
     * Invalida o cache de estatísticas para um registro DNS específico
     *
     * @param int $dnsId ID do registro DNS
     * @return void
     */
    public function invalidateDnsCache(int $dnsId): void
    {
        // Limpa os caches relacionados ao DNS específico
        $patterns = [
            "dns:{$dnsId}",
            "dns:{$dnsId}:*",
        ];
        
        foreach ($patterns as $pattern) {
            Cache::forget($this->getCacheKey($pattern));
        }
    }
}
