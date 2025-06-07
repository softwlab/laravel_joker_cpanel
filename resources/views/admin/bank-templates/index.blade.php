@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Templates de Bancos</h1>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Templates de Bancos</h6>
            <a href="{{ route('admin.bank-templates.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Novo Template
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Slug</th>
                            <th>Campos</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>{{ $template->id }}</td>
                                <td>{{ $template->name }}</td>
                                <td>{{ $template->slug }}</td>
                                <td>{{ $template->fields_count }}</td>
                                <td>
                                    @if($template->active)
                                        <span class="badge badge-success">Ativo</span>
                                    @else
                                        <span class="badge badge-danger">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.bank-templates.edit', $template->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.bank-templates.show', $template->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.bank-templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este template?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Nenhum template encontrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $templates->links() }}
        </div>
    </div>
</div>
@endsection
