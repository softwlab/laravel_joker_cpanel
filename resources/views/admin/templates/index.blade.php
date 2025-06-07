@extends('layouts.admin')

@section('title', 'Gerenciar Instituições Bancárias')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gerenciar Instituições Bancárias</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.templates.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-plus"></i> Nova Instituição Bancária
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Logo</th>
                            <th scope="col">Nome da Instituição</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Status</th>
                            <th scope="col">Links Associados</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                        <tr>
                            <td>{{ $template->id }}</td>
                            <td class="text-center">
                                @if($template->logo)
                                    <img src="{{ asset('storage/' . $template->logo) }}" alt="{{ $template->name }}" 
                                        class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                @else
                                    <i class="fas fa-university text-secondary fa-2x"></i>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $template->name }}</strong>
                                @if($template->description)
                                <div class="small text-muted">{{ Str::limit($template->description, 50) }}</div>
                                @endif
                            </td>
                            <td><code>{{ $template->slug }}</code></td>
                            <td>
                                @if($template->active)
                                <span class="badge bg-success">Ativo</span>
                                @else
                                <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $template->banks()->count() }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $template->id }}" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Modal de confirmação de exclusão -->
                                <div class="modal fade" id="deleteModal{{ $template->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $template->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $template->id }}">Confirmar exclusão</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Tem certeza que deseja excluir a instituição bancária <strong>{{ $template->name }}</strong>?</p>
                                                
                                                @if($template->banks()->count() > 0)
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> 
                                                    Esta instituição está associada a {{ $template->banks()->count() }} link(s) bancário(s).
                                                    Não será possível excluí-la.
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                
                                                @if($template->banks()->count() == 0)
                                                <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i> Nenhuma instituição bancária cadastrada.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
