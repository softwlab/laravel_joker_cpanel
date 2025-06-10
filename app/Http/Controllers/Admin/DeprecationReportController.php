<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DeprecationMonitoringService;
use Carbon\Carbon;

class DeprecationReportController extends Controller
{
    /**
     * Serviço de monitoramento de depreciação
     *
     * @var DeprecationMonitoringService
     */
    protected $monitoringService;

    /**
     * Construtor
     *
     * @param DeprecationMonitoringService $monitoringService
     */
    public function __construct(DeprecationMonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Exibe o relatório de uso de APIs depreciadas
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Período padrão é 30 dias, mas pode ser alterado pelo usuário
        $dias = $request->input('dias', 30);
        $dias = min(max((int)$dias, 1), 365); // Limitar entre 1 e 365 dias
        
        // Buscar dados do relatório
        $report = $this->monitoringService->getDeprecatedApiUsageReport($dias);
        
        // Formatar os dados para o gráfico (último mês)
        $chartDates = [];
        $chartValues = [];
        
        $startDate = Carbon::now()->subDays($dias);
        $endDate = Carbon::now();
        
        // Gerar array com todas as datas no período
        $dateRange = [];
        for($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $dateRange[$dateKey] = 0;
            $chartDates[] = $date->format('d/m');
        }
        
        // Preencher com dados reais onde existirem
        if (!empty($report['daily_trend'])) {
            foreach ($report['daily_trend'] as $item) {
                $dateRange[$item->date] = $item->total;
            }
        }
        
        $chartValues = array_values($dateRange);
        
        return view('admin.reports.deprecated-api', [
            'report' => $report,
            'dias' => $dias,
            'chartDates' => json_encode($chartDates),
            'chartValues' => json_encode($chartValues),
        ]);
    }
}
