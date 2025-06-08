@extends('layouts.admin')

@section('title', 'Editar Chave API')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Editar Chave API: {{ $apiKey->name }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.api_keys.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.api_keys.update', $apiKey->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome da Chave <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $apiKey->name) }}" required>
                            
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $apiKey->description) }}</textarea>
                            
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ $apiKey->active ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">Chave Ativa</label>
                            </div>
                            <div class="form-text">
                                Desative a chave para bloquear temporariamente o acesso sem precisar excluí-la
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="key_preview" class="form-label">Fragmento da Chave</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="key_preview" value="{{ substr($apiKey->key, 0, 16) }}..." readonly>
                                <a href="{{ route('admin.api_keys.show', $apiKey->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-eye"></i> Ver Completa
                                </a>
                            </div>
                            <div class="form-text">
                                Para gerar uma nova chave, use a opção "Regenerar Chave"
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle"></i> Informações
                </div>
                <div class="card-body">
                    <p>Ao editar uma chave de API, você pode:</p>
                    <ul>
                        <li>Alterar o nome e descrição</li>
                        <li>Ativar ou desativar o acesso</li>
                    </ul>
                    
                    <p>Para alterar o valor da chave, use a opção "Regenerar Chave" na página de detalhes.</p>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-exclamation-triangle"></i> Zona de Perigo
                </div>
                <div class="card-body">
                    <p>Para excluir esta chave permanentemente:</p>
                    
                    <form action="{{ route('admin.api_keys.destroy', $apiKey->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta chave API? Esta ação não pode ser desfeita.');">
                        @csrf
                        @method('DELETE')
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Excluir Chave API
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
