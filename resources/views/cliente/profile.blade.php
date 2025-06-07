@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Meu Perfil</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informações Pessoais</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('cliente.profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control @error('nome') is-invalid @enderror"
                            id="nome" name="nome" value="{{ old('nome', $user->nome) }}" required>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="senha" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                        <input type="password" class="form-control @error('senha') is-invalid @enderror"
                            id="senha" name="senha">
                        @error('senha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="senha_confirmation" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="senha_confirmation" name="senha_confirmation">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informações da Conta</h5>
            </div>
            <div class="card-body">
                <p><strong>Nível:</strong> 
                    <span class="badge bg-info">{{ ucfirst($user->nivel) }}</span>
                </p>
                <p><strong>Status:</strong> 
                    @if($user->ativo)
                        <span class="badge bg-success">Ativo</span>
                    @else
                        <span class="badge bg-danger">Inativo</span>
                    @endif
                </p>
                <p><strong>Cadastrado em:</strong><br>
                    {{ $user->created_at->format('d/m/Y H:i') }}
                </p>
                <p><strong>Última atualização:</strong><br>
                    {{ $user->updated_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection