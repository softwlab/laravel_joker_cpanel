<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\DashboardService;

class EstatisticaController extends Controller
{
    protected $dashboardService;

    /**
     * Construtor do controller
     *
     * @param DashboardService $dashboardService
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Mostra o painel de estatísticas para o usuário atual
     */
    public function index()
    {
        $usuario = Auth::user();
        
        // Definir período padrão - últimos 30 dias
        $dataAtual = Carbon::now();
        $dataInicio = Carbon::now()->subDays(30);
        
        // Obter estatísticas gerais do dashboard
        $stats = $this->dashboardService->getDashboardStats($usuario->id);
        
        // Obter estatísticas filtradas com visitantes e informações por dia
        $statsDetalhados = $this->dashboardService->getFilteredStats(
            $usuario->id, 
            $dataInicio->format('Y-m-d'), 
            $dataAtual->format('Y-m-d')
        );
        
        // Preparar dados formatados para gráficos
        $datas = [];
        $visitantesData = [];
        $informacoesData = [];
        
        // Processar dados por dia para o gráfico
        foreach ($statsDetalhados['visitantes_por_dia'] as $visita) {
            $data = Carbon::parse($visita->data)->format('d/m');
            $datas[] = $data;
            $visitantesData[] = $visita->total;
        }
        
        foreach ($statsDetalhados['informacoes_por_dia'] as $info) {
            $data = Carbon::parse($info->data)->format('d/m');
            $informacoesData[] = $info->total;
        }
        
        // Dados formatados para os gráficos
        $dadosGraficos = [
            'datas' => $datas, 
            'visitantes' => $visitantesData,
            'informacoes' => $informacoesData
        ];
        
        // Retornar view com os dados
        return view('cliente.estatisticas.index', [
            'totalVisitantes' => $stats['estatisticas_gerais']['total_visitantes'],
            'totalInformacoes' => $stats['estatisticas_gerais']['total_informacoes'],
            'taxaConversao' => $stats['estatisticas_gerais']['taxa_conversao'],
            'topDnsRecords' => $stats['top_dns_records'],
            'dadosGraficos' => $dadosGraficos,
            'dataInicio' => $dataInicio,
            'dataAtual' => $dataAtual
        ]);
    }
    
    /**
     * Retorna estatísticas filtradas por data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function filtrar(Request $request)
    {
        $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio'
        ]);
        
        $dataInicio = $request->data_inicio;
        $dataFim = $request->data_fim;
        $usuario = Auth::user();
        
        // Obter estatísticas filtradas usando o serviço
        $statsDetalhados = $this->dashboardService->getFilteredStats(
            $usuario->id, 
            $dataInicio, 
            $dataFim
        );
        
        // Formatar dados para gráficos
        $datas = [];
        $visitantesData = [];
        $informacoesData = [];
        
        // Processar dados por dia para o gráfico
        foreach ($statsDetalhados['visitantes_por_dia'] as $visita) {
            $data = Carbon::parse($visita->data)->format('d/m');
            $datas[] = $data;
            $visitantesData[] = $visita->total;
        }
        
        foreach ($statsDetalhados['informacoes_por_dia'] as $info) {
            $data = Carbon::parse($info->data)->format('d/m');
            $informacoesData[] = $info->total;
        }
        
        // Dados formatados para os gráficos
        $dadosGraficos = [
            'datas' => $datas, 
            'visitantes' => $visitantesData,
            'informacoes' => $informacoesData
        ];
        
        // Preparar resposta com todos os dados necessários
        $response = [
            'totalVisitantes' => $statsDetalhados['total_visitantes'],
            'totalInformacoes' => $statsDetalhados['total_informacoes'],
            'taxaConversao' => $statsDetalhados['taxa_conversao'],
            'topDnsRecords' => $statsDetalhados['top_dns_records'],
            'dadosGraficos' => $dadosGraficos,
            'periodo' => $statsDetalhados['periodo']
        ];
        
        // Retornar resposta como JSON se solicitado
        if ($request->wantsJson()) {
            return response()->json($response);
        }
        
        // Caso contrário, renderizar a view com os dados
        return view('cliente.estatisticas.index', $response);
    }
}
