@extends('layouts.app')

@section('title', 'Informações Bancárias')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Informações Bancárias</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Painel de estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Total de Registros</h5>
                    <h2 class="mb-0">{{ number_format($bankingEstatisticas['totalRegistros'] ?? 0, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Por CPF</h5>
                    <h2 class="mb-0">{{ number_format($bankingEstatisticas['totalPorIdentificador']['cpf'] ?? 0, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Por Email</h5>
                    <h2 class="mb-0">{{ number_format($bankingEstatisticas['totalPorIdentificador']['email'] ?? 0, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h5 class="card-title">Taxa de Conversão</h5>
                    <h2 class="mb-0">{{ number_format(is_array($bankingEstatisticas['taxaDeConversao'] ?? 0) ? ($bankingEstatisticas['taxaDeConversao']['taxa'] ?? 0) : ($bankingEstatisticas['taxaDeConversao'] ?? 0), 1, ',', '.') }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de registros diários -->
    @if(isset($bankingEstatisticas['registrosPorDia']) && !empty($bankingEstatisticas['registrosPorDia']))
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Registros por Dia</h5>
        </div>
        <div class="card-body">
            <canvas id="registrosChart" height="100"></canvas>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Informações Bancárias</h5>
            </div>
        </div>
        <div class="card-body">
            @if($informacoes->isEmpty())
                <div class="alert alert-info">
                    Nenhuma informação bancária registrada.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>CPF</th>
                                <th>Nome</th>
                                <th>Agência/Conta</th>
                                <th>Telefone</th>
                                <th>Visitante</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($informacoes as $info)
                            <tr>
                                <td>{{ $info->id }}</td>
                                <td>{{ $info->data ? \Carbon\Carbon::parse($info->data)->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ $info->cpf ?: 'N/A' }}</td>
                                <td>{{ $info->nome_completo ?: 'N/A' }}</td>
                                <td>
                                    @if($info->agencia || $info->conta)
                                        {{ $info->agencia }} / {{ $info->conta }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $info->telefone ?: 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('cliente.visitantes.show', $info->visitante->id) }}">
                                        #{{ $info->visitante->id }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('cliente.informacoes.show', $info->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Detalhes
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $informacoes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(isset($bankingEstatisticas['registrosPorDia']) && !empty($bankingEstatisticas['registrosPorDia']))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('registrosChart');
        const context = ctx ? ctx.getContext('2d') : null;
        
        // Extrair dados de registros por dia
        const registrosPorDia = {!! json_encode(isset($bankingEstatisticas['registrosPorDia']) ? $bankingEstatisticas['registrosPorDia'] : []) !!};
        const labels = Object.keys(registrosPorDia);
        const values = Object.values(registrosPorDia);
        
        if (ctx && context) {
            new Chart(context, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Registros Bancários por Dia',
                    data: values,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Registros Bancários Diários'
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
        }
    });
</script>
@endif
@endsection
