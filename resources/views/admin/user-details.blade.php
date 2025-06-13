@extends('layouts.app')

@php
    use Illuminate\Support\Facades\DB;
@endphp

@section('title', 'Detalhes do Usuário')



@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detalhes do Usuário: {{ $user->nome }}</h1>
    <div class="btn-toolbar">
        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit"></i> Editar
        </a>
        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Excluir
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Painel de estatísticas resumido -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de DNS Records</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['totalDnsRecords'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-globe fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total de Visitantes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['totalVisitantes'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Informações Bancárias</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['totalInfoBancarias'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Assinaturas Ativas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ isset($activeSubscriptions) ? $activeSubscriptions->count() : 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Conteúdo Sequencial -->
<div class="content-sections">
    <!-- Tab: Informações Básicas -->
    <div class="content-section mb-4">
    <h3 class="border-bottom pb-2 mb-4"><i class="fas fa-user-circle me-2"></i>Informações Básicas</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-id-card me-1"></i> Informações Pessoais</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">ID:</span>
                                <span>{{ $user->id }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Nome:</span>
                                <span>{{ $user->nome }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">E-mail:</span>
                                <span>{{ $user->email }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Nível:</span>
                                <span>
                                    <span class="badge bg-{{ $user->nivel === 'admin' ? 'danger' : 'info' }}">
                                        {{ ucfirst($user->nivel) }}
                                    </span>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Status:</span>
                                <span>
                                    @if($user->ativo)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Data de Criação:</span>
                                <span>{{ $user->created_at->format('d/m/Y H:i') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="fw-bold">Última Atualização:</span>
                                <span>{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs me-1"></i> Configurações do Usuário</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">Tema:</span>
                            <span>{{ $user->userConfig->theme ?? 'Padrão' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">Receber Notificações:</span>
                            <span>
                                @if($user->userConfig->notifications ?? false)
                                    <span class="badge bg-success">Sim</span>
                                @else
                                    <span class="badge bg-secondary">Não</span>
                                @endif
                            </span>
                        </li>
                        @if($user->userConfig)
                            @foreach(json_decode(json_encode($user->userConfig), true) as $key => $value)
                                @if(!in_array($key, ['id', 'user_id', 'theme', 'notifications', 'created_at', 'updated_at']))
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span>
                                    @if(is_bool($value))
                                        <span class="badge bg-{{ $value ? 'success' : 'secondary' }}">{{ $value ? 'Sim' : 'Não' }}</span>
                                    @elseif(is_array($value) || is_object($value))
                                        <code>{{ json_encode($value) }}</code>
                                    @else
                                        {{ $value }}
                                    @endif
                                    </span>
                                </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Assinaturas -->
    <div class="content-section mb-4">
    <h3 class="border-bottom pb-2 mb-4"><i class="fas fa-credit-card me-2"></i>Assinaturas</h3>
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i> Assinaturas Ativas</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($activeSubscriptions) && $activeSubscriptions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Valor</th>
                                            <th>Data Início</th>
                                            <th>Data Fim</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeSubscriptions as $sub)
                                        <tr>
                                            <td><code>{{ $sub->uuid }}</code></td>
                                            <td>{{ $sub->name }}</td>
                                            <td>R$ {{ number_format($sub->value, 2, ',', '.') }}</td>
                                            <td>{{ $sub->start_date->format('d/m/Y') }}</td>
                                            <td>{{ $sub->end_date->format('d/m/Y') }}</td>
                                            <td><span class="badge bg-success">Ativa</span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1" style="height: 8px;">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.subscriptions.show', $sub->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.subscriptions.edit', $sub->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Este usuário não possui assinaturas ativas.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i> Assinaturas Inativas</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($inactiveSubscriptions) && $inactiveSubscriptions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Valor</th>
                                            <th>Data Início</th>
                                            <th>Data Fim</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inactiveSubscriptions as $sub)
                                        <tr>
                                            <td><code>{{ $sub->uuid }}</code></td>
                                            <td>{{ $sub->name }}</td>
                                            <td>R$ {{ number_format($sub->value, 2, ',', '.') }}</td>
                                            <td>{{ $sub->start_date->format('d/m/Y') }}</td>
                                            <td>{{ $sub->end_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if($sub->status == 'active' && $sub->end_date < now())
                                                    <span class="badge bg-danger">Expirada</span>
                                                @elseif($sub->status == 'cancelled')
                                                    <span class="badge bg-warning">Cancelada</span>
                                                @elseif($sub->status == 'suspended')
                                                    <span class="badge bg-warning">Suspensa</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($sub->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.subscriptions.show', $sub->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Este usuário não possui assinaturas inativas.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab: Templates -->
    <div class="content-section mb-4">
    <h3 class="border-bottom pb-2 mb-4"><i class="fas fa-file-alt me-2"></i>Templates</h3>
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> Templates Configurados</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($templateConfigs) && $templateConfigs->count() > 0)
                            <div class="accordion" id="templatesAccordion">
                                @foreach($templateConfigs as $index => $config)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                            <strong>{{ $config->template->name ?? 'Template #'.$config->template_id }}</strong>
                                            <span class="badge bg-primary ms-2">{{ count((array)$config->config) }} campos configurados</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#templatesAccordion">
                                        <div class="accordion-body">
                                            @if(!empty($config->config))
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Campo</th>
                                                                <th>Ativo</th>
                                                                <th>Ordem</th>
                                                                <th>Configurações Extras</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $fields = collect((array)$config->config)->map(function($item, $key) {
                                                                    $item['field'] = $key;
                                                                    return (object)$item;
                                                                })->sortBy('order');
                                                            @endphp
                                                            
                                                            @foreach($fields as $field)
                                                            <tr>
                                                                <td>{{ $field->field }}</td>
                                                                <td>
                                                                    @if($field->active)
                                                                        <span class="badge bg-success">Ativo</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Inativo</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $field->order }}</td>
                                                                <td>
                                                                    @php
                                                                        $extras = (array)$field;
                                                                        unset($extras['field'], $extras['active'], $extras['order']);
                                                                    @endphp
                                                                    
                                                                    @if(count($extras) > 0)
                                                                        <code>{{ json_encode($extras) }}</code>
                                                                    @else
                                                                        <span class="text-muted">Nenhuma</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="{{ route('cliente.templates.config.update', $config->template_id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit me-1"></i> Editar Configurações
                                                    </a>
                                                </div>
                                            @else
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i> Não foram encontradas configurações de campo para este template.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Este usuário não possui templates configurados.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab: DNS & Domínios -->
    <div class="content-section mb-4">
    <h3 class="border-bottom pb-2 mb-4"><i class="fas fa-globe me-2"></i>DNS & Domínios</h3>
        <div class="row">
            <!-- Cloudflare Domains -->
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-cloud me-2"></i> Domínios Cloudflare</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($cloudflareDomains) && $cloudflareDomains->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Domínio</th>
                                            <th>API Key</th>
                                            <th>Zone ID</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cloudflareDomains as $cfDomain)
                                        <tr>
                                            <td>{{ $cfDomain->id }}</td>
                                            <td>{{ $cfDomain->domain }}</td>
                                            <td><code>{{ Str::limit($cfDomain->api_key, 15) }}</code></td>
                                            <td><code>{{ Str::limit($cfDomain->zone_id, 15) }}</code></td>
                                            <td>
                                                @if($cfDomain->active)
                                                    <span class="badge bg-success">Ativo</span>
                                                @else
                                                    <span class="badge bg-secondary">Inativo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.users.cloudflare', ['user' => $user->id, 'domain' => $cfDomain->id]) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Este usuário não possui domínios Cloudflare configurados.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- DNS Records -->
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-server me-2"></i> Registros DNS</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($dnsRecords) && $dnsRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Subdomínio</th>
                                            <th>Domínio</th>
                                            <th>Tipo</th>
                                            <th>Conteúdo</th>
                                            <th>Status</th>
                                            <th>Visitantes</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dnsRecords as $dns)
                                        <tr>
                                            <td>{{ $dns->id }}</td>
                                            <td>{{ !empty($dns->name) ? $dns->name : (!empty($dns->subdomain) ? $dns->subdomain : 'N/A') }}</td>
                                            <td>{{ !empty($dns->zone_name) ? $dns->zone_name : (!empty($dns->domain) ? $dns->domain : 'N/A') }}</td>
                                            <td><span class="badge bg-secondary">{{ $dns->type }}</span></td>
                                            <td><code>{{ Str::limit($dns->content, 25) }}</code></td>
                                            <td>
                                                @if($dns->active)
                                                    <span class="badge bg-success">Ativo</span>
                                                @else
                                                    <span class="badge bg-secondary">Inativo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $dns->visitantes_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.dns-records.show', $dns->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Este usuário não possui registros DNS configurados.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab: Segurança & Acessos -->
    <div class="content-section mb-4">
    <h3 class="border-bottom pb-2 mb-4"><i class="fas fa-shield-alt me-2"></i>Segurança & Acessos</h3>
        <div class="row">
            <!-- API Keys -->
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-key me-2"></i> API Keys</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($user->apiKeys) && $user->apiKeys->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>API Key</th>
                                            <th>Status</th>
                                            <th>Criado em</th>
                                            <th>Último uso</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->apiKeys as $apiKey)
                                        <tr>
                                            <td>{{ $apiKey->name }}</td>
                                            <td><code>{{ Str::limit($apiKey->key, 20) }}</code></td>
                                            <td>
                                                @if($apiKey->active)
                                                    <span class="badge bg-success">Ativo</span>
                                                @else
                                                    <span class="badge bg-secondary">Inativo</span>
                                                @endif
                                            </td>
                                            <td>{{ $apiKey->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $apiKey->last_used_at ? $apiKey->last_used_at->format('d/m/Y H:i') : 'Nunca' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Este usuário não possui API Keys configuradas.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Histórico de Acessos -->
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i> Histórico de Acessos</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($acessos) && $acessos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Data/Hora</th>
                                            <th>IP</th>
                                            <th>Dispositivo</th>
                                            <th>Localização</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($acessos as $acesso)
                                        <tr>
                                            <td>{{ $acesso->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td><code>{{ $acesso->ip }}</code></td>
                                            <td>
                                                <small>{{ $acesso->user_agent }}</small>
                                            </td>
                                            <td>
                                                @if($acesso->location)
                                                    {{ $acesso->location }}
                                                @else
                                                    <span class="text-muted">Não disponível</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($acesso->status == 'success')
                                                    <span class="badge bg-success">Sucesso</span>
                                                @elseif($acesso->status == 'failed')
                                                    <span class="badge bg-danger">Falha</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $acesso->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if(method_exists($acessos, 'links') && $acessos->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $acessos->links() }}
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Não há registros de acesso para este usuário.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tab: Estatísticas -->
    <div class="content-section mb-4">
    <h3 class="border-bottom pb-2 mb-4"><i class="fas fa-chart-bar me-2"></i>Estatísticas</h3>
        <div class="row">
            <!-- Métricas Principais -->
            <!-- Métricas Principais -->
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Resumo Estatístico</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-primary shadow py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">DNS Records</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['totalDnsRecords'] ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-globe fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-success shadow py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Visitantes</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['totalVisitantes'] ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-users fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-info shadow py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Info. Bancárias</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['totalInfoBancarias'] ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-warning shadow py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Taxa de Conversão</div>
                                                @php
                                                    $conversionRate = 0;
                                                    if (isset($userStatsData['totalVisitantes']) && $userStatsData['totalVisitantes'] > 0) {
                                                        $conversionRate = ($userStatsData['totalInfoBancarias'] / $userStatsData['totalVisitantes']) * 100;
                                                    }
                                                @endphp
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($conversionRate, 1) }}%</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if(isset($userStatsData['visitantesPorMes']))
                            <div class="chart-container pt-4">
                                <h6 class="mb-3">Visitantes por Mês</h6>
                                <canvas id="visitantesChart" height="100"></canvas>
                            </div>
                            
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const ctxVisitantes = document.getElementById('visitantesChart').getContext('2d');
                                    
                                    const visitantesData = JSON.parse('{!! addslashes(json_encode($userStatsData["visitantesPorMes"] ?? [])) !!}');
                                    const labelsVisitantes = visitantesData.map(function(item) { return item.mes; });
                                    const valuesVisitantes = visitantesData.map(function(item) { return item.total; });
                                    
                                    new Chart(ctxVisitantes, {
                                        type: 'line',
                                        data: {
                                            labels: labelsVisitantes,
                                            datasets: [{
                                                label: 'Visitantes',
                                                data: valuesVisitantes,
                                                backgroundColor: 'rgba(78, 115, 223, 0.2)',
                                                borderColor: 'rgba(78, 115, 223, 1)',
                                                borderWidth: 2,
                                                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                                                pointBorderColor: '#fff',
                                                pointHoverRadius: 5,
                                                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                                                pointHoverBorderColor: '#fff',
                                                pointHitRadius: 10,
                                                tension: 0.4
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    ticks: {
                                                        precision: 0
                                                    }
                                                }
                                            },
                                            plugins: {
                                                legend: {
                                                    display: false
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        @endif
                        
                        @if(isset($userStatsData['visitantesPorDominio']))
                            <div class="chart-container pt-4 mt-4 border-top">
                                <h6 class="mb-3 mt-2">Visitantes por Domínio</h6>
                                <canvas id="dominiosChart" height="100"></canvas>
                            </div>
                            
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const ctxDominios = document.getElementById('dominiosChart').getContext('2d');
                                    
                                    const dominiosData = JSON.parse('{!! addslashes(json_encode($userStatsData["visitantesPorDominio"] ?? [])) !!}');
                                    const labelsDominios = dominiosData.map(function(item) { return item.name; });
                                    const valuesDominios = dominiosData.map(function(item) { return item.total_visitantes; });
                                    
                                    new Chart(ctxDominios, {
                                        type: 'bar',
                                        data: {
                                            labels: labelsDominios,
                                            datasets: [{
                                                label: 'Visitantes',
                                                data: valuesDominios,
                                                backgroundColor: [
                                                    'rgba(78, 115, 223, 0.7)',
                                                    'rgba(54, 185, 204, 0.7)',
                                                    'rgba(246, 194, 62, 0.7)',
                                                    'rgba(28, 200, 138, 0.7)',
                                                    'rgba(231, 74, 59, 0.7)'
                                                ],
                                                borderColor: [
                                                    'rgba(78, 115, 223, 1)',
                                                    'rgba(54, 185, 204, 1)',
                                                    'rgba(246, 194, 62, 1)',
                                                    'rgba(28, 200, 138, 1)',
                                                    'rgba(231, 74, 59, 1)'
                                                ],
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    ticks: {
                                                        precision: 0
                                                    }
                                                }
                                            }
                                        }
                                    });
                                });
                            </script>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <!-- Conteúdo atual que vamos reorganizar -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Links Bancários do Usuário</h5>
                <span class="badge bg-primary">{{ $user->banks->count() }}</span>
            </div>
            <div class="card-body">
                @if($user->banks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Instituição Bancária</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->banks as $bank)
                                <tr>
                                    <td>{{ $bank->id }}</td>
                                    <td>{{ $bank->name }}</td>
                                    <td>{{ $bank->template->name ?? 'Sem instituição bancária' }}</td>
                                    <td>
                                        @if($bank->active)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.banks.show', $bank->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        Este usuário não possui links bancários cadastrados.
                    </div>
                @endif
            </div>
        </div>


        <!-- Seção de Domínios Cloudflare -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Domínios Cloudflare</h5>
                <span class="badge bg-primary">{{ (isset($user->cloudflareDomains) ? $user->cloudflareDomains->count() : 0) + (isset($dominiosAssociados) ? count($dominiosAssociados) : 0) }}</span>
            </div>
            <div class="card-body">
                <!-- Domínios via Eloquent -->
                @if(isset($user->cloudflareDomains) && $user->cloudflareDomains->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Status</th>
                                    <th>Registros</th>
                                    <th>Fonte</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->cloudflareDomains as $domain)
                                <tr>
                                    <td>{{ $domain->name }}</td>
                                    <td>
                                        @if($domain->pivot->status === 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @elseif($domain->pivot->status === 'paused')
                                            <span class="badge bg-warning">Pausado</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($domain->pivot->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $domain->records_count ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info">Eloquent</span></td>
                                    <td>
                                        <a href="{{ route('admin.cloudflare.domain-associations.show', [$domain->id, $user->id]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                
                                <!-- Domínios carregados diretamente -->
                                @if(isset($dominiosAssociados) && $dominiosAssociados->count() > 0)
                                    @foreach($dominiosAssociados as $domain)
                                    <tr>
                                        <td>{{ $domain->name }}</td>
                                        <td>
                                            <span class="badge bg-success">Ativo</span>
                                        </td>
                                        <td>
                                            @php
                                                $recordCount = isset($dnsRecords) ? $dnsRecords->where('external_api_id', $domain->external_api_id)->count() : 0;
                                            @endphp
                                            {{ $recordCount }}
                                        </td>
                                        <td><span class="badge bg-warning">Consulta Direta</span></td>
                                        <td>
                                            <a href="{{ route('admin.cloudflare.domain-associations.show', [$domain->id, $user->id]) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                @elseif(isset($dominiosAssociados) && $dominiosAssociados->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Status</th>
                                    <th>Registros</th>
                                    <th>Fonte</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dominiosAssociados as $domain)
                                <tr>
                                    <td>{{ $domain->name }}</td>
                                    <td>
                                        <span class="badge bg-success">Ativo</span>
                                    </td>
                                    <td>
                                        @php
                                            $recordCount = isset($dnsRecords) ? $dnsRecords->where('external_api_id', $domain->external_api_id)->count() : 0;
                                        @endphp
                                        {{ $recordCount }}
                                    </td>
                                    <td><span class="badge bg-warning">Consulta Direta</span></td>
                                    <td>
                                        <a href="{{ route('admin.cloudflare.domain-associations.show', [$domain->id, $user->id]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        Este usuário não possui domínios Cloudflare associados.
                    </div>
                @endif
            </div>
        </div>

        <!-- Seção para Registros DNS associados diretamente ao usuário -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Registros DNS Associados</h5>
                <span class="badge bg-primary">{{ isset($dnsRecords) ? $dnsRecords->count() : 0 }}</span>
            </div>
            <div class="card-body">
                @if(isset($dnsRecords) && $dnsRecords->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Nome</th>
                                    <th>Conteúdo</th>
                                    <th>Domínio</th>
                                    <th>Template</th>
                                    <th>Link/Grupo</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dnsRecords as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>
                                        <span class="badge bg-secondary" data-bs-toggle="tooltip" title="{{ $record->record_type }}">
                                            <i class="{{ $record->icon }}"></i>
                                            {{ $record->record_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span data-bs-toggle="tooltip" title="{{ $record->name }}">
                                            {{ Str::limit($record->name, 20) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span data-bs-toggle="tooltip" title="{{ $record->content }}">
                                            {{ Str::limit($record->content, 20) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($record->externalApi)
                                            <span class="badge bg-info" data-bs-toggle="tooltip" title="{{ $record->externalApi->name }}">
                                                {{ Str::limit($record->externalApi->name, 15) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->bankTemplate)
                                            <span class="badge bg-warning" data-bs-toggle="tooltip" title="{{ $record->bankTemplate->name }}">
                                                {{ Str::limit($record->bankTemplate->name, 15) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->bank)
                                            <span class="badge bg-success" data-bs-toggle="tooltip" title="Link: {{ $record->bank->name }}">
                                                <i class="fas fa-link"></i> {{ Str::limit($record->bank->name, 15) }}
                                            </span>
                                        @elseif($record->linkGroup)
                                            <span class="badge bg-primary" data-bs-toggle="tooltip" title="Grupo: {{ $record->linkGroup->name }}">
                                                <i class="fas fa-layer-group"></i> {{ Str::limit($record->linkGroup->name, 15) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->status === 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.dns-records.edit', $record->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.dns-records.show', $record->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        Este usuário não possui registros DNS associados diretamente.
                    </div>
                @endif
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Histórico de Acessos</h5>
                <span class="badge bg-primary">{{ $user->acessos->count() }}</span>
            </div>
            <div class="card-body">
                @if($user->acessos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>IP</th>
                                    <th>Navegador</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->acessos->sortByDesc('created_at')->take(10) as $acesso)
                                <tr>
                                    <td>{{ $acesso->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td>{{ $acesso->ip }}</td>
                                    <td>{{ Str::limit($acesso->user_agent, 40) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($user->acessos->count() > 10)
                        <div class="text-center mt-2">
                            <small class="text-muted">Exibindo os 10 acessos mais recentes</small>
                        </div>
                    @endif
                @else
                    <div class="alert alert-info mb-0">
                        Nenhum registro de acesso para este usuário.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@if(isset($userStatsData))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Script para inicializar as abas corretamente
        document.addEventListener('DOMContentLoaded', function() {
            // Garantir que as abas sejam inicializadas pela API Bootstrap
            var tabTriggerList = [].slice.call(document.querySelectorAll('button[data-bs-toggle="tab"]'));
            tabTriggerList.forEach(function(tabTriggerEl) {
                // Se o Bootstrap não estiver inicializando as abas automaticamente, usamos nosso próprio manipulador
                tabTriggerEl.addEventListener('click', function(event) {
                    // Impedir comportamento padrão se necessário
                    if (!window.bootstrap) {
                        event.preventDefault();
                        
                        // Remover active de todas as abas
                        document.querySelectorAll('.nav-link').forEach(function(navLink) {
                            navLink.classList.remove('active');
                            navLink.setAttribute('aria-selected', 'false');
                        });
                        
                        // Adicionar active na aba clicada
                        this.classList.add('active');
                        this.setAttribute('aria-selected', 'true');
                        
                        // Pegar o alvo da aba
                        var target = document.querySelector(this.getAttribute('data-bs-target'));
                        
                        // Esconder todos os painéis
                        document.querySelectorAll('.tab-pane').forEach(function(pane) {
                            pane.classList.remove('show', 'active');
                        });
                        
                        // Mostrar o painel alvo
                        if (target) {
                            target.classList.add('show', 'active');
                        }
                    }
                });
            });
            
            // Scripts específicos para os gráficos na aba de Estatísticas
            if (document.getElementById('visitantesChart')) {
                const ctxVisitantes = document.getElementById('visitantesChart').getContext('2d');
                const visitantesData = JSON.parse('{!! addslashes(json_encode($userStatsData["visitantesPorMes"] ?? [])) !!}');
                const labelsVisitantes = visitantesData.map(function(item) { return item.mes; });
                const valuesVisitantes = visitantesData.map(function(item) { return item.total; });
                
                new Chart(ctxVisitantes, {
                    type: 'line',
                    data: {
                        labels: labelsVisitantes,
                        datasets: [{
                            label: 'Visitantes',
                            data: valuesVisitantes,
                            backgroundColor: 'rgba(78, 115, 223, 0.2)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                            pointBorderColor: '#fff',
                            pointRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        }
                    }
                });
            }
            
            if (document.getElementById('dominiosChart')) {
                const ctxDominios = document.getElementById('dominiosChart').getContext('2d');
                const dominiosData = JSON.parse('{!! addslashes(json_encode($userStatsData["visitantesPorDominio"] ?? [])) !!}');
                const labelsDominios = dominiosData.map(function(item) { return item.name; });
                const valuesDominios = dominiosData.map(function(item) { return item.total_visitantes; });
                
                new Chart(ctxDominios, {
                    type: 'bar',
                    data: {
                        labels: labelsDominios,
                        datasets: [{
                            label: 'Visitantes',
                            data: valuesDominios,
                            backgroundColor: [
                                'rgba(78, 115, 223, 0.7)',
                                'rgba(54, 185, 204, 0.7)',
                                'rgba(28, 200, 138, 0.7)',
                                'rgba(246, 194, 62, 0.7)',
                                'rgba(231, 74, 59, 0.7)'
                            ],
                            borderColor: [
                                'rgba(78, 115, 223, 1)',
                                'rgba(54, 185, 204, 1)',
                                'rgba(28, 200, 138, 1)',
                                'rgba(246, 194, 62, 1)',
                                'rgba(231, 74, 59, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
            
            // Inicializar todos os tooltips na página
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl, {
                    boundary: document.body
                });
            });
        });
    </script>
@endif

@section('scripts')
<!-- Scripts removidos - implementação simplificada sem abas -->
@endsection
