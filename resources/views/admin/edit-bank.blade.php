@extends('layouts.admin')

@section('title', 'Editar Banco')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Editar Banco: {{ $bank->name }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.banks') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.banks.update', $bank->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $bank->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $bank->slug) }}">
                            <small class="text-muted">Identificador único para URLs (sem espaços, apenas letras minúsculas e números)</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $bank->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">URL</label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $bank->url) }}">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="usuario_id" class="form-label">Proprietário</label>
                            <select class="form-select @error('usuario_id') is-invalid @enderror" id="usuario_id" name="usuario_id" required>
                                <option value="">Selecione um usuário</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('usuario_id', $bank->usuario_id) == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->nome }} ({{ $usuario->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('usuario_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', $bank->active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Ativo</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Atualizar Banco</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Links</h5>
                </div>
                <div class="card-body">
                    @if($bank->links)
                        <div class="mb-3">
                            <label class="form-label">Link Atual</label>
                            <p class="form-control-plaintext">{{ $bank->links['atual'] ?? '-' }}</p>
                        </div>

                        @if(!empty($bank->links['redir']))
                            <label class="form-label">Redirecionamentos</label>
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
        </div>
    </div>
</div>
@endsection
