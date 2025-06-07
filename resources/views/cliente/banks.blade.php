@extends('layouts.cliente')

@section('title', 'Meus Links Bancários')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Meus Links Bancários</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('cliente.banks.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Criar Novo Link
            </a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Meus Grupos Organizados</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Organize seus links bancários em grupos para facilitar o acesso e gerenciamento. Cada grupo pode conter vários links de diferentes bancos.</p>
                    
                    @if($linkGroups && $linkGroups->count() > 0)
                        <div class="row">
                            @foreach($linkGroups as $group)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h6 class="mb-0">{{ $group->title }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="small">{{ Str::limit($group->description, 100) }}</p>
                                            <p class="mb-1"><strong>Links associados:</strong> {{ $group->banks->count() }}</p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="{{ route('cliente.linkgroups.show', $group->id) }}" class="btn btn-sm btn-outline-primary">Gerenciar Grupo</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Você ainda não possui grupos de links cadastrados.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <h4 class="mb-3">Meus Links Bancários Ativos</h4>

@if($banks->count() > 0)
<div class="row">
    @foreach($banks as $bank)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ $bank->name ?: 'Link Bancário ' . $bank->id }}</h6>
                @if($bank->active)
                    <span class="badge bg-success text-white">Ativo</span>
                @else
                    <span class="badge bg-danger text-white">Inativo</span>
                @endif
            </div>
            <div class="card-body">
                <p class="card-text">
                    <strong>Template:</strong> {{ $bank->template->name ?? 'Não definido' }}<br>
                    <strong>Descrição:</strong> {{ $bank->description ? \Illuminate\Support\Str::limit($bank->description, 50) : 'Não informada' }}<br>
                    <strong>URL do Link:</strong> {{ $bank->url ? \Illuminate\Support\Str::limit($bank->url, 30) : 'Não informada' }}
                </p>
                
                @if(isset($bank->links['atual']))
                <div class="mb-2">
                    <small class="text-muted">Endereço do Link:</small><br>
                    <code class="small">{{ \Illuminate\Support\Str::limit($bank->links['atual'], 40) }}</code>
                </div>
                @endif
                
                <div class="d-flex justify-content-around mt-3">
                    <a href="{{ route('cliente.banks.show', $bank->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Gerenciar Link
                    </a>
                    <a href="{{ $bank->url }}" class="btn btn-sm btn-success" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Acessar
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> Você ainda não possui links bancários cadastrados.
    <a href="{{ route('cliente.banks.create') }}" class="alert-link">Clique aqui</a> para criar seu primeiro link bancário.
</div>
@endif

</div> <!-- Fechamento do container-fluid -->
@endsection