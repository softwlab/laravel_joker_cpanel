@extends('layouts.app')

@section('title', 'Configurar Grupo Organizado')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cliente.linkgroups.index') }}">Meus Grupos Organizados</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cliente.linkgroups.show', $linkGroup->id) }}">{{ $linkGroup->title }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Configurar</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h1 class="h4 mb-0">Configurar Grupo Organizado</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('cliente.linkgroups.update', $linkGroup->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $linkGroup->title) }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $linkGroup->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="active" name="active" value="1" {{ old('active', $linkGroup->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Ativo</label>
                </div>
                
                <div class="d-flex justify-content-between">
                    <form action="{{ route('cliente.linkgroups.destroy', $linkGroup->id) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Excluir Grupo</button>
                    </form>
                    
                    <div>
                        <a href="{{ route('cliente.linkgroups.show', $linkGroup->id) }}" class="btn btn-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmação antes de excluir
        const deleteForm = document.querySelector('.delete-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(event) {
                if (!confirm('Tem certeza que deseja excluir este grupo? Todos os links dentro dele serão excluídos.')) {
                    event.preventDefault();
                }
            });
        }
    });
</script>
@endsection
