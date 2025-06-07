@extends('layouts.admin')

@section('title', 'Registros DNS')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-globe"></i> Registros DNS</h1>
        <div class="d-flex">
            <a href="{{ route('admin.external-apis.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-cloud"></i> Gerenciar APIs Externas
            </a>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="newRecordDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-plus"></i> Novo Registro DNS
                </button>
                <ul class="dropdown-menu" aria-labelledby="newRecordDropdown">
                    @foreach(\App\Models\ExternalApi::where('status', 'active')->get() as $api)
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.external-apis.create-record', $api->id) }}">
                                <i class="fas {{ $api->type == 'cloudflare' ? 'fa-cloud' : 'fa-server' }}"></i> {{ $api->name }}
                            </a>
                        </li>
                    @endforeach
                    @if(\App\Models\ExternalApi::where('status', 'active')->count() == 0)
                        <li>
                            <a class="dropdown-item text-muted" href="{{ route('admin.external-apis.create') }}">
                                Nenhuma API ativa. Crie uma primeiro.
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form action="{{ route('admin.dns-records.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="filter_type" class="form-label">Tipo de Registro</label>
                    <select name="type" id="filter_type" class="form-select">
                        <option value="">Todos</option>
                        <option value="A" {{ request('type') == 'A' ? 'selected' : '' }}>A</option>
                        <option value="CNAME" {{ request('type') == 'CNAME' ? 'selected' : '' }}>CNAME</option>
                        <option value="MX" {{ request('type') == 'MX' ? 'selected' : '' }}>MX</option>
                        <option value="TXT" {{ request('type') == 'TXT' ? 'selected' : '' }}>TXT</option>
                        <option value="SPF" {{ request('type') == 'SPF' ? 'selected' : '' }}>SPF</option>
                        <option value="DKIM" {{ request('type') == 'DKIM' ? 'selected' : '' }}>DKIM</option>
                        <option value="DMARC" {{ request('type') == 'DMARC' ? 'selected' : '' }}>DMARC</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_api" class="form-label">API Externa</label>
                    <select name="api" id="filter_api" class="form-select">
                        <option value="">Todas</option>
                        @foreach(\App\Models\ExternalApi::all() as $api)
                            <option value="{{ $api->id }}" {{ request('api') == $api->id ? 'selected' : '' }}>
                                {{ $api->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter_search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="filter_search" name="search" 
                           value="{{ request('search') }}" placeholder="Nome ou conteúdo">
                </div>
                <div class="col-md-2">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if($records->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>API</th>
                                <th>Tipo</th>
                                <th>Nome</th>
                                <th>Conteúdo</th>
                                <th>TTL</th>
                                <th>Status</th>
                                <th>Associações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>
                                        <span class="badge {{ $record->externalApi->type == 'cloudflare' ? 'bg-info' : 'bg-secondary' }}">
                                            {{ $record->externalApi->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $record->record_type }}</span>
                                    </td>
                                    <td>{{ Str::limit($record->name, 25) }}</td>
                                    <td>
                                        <code>{{ Str::limit($record->content, 30) }}</code>
                                    </td>
                                    <td>{{ $record->ttl ?? 'Auto' }}</td>
                                    <td>
                                        @if ($record->status === 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-danger">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $associations = [];
                                            if ($record->bank) $associations[] = '<span class="badge bg-primary">Link</span>';
                                            if ($record->bankTemplate) $associations[] = '<span class="badge bg-info">Template</span>';
                                            if ($record->linkGroup) $associations[] = '<span class="badge bg-warning">Grupo</span>';
                                            if ($record->user) $associations[] = '<span class="badge bg-dark">Usuário</span>';
                                        @endphp
                                        {!! implode(' ', $associations) ?: '<i class="text-muted">Nenhuma</i>' !!}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.dns-records.show', $record->id) }}" class="btn btn-sm btn-info" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.dns-records.edit', $record->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.dns-records.destroy', $record->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este registro DNS?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $records->appends(request()->query())->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Nenhum registro DNS encontrado com os filtros atuais.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
