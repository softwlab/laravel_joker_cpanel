@extends('layouts.app')

@section('title', 'Criar Novo Grupo Organizado')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cliente.linkgroups.index') }}">Meus Grupos Organizados</a></li>
            <li class="breadcrumb-item active" aria-current="page">Criar Novo Grupo</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h1 class="h4 mb-0">Criar Novo Grupo Organizado</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('cliente.linkgroups.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Ativo</label>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('cliente.linkgroups.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
