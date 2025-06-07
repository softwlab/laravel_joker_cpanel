@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Criar Template de Banco</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Novo Template de Banco</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.bank-templates.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="name">Nome do Template *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="template_url">URL do Template</label>
                    <input type="url" class="form-control @error('template_url') is-invalid @enderror" id="template_url" name="template_url" value="{{ old('template_url') }}">
                    <small class="form-text text-muted">URL para a página de login do banco</small>
                    @error('template_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="logo">Logo</label>
                    <input type="text" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" value="{{ old('logo') }}">
                    <small class="form-text text-muted">URL da imagem ou nome do ícone</small>
                    @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="active" name="active" checked>
                        <label class="custom-control-label" for="active">Ativo</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <a href="{{ route('admin.bank-templates.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
