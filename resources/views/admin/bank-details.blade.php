@extends('layouts.admin')

@section('title', 'Detalhes do Banco')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Banco: {{ $bank->name }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.banks.edit', $bank->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('admin.banks') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Informações Gerais</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Nome</dt>
                        <dd class="col-sm-9">{{ $bank->name }}</dd>
                        
                        <dt class="col-sm-3">Slug</dt>
                        <dd class="col-sm-9">{{ $bank->slug }}</dd>
                        
                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            @if($bank->active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-danger">Inativo</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Proprietário</dt>
                        <dd class="col-sm-9">
                            <a href="{{ route('admin.users.show', $bank->usuario->id) }}">
                                {{ $bank->usuario->nome }} ({{ $bank->usuario->email }})
                            </a>
                        </dd>
                        
                        <dt class="col-sm-3">URL</dt>
                        <dd class="col-sm-9">
                            @if($bank->url)
                                <a href="{{ $bank->url }}" target="_blank">{{ $bank->url }}</a>
                            @else
                                <span class="text-muted">Não definido</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Descrição</dt>
                        <dd class="col-sm-9">
                            {{ $bank->description ?: 'Sem descrição' }}
                        </dd>
                        
                        <dt class="col-sm-3">Criado em</dt>
                        <dd class="col-sm-9">{{ $bank->created_at->format('d/m/Y H:i:s') }}</dd>
                        
                        <dt class="col-sm-3">Atualizado em</dt>
                        <dd class="col-sm-9">{{ $bank->updated_at->format('d/m/Y H:i:s') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Links</h5>
                </div>
                <div class="card-body">
                    @if($bank->links)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Link Atual</label>
                            <p class="form-control-plaintext">
                                @if(!empty($bank->links['atual']))
                                    <a href="{{ $bank->links['atual'] }}" target="_blank">{{ $bank->links['atual'] }}</a>
                                @else
                                    <span class="text-muted">Não definido</span>
                                @endif
                            </p>
                        </div>

                        @if(!empty($bank->links['redir']))
                            <label class="form-label fw-bold">Redirecionamentos</label>
                            <ul class="list-group">
                                @foreach($bank->links['redir'] as $link)
                                    <li class="list-group-item">
                                        <a href="{{ $link }}" target="_blank">{{ $link }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @else
                        <p class="card-text text-muted">Nenhum link configurado.</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">Zona de Perigo</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.banks.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este banco? Esta ação não pode ser desfeita.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Excluir Banco
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
