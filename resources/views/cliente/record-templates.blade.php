@extends('layouts.app')

@section('title', 'Templates do Registro')

@section('content')
<div class="container-fluid">
    <!-- Cabeçalho com breadcrumb -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Templates para o Registro: {{ $record->name }}</h1>
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
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Este registro DNS usa templates multipágina. Configure cada template individualmente para personalizar os campos.
                    </div>

                    <!-- Template Principal -->
                    <h4>Template Principal</h4>
                    <div class="table-responsive mb-4">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Caminho</th>
                                    <th width="15%" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($primaryTemplate)
                                <tr>
                                    <td><strong>{{ $primaryTemplate->name }}</strong></td>
                                    <td>{{ $primaryTemplate->description ?? 'Sem descrição' }}</td>
                                    <td><span class="badge bg-primary">Página Principal</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('cliente.templates.config', ['template_id' => $primaryTemplate->id, 'record_id' => $record->id, 'is_primary' => 'true']) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-cog"></i> Configurar
                                        </a>
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <td colspan="4" class="text-center">Nenhum template principal configurado</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Templates Secundários -->
                    <h4>Templates Secundários</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Caminho</th>
                                    <th width="15%" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($secondaryTemplates->count() > 0)
                                    @foreach($secondaryTemplates as $template)
                                    <tr>
                                        <td><strong>{{ $template->name }}</strong></td>
                                        <td>{{ $template->description ?? 'Sem descrição' }}</td>
                                        <td>
                                            <code>/{{ $template->pivot->path_segment ?? '' }}</code>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('cliente.templates.config', ['template_id' => $template->id, 'record_id' => $record->id, 'is_primary' => 'false']) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-cog"></i> Configurar
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="4" class="text-center">Nenhum template secundário configurado</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection