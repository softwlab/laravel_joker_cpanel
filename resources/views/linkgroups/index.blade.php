@extends('layouts.app')

@section('title', 'Gerenciar Grupos Organizados')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Meus Grupos Organizados</h1>
        <a href="{{ route('cliente.linkgroups.create') }}" class="btn btn-primary">
            <i class="fas fa-folder-plus"></i> Criar Novo Grupo
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($linkGroups->isEmpty())
    <div class="alert alert-info">
        Você ainda não criou nenhum grupo organizado. Grupos permitem organizar seus links bancários de maneira intuitiva. 
        Clique em "Criar Novo Grupo" para começar.
    </div>
    @else
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach($linkGroups as $group)
        <div class="col">
            <div class="card h-100 {{ $group->active ? 'border-success' : 'border-secondary opacity-75' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    @if($group->active)
                    <span class="badge bg-success">Ativo</span>
                    @else
                    <span class="badge bg-secondary">Inativo</span>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('cliente.linkgroups.show', $group->id) }}"><i class="fas fa-eye"></i> Visualizar</a></li>
                            <li><a class="dropdown-item" href="{{ route('cliente.linkgroups.edit', $group->id) }}"><i class="fas fa-edit"></i> Editar</a></li>
                            <li>
                                <form action="{{ route('cliente.linkgroups.destroy', $group->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash"></i> Excluir</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $group->title }}</h5>
                    <p class="card-text">{{ Str::limit($group->description, 100) }}</p>
                </div>
                <div class="card-footer">
                    <small class="text-muted">{{ $group->items->count() }} links</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirmação antes de excluir
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!confirm('Tem certeza que deseja excluir este grupo? Todos os links dentro dele serão excluídos.')) {
                    event.preventDefault();
                }
            });
        });
    });
</script>
@endsection
