@extends('layouts.app')

@section('title', 'Estatísticas de Visitantes')

@section('styles')
<style>
    .stat-card {
        transition: all 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        height: 400px;
    }
    .top-links-item {
        border-left: 3px solid #3490dc;
        padding-left: 10px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Estatísticas de Visitantes</h1>
        
        <div class="d-flex">
            <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#filtroModal">
                <i class="fas fa-filter"></i> Filtrar por Período
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download"></i> Exportar
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="#" id="exportarPDF"><i class="fas fa-file-pdf"></i> Exportar como PDF</a></li>
                    <li><a class="dropdown-item" href="#" id="exportarCSV"><i class="fas fa-file-csv"></i> Exportar como CSV</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Cards de estatísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card bg-primary bg-gradient text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total de Visitantes</h6>
                            <h2 class="mt-2 mb-0">{{ number_format($totalVisitantes) }}</h2>
                        </div>
                        <div class="display-4">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stat-card bg-success bg-gradient text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Total de Informações</h6>
                            <h2 class="mt-2 mb-0">{{ number_format($totalInformacoes) }}</h2>
                        </div>
                        <div class="display-4">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stat-card bg-info bg-gradient text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Taxa de Conversão</h6>
                            <h2 class="mt-2 mb-0">{{ $taxaConversao }}%</h2>
                        </div>
                        <div class="display-4">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <!-- Gráfico de visitantes e informações -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Histórico de Visitantes e Informações</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="visitantesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Links mais acessados -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top 5 Links Mais Acessados</h5>
                </div>
                <div class="card-body">
                    @if(isset($topLinks) && $topLinks->isNotEmpty())
                        <ul class="list-group">
                            @foreach($topLinks as $link)
                                <li class="list-group-item top-links-item">
                                    <div class="d-flex justify-content-between">
                                        <div class="text-truncate" title="{{ $link->title }}">
                                            <strong>{{ $link->title }}</strong>
                                        </div>
                                        <span class="badge rounded-pill bg-primary">{{ $link->total_acessos }}</span>
                                    </div>
                                    <div class="text-muted text-truncate small" title="{{ $link->url }}">
                                        {{ $link->url }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="alert alert-info">
                            Nenhum link acessado no período.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Filtro por Período -->
<div class="modal fade" id="filtroModal" tabindex="-1" aria-labelledby="filtroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="filtroForm" action="{{ route('cliente.estatisticas.filtrar') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="filtroModalLabel">Filtrar por Período</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_inicio" class="form-label">Data Inicial</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_fim" class="form-label">Data Final</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados para o gráfico (recebidos do controller)
    const dadosGraficos = JSON.parse('@json($dadosGraficos)');
    
    // Criar o gráfico de linha
    const ctx = document.getElementById('visitantesChart').getContext('2d');
    const visitantesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dadosGraficos.datas,
            datasets: [
                {
                    label: 'Visitantes',
                    data: dadosGraficos.visitantes,
                    borderColor: 'rgba(52, 144, 220, 1)',
                    backgroundColor: 'rgba(52, 144, 220, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Informações Bancárias',
                    data: dadosGraficos.informacoes,
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // Filtro por AJAX (para não recarregar a página)
    const filtroForm = document.getElementById('filtroForm');
    filtroForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(filtroForm);
        
        fetch(filtroForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Atualizar os valores das estatísticas
            document.querySelector('.col-md-4:nth-child(1) h2').textContent = data.totalVisitantes.toLocaleString();
            document.querySelector('.col-md-4:nth-child(2) h2').textContent = data.totalInformacoes.toLocaleString();
            document.querySelector('.col-md-4:nth-child(3) h2').textContent = data.taxaConversao + '%';
            
            // Atualizar o gráfico
            visitantesChart.data.labels = data.dadosGraficos.datas;
            visitantesChart.data.datasets[0].data = data.dadosGraficos.visitantes;
            visitantesChart.data.datasets[1].data = data.dadosGraficos.informacoes;
            visitantesChart.update();
            
            // Fechar o modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('filtroModal'));
            modal.hide();
        })
        .catch(error => {
            console.error('Erro ao filtrar dados:', error);
            alert('Erro ao filtrar os dados. Por favor, tente novamente.');
        });
    });
});
</script>
@endsection
