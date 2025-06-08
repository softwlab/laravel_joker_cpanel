@extends('layouts.admin')

@section('title', 'Nova Chave API Pública')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Nova Chave API Pública</h1>
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
                    <form method="POST" action="{{ route('admin.api_keys.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nome da Chave <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                Identifique o propósito desta chave (ex: Integração site cliente X)
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            
                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                Informações adicionais sobre o uso desta chave
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Atenção:</strong> Uma vez criada, a chave de API completa será exibida apenas uma vez!
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Gerar Nova Chave API
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle"></i> Informações
                </div>
                <div class="card-body">
                    <p>As chaves de API públicas permitem acesso aos endpoints da API pública 
                    <code>PublicaExternalPages</code> para obter dados sobre domínios e seus templates.</p>
                    
                    <p>Todas as operações realizadas com uma chave de API são registradas para auditoria.</p>
                    
                    <h6>Endpoints disponíveis:</h6>
                    <ul>
                        <li><code>GET /api/public/domain_external/{identifier}</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
