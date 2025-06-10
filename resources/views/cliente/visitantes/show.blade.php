@extends('layouts.app')

@section('title', 'Detalhes do Visitante')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cliente.visitantes.index') }}">Visitantes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes do Visitante #{{ $visitante->id }}</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informações do Visitante</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>ID:</strong> {{ $visitante->id }}
                </div>
                <div class="col-md-4">
                    <strong>UUID:</strong> {{ $visitante->uuid }}
                </div>
                <div class="col-md-4">
                    <strong>Data:</strong> {{ $visitante->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>IP:</strong> {{ $visitante->ip }}
                </div>
                <div class="col-md-6">
                    <strong>Referrer:</strong> {{ $visitante->referrer ?: 'N/A' }}
                </div>
            </div>
            
            @if($visitante->dnsRecord)
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>DNS Record:</strong> 
                    <span>{{ $visitante->dnsRecord->name }}</span>
                </div>
            </div>
            @endif
            
            <div class="row">
                <div class="col-md-12">
                    <strong>User Agent:</strong>
                    <div class="text-muted">{{ $visitante->user_agent }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Informações Bancárias</h5>
            <span class="badge bg-primary">{{ $visitante->informacoes->count() }}</span>
        </div>
        <div class="card-body">
            @if($visitante->informacoes->isEmpty())
                <div class="alert alert-info">
                    Nenhuma informação bancária registrada para este visitante.
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
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visitante->informacoes as $info)
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
            @endif
        </div>
    </div>
</div>
@endsection
