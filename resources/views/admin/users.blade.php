@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Usuários</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Novo Usuário
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Nível</th>
                        <th>Status</th>
                        <th>Bancos</th>
                        <th>Último Acesso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->nome }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-{{ $user->nivel === 'admin' ? 'danger' : 'info' }}">
                                {{ ucfirst($user->nivel) }}
                            </span>
                        </td>
                        <td>
                            @if($user->ativo)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-danger">Inativo</span>
                            @endif
                        </td>
                        <td>{{ $user->banks->count() }}</td>
                        <td>
                            @if($user->acessos->count() > 0)
                                {{ $user->acessos->first()->data_acesso->format('d/m/Y H:i') }}
                            @else
                                <span class="text-muted">Nunca</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users.show', $user->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" 
                                      class="d-inline" onsubmit="return confirm('Tem certeza que deseja deletar este usuário?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection