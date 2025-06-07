@extends('layouts.admin')

@section('title', 'Logs de Acesso')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Logs de Acesso</h1>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Usuário</th>
                    <th scope="col">IP</th>
                    <th scope="col">User Agent</th>
                    <th scope="col">Data de Acesso</th>
                    <th scope="col">Último Acesso</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->usuario->name }}</td>
                    <td>{{ $log->ip }}</td>
                    <td class="text-truncate" style="max-width: 200px;">{{ $log->user_agent }}</td>
                    <td>{{ $log->data_acesso->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->ultimo_acesso->format('d/m/Y H:i:s') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Nenhum log encontrado</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $logs->links() }}
    </div>
</div>
@endsection
