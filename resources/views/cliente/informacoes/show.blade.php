@extends('layouts.app')

@section('title', 'Detalhes da Informação Bancária')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cliente.informacoes.index') }}">Informações Bancárias</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes da Informação #{{ $informacao->id }}</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Informação Bancária</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>ID:</strong> {{ $informacao->id }}
                </div>
                <div class="col-md-4">
                    <strong>Data da Informação:</strong> {{ $informacao->data ? \Carbon\Carbon::parse($informacao->data)->format('d/m/Y') : 'N/A' }}
                </div>
                <div class="col-md-4">
                    <strong>Data de Registro:</strong> {{ $informacao->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>CPF:</strong> {{ $informacao->cpf ?: 'N/A' }}
                </div>
                <div class="col-md-6">
                    <strong>Nome Completo:</strong> {{ $informacao->nome_completo ?: 'N/A' }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Agência:</strong> {{ $informacao->agencia ?: 'N/A' }}
                </div>
                <div class="col-md-4">
                    <strong>Conta:</strong> {{ $informacao->conta ?: 'N/A' }}
                </div>
                <div class="col-md-4">
                    <strong>Telefone:</strong> {{ $informacao->telefone ?: 'N/A' }}
                </div>
            </div>
            
            @if($informacao->informacoes_adicionais)
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Informações Adicionais:</strong>
                    <div class="card">
                        <div class="card-body bg-light">
                            <pre class="mb-0">{{ json_encode($informacao->informacoes_adicionais, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Origem da Informação</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Visitante ID:</strong>
                    <a href="{{ route('cliente.visitantes.show', $informacao->visitante->id) }}">
                        #{{ $informacao->visitante->id }}
                    </a>
                </div>
                <div class="col-md-4">
                    <strong>UUID:</strong> {{ $informacao->visitante->uuid }}
                </div>
                <div class="col-md-4">
                    <strong>Data da Visita:</strong> {{ $informacao->visitante->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>IP do Visitante:</strong> {{ $informacao->visitante->ip }}
                </div>
                <div class="col-md-6">
                    <strong>Referenciador:</strong> {{ $informacao->visitante->referrer ?: 'N/A' }}
                </div>
            </div>
            
            @if($informacao->visitante->linkItem)
            <div class="row">
                <div class="col-md-12">
                    <strong>Link de Origem:</strong>
                    <a href="{{ $informacao->visitante->linkItem->url }}" target="_blank">
                        {{ $informacao->visitante->linkItem->title }} ({{ $informacao->visitante->linkItem->url }})
                    </a>
                    <div class="mt-2">
                        <a href="{{ route('cliente.linkgroups.show', $informacao->visitante->linkItem->group_id) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-link"></i> Ver Grupo de Links
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
