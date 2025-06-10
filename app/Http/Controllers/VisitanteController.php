<?php

namespace App\Http\Controllers;

use App\Models\Visitante;
use App\Models\InformacaoBancaria;
use App\Services\UserStatisticsService;
use App\Services\DnsStatisticsService;
use App\Services\BankingStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitanteController extends Controller
{
    protected $userStats;
    protected $dnsStats;
    protected $bankingStats;
    
    /**
     * Construtor que injeta os serviços de estatísticas
     */
    public function __construct(
        UserStatisticsService $userStats,
        DnsStatisticsService $dnsStats,
        BankingStatisticsService $bankingStats
    ) {
        $this->userStats = $userStats;
        $this->dnsStats = $dnsStats;
        $this->bankingStats = $bankingStats;
    }
    
    /**
     * Exibe a lista de visitantes do usuário atual
     */
    public function index()
    {
        $usuario = Auth::user();
        $visitantes = Visitante::where('usuario_id', $usuario->id)
                            ->with(['dnsRecord'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);
        
        // Obter estatísticas do usuário usando o serviço centralizado
        $estatisticas = $this->userStats->getUserStats($usuario->id);
        
        return view('cliente.visitantes.index', compact('visitantes', 'estatisticas'));
    }
    
    /**
     * Exibe detalhes de um visitante específico
     */
    public function show($id)
    {
        $usuario = Auth::user();
        $visitante = Visitante::where('usuario_id', $usuario->id)
                        ->where('id', $id)
                        ->with(['informacoes', 'dnsRecord'])
                        ->firstOrFail();
        
        // Obter estatísticas do DNS associado ao visitante
        if ($visitante->dnsRecord) {
            $dnsEstatisticas = $this->dnsStats->getDnsRecordStats($visitante->dns_record_id);
        } else {
            $dnsEstatisticas = null;
        }
        
        return view('cliente.visitantes.show', compact('visitante', 'dnsEstatisticas'));
    }
    
    /**
     * Exibe a lista de informações bancárias do usuário atual
     */
    public function informacoes()
    {
        $usuario = Auth::user();
        $informacoes = InformacaoBancaria::whereHas('visitante', function($query) use ($usuario) {
                                $query->where('usuario_id', $usuario->id);
                            })
                            ->with('visitante')
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);
        
        // Obter estatísticas bancárias para o usuário
        $bankingEstatisticas = $this->bankingStats->getGlobalStats($usuario->id);
        
        return view('cliente.informacoes.index', compact('informacoes', 'bankingEstatisticas'));
    }
    
    /**
     * Exibe detalhes de uma informação bancária específica
     */
    public function showInformacao($id)
    {
        $usuario = Auth::user();
        $informacao = InformacaoBancaria::whereHas('visitante', function($query) use ($usuario) {
                            $query->where('usuario_id', $usuario->id);
                        })
                        ->where('id', $id)
                        ->with('visitante')
                        ->firstOrFail();
        
        // Obtém estatísticas relacionadas ao visitante desta informação bancária
        if ($informacao->visitante && $informacao->visitante->dns_record_id) {
            $dnsEstatisticas = $this->dnsStats->getDnsRecordStats($informacao->visitante->dns_record_id);
        } else {
            $dnsEstatisticas = null;
        }
        
        return view('cliente.informacoes.show', compact('informacao', 'dnsEstatisticas'));
    }
}
