@extends('layouts.app')

@section('title', 'Todos os Templates')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho com breadcrumb -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Templates Disponíveis</h1>
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
                    <i class="fas fa-list"></i> Todos os Templates
                </div>
                <div class="card-body">
                    @if($templates->count() > 0)
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle"></i> 
                            Para configurar um template, acesse o seu Dashboard e clique no botão "Configurar Página" ao lado do domínio/página desejado.
                        </div>

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
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Total de campos
                                            <span class="badge bg-primary rounded-pill">{{ $template->fields->count() }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Campos obrigatórios
                                            <span class="badge bg-danger rounded-pill">{{ $template->fields->where('required', true)->count() }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Campos opcionais
                                            <span class="badge bg-info rounded-pill">{{ $template->fields->where('required', false)->count() }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Não há templates disponíveis no momento.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
