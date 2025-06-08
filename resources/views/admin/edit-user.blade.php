@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Usuário: {{ $user->nome }}</h1>
    <a href="{{ route('admin.users') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Voltar
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Dados do Usuário</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
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
                        <label for="senha" class="form-label">Nova Senha (deixe em branco para manter)</label>
                        <input type="password" class="form-control @error('senha') is-invalid @enderror"
                            id="senha" name="senha">
                        @error('senha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="nivel" class="form-label">Nível</label>
                        <select class="form-select @error('nivel') is-invalid @enderror" 
                                id="nivel" name="nivel" required>
                            <option value="cliente" {{ old('nivel', $user->nivel) === 'cliente' ? 'selected' : '' }}>
                                Cliente
                            </option>
                            <option value="admin" {{ old('nivel', $user->nivel) === 'admin' ? 'selected' : '' }}>
                                Administrador
                            </option>
                        </select>
                        @error('nivel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="ativo" class="form-label">Status</label>
                        <select class="form-select @error('ativo') is-invalid @enderror" 
                                id="ativo" name="ativo" required>
                            <option value="1" {{ old('ativo', $user->ativo) == 1 ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ old('ativo', $user->ativo) == 0 ? 'selected' : '' }}>Inativo</option>
                        </select>
                        @error('ativo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection