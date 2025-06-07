@extends('layouts.app')

@section('title', 'Criar Usuário')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Criar Usuário</h1>
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
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control @error('nome') is-invalid @enderror"
                            id="nome" name="nome" value="{{ old('nome') }}" required>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control @error('senha') is-invalid @enderror"
                            id="senha" name="senha" required>
                        @error('senha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="nivel" class="form-label">Nível</label>
                        <select class="form-select @error('nivel') is-invalid @enderror" 
                                id="nivel" name="nivel" required>
                            <option value="">Selecione...</option>
                            <option value="cliente" {{ old('nivel') === 'cliente' ? 'selected' : '' }}>
                                Cliente
                            </option>
                            <option value="admin" {{ old('nivel') === 'admin' ? 'selected' : '' }}>
                                Administrador
                            </option>
                        </select>
                        @error('nivel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="ativo" name="ativo" 
                               {{ old('ativo', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="ativo">
                            Usuário ativo
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Criar Usuário
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection