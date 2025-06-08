@extends('layouts.app')

@section('title', 'Templates Disponíveis')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho com breadcrumb -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Templates para: {{ $domain->name }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('cliente.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list"></i> Templates Disponíveis
                </div>
                <div class="card-body">
                    @if($templates->count() > 0)
                        <div class="row row-cols-1 row-cols-md-3 g-4">
                            @foreach($templates as $template)
                            <div class="col">
                                <div class="card h-100">
                                    @if($template->logo)
                                        <div class="text-center pt-3">
                                            <img src="{{ $template->logo }}" class="card-img-top" alt="{{ $template->name }}" style="max-height: 80px; width: auto;">
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $template->name }}</h5>
                                        <p class="card-text">{{ $template->description ?? 'Sem descrição disponível' }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="btn-group">
                                                @if($domain->dnsRecords->where('bank_template_id', $template->id)->first())
                                                    @php 
                                                        $record = $domain->dnsRecords->where('bank_template_id', $template->id)->first();
                                                    @endphp
                                                    <a href="{{ route('cliente.templates.config', ['template_id' => $template->id, 'record_id' => $record->id]) }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-cog"></i> Configurar Template
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="fas fa-times-circle"></i> Não associado
                                                    </button>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $template->fields->count() }} campos</small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <small class="text-muted">
                                            Campos obrigatórios: {{ $template->fields->where('required', true)->count() }}
                                            <br>
                                            Campos opcionais: {{ $template->fields->where('required', false)->count() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Não há templates disponíveis para este domínio.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Informações do Domínio
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Nome do Domínio:</dt>
                                <dd class="col-sm-8">{{ $domain->name }}</dd>
                                
                                <dt class="col-sm-4">Zone ID:</dt>
                                <dd class="col-sm-8">{{ $domain->zone_id }}</dd>
                                
                                <dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    @php
                                        $status = $domain->pivot ? $domain->pivot->status : 'unknown';
                                    @endphp
                                    @if($status === 'active')
                                        <span class="badge bg-success">Ativo</span>
                                    @elseif($status === 'paused')
                                        <span class="badge bg-warning">Pausado</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($status) }}</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Registros DNS:</dt>
                                <dd class="col-sm-8">{{ $domain->dnsRecords->count() }}</dd>
                                
                                <dt class="col-sm-4">Templates associados:</dt>
                                <dd class="col-sm-8">
                                    {{ $domain->dnsRecords->whereNotNull('bank_template_id')->count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
