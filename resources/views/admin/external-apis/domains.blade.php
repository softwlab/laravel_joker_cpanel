@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Domínios na {{ $api->name }}</h1>
    
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.external-apis.index') }}">APIs Externas</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.external-apis.show', $api->id) }}">{{ $api->name }}</a></li>
        <li class="breadcrumb-item active">Domínios</li>
    </ol>
    
    <!-- Card de Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $activeDomainsCount }}</h4>
                            <div>Domínios Ativos</div>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">{{ $inactiveDomainsCount }}</h4>
                            <div>Domínios Inativos</div>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-globe me-1"></i>
                Domínios disponíveis
            </div>
            <div class="d-flex gap-2">
                @if(!isset($showingSeedDomains) || !$showingSeedDomains)
                    <a href="{{ route('admin.external-apis.domains', ['id' => $api->id, 'show_all' => 1]) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye me-1"></i> Mostrar Domínios de Teste
                    </a>
                @else
                    <a href="{{ route('admin.external-apis.domains', ['id' => $api->id]) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-eye-slash me-1"></i> Ocultar Domínios de Teste
                    </a>
                @endif
                <a href="{{ route('admin.external-apis.show', $api->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
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
            
            @if(isset($error) && $error)
                <div class="alert alert-danger">
                    <strong>Erro ao listar domínios:</strong> {{ $error }}
                </div>
            @endif
            
            @if(count($domains) > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>Nameservers</th>
                                <th>Registros</th>
                                <th>Ghost</th>
                                <th>Data Adição</th>
                                <th>Última Atualização</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($domains as $domain)
                                <tr>
                                    <td>{{ $domain['id'] ?? 'N/A' }}</td>
                                    <td>{{ $domain['name'] ?? 'N/A' }}</td>
                                    <td>
                                        @if(isset($domain['status']))
                                            @if($domain['status'] == 'active')
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($domain['status']) }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($domain['nameservers']) && is_array($domain['nameservers']))
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ implode(', ', $domain['nameservers']) }}">
                                                {{ count($domain['nameservers']) }} nameservers
                                            </span>
                                        @elseif(is_string($domain['nameservers'] ?? ''))
                                            {{ $domain['nameservers'] }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Contagem correta de registros DNS por domínio
                                            $domainName = $domain['name'];
                                            $recordCount = \App\Models\DnsRecord::where('external_api_id', $api->id)
                                                ->where(function($query) use ($domainName) {
                                                    // Filtra registros que pertencem a este domínio
                                                    $query->where('name', $domainName)
                                                          ->orWhere('name', 'like', '%.' . $domainName)
                                                          ->orWhere('name', 'like', '%' . $domainName);
                                                })
                                                ->count();
                                        @endphp
                                        <span class="badge bg-info">{{ $recordCount }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $isGhost = isset($domain['is_ghost']) && $domain['is_ghost'];
                                                $newGhostValue = $isGhost ? 0 : 1;
                                                $btnClass = $isGhost ? 'btn-danger' : 'btn-secondary';
                                                $statusText = $isGhost ? 'Ativo' : 'Inativo';
                                                $formId = "ghost-form-{$domain['id']}";
                                            @endphp
                                            <form id="{{ $formId }}" action="{{ route('admin.external-apis.update-ghost') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="domain_id" value="{{ $domain['id'] }}">
                                                <input type="hidden" name="is_ghost" value="{{ $newGhostValue }}">
                                                <button type="submit" class="btn {{ $btnClass }} btn-sm">
                                                    <i class="fas fa-ghost"></i>
                                                    <span class="ghost-status-text">{{ $statusText }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        {{ isset($domain['created_at']) ? date('d/m/Y H:i', strtotime($domain['created_at'])) : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ isset($domain['updated_at']) ? date('d/m/Y H:i', strtotime($domain['updated_at'])) : 'N/A' }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.domains.records', ['apiId' => $api->id, 'zoneId' => $domain['id']]) }}" 
                                               class="btn btn-info btn-sm me-1">
                                                <i class="fas fa-list me-1"></i> Registros DNS
                                            </a>
                                            <button type="button" class="btn btn-primary btn-sm sync-domain-btn" 
                                                    data-domain-id="{{ $domain['id'] }}" 
                                                    data-domain-name="{{ $domain['name'] }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#syncDomainModal">
                                                <i class="fas fa-sync-alt me-1"></i> Sincronizar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Nenhum domínio encontrado na conta do Cloudflare.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para sincronização de domínio -->
<div class="modal fade" id="syncDomainModal" tabindex="-1" aria-labelledby="syncDomainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.dns-records.sync', ['apiId' => $api->id]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="syncDomainModalLabel">Sincronizar Domínio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Você está prestes a sincronizar o domínio <strong id="syncDomainName"></strong>.</p>
                    <p>Essa ação importará todos os registros DNS deste domínio para o sistema.</p>
                    
                    <input type="hidden" id="syncZoneId" name="zone_id" value="">
                    <input type="hidden" id="syncDomainNameInput" name="domain_name" value="">
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="update_existing" id="updateExisting" value="1" checked>
                        <label class="form-check-label" for="updateExisting">
                            Atualizar registros existentes
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Sincronizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Documento carregado. Inicializando scripts...');
        
        // Sincronização de domínio
        document.querySelectorAll('.sync-domain-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var domainId = this.getAttribute('data-domain-id');
                var domainName = this.getAttribute('data-domain-name');
                
                document.getElementById('syncDomainName').textContent = domainName;
                document.getElementById('syncZoneId').value = domainId;
                document.getElementById('syncDomainNameInput').value = domainName;
            });
        });
        
        // Inicializa tooltips Bootstrap (se Bootstrap estiver disponível)
        if (typeof bootstrap !== 'undefined') {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
        
        // ===== FORMULÁRIOS GHOST =====
        // Configuração de submissão de formulários via AJAX
        var ghostForms = document.querySelectorAll('form[id^="ghost-form-"]');
        console.log('Formulários Ghost encontrados:', ghostForms.length);
        
        // Adicionamos evento de submit para cada formulário Ghost
        ghostForms.forEach(function(form) {
            form.addEventListener('submit', function(event) {
                // Previne o comportamento padrão de submissão
                event.preventDefault();
                
                var formElement = this;
                var domainId = formElement.querySelector('input[name="domain_id"]').value;
                var newGhostValue = formElement.querySelector('input[name="is_ghost"]').value;
                var buttonElement = formElement.querySelector('button[type="submit"]');
                var iconElement = buttonElement.querySelector('i.fa-ghost');
                var statusTextElement = buttonElement.querySelector('.ghost-status-text');
                
                console.log('Formulário Ghost submetido para domínio:', domainId);
                console.log('Novo estado:', newGhostValue);
                
                // Mostramos indicação visual de processamento
                buttonElement.disabled = true;
                if (iconElement) {
                    iconElement.classList.add('fa-spin');
                }
                
                // Preparamos os dados do formulário
                var formData = new FormData(formElement);
                
                // Enviamos o formulário usando fetch API
                fetch(formElement.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Erro na resposta: ' + response.status);
                    }
                    return response.json();
                })
                .then(function(data) {
                    console.log('Resposta do servidor:', data);
                    
                    // Removemos indicação visual de processamento
                    buttonElement.disabled = false;
                    if (iconElement) {
                        iconElement.classList.remove('fa-spin');
                    }
                    
                    if (data.success) {
                        // Obtemos o valor atual e o novo valor
                        var isNowGhost = data.status ? true : false;
                        
                        // Atualizamos a classe do botão e o texto de status
                        if (isNowGhost) {
                            buttonElement.classList.remove('btn-secondary');
                            buttonElement.classList.add('btn-danger');
                            statusTextElement.textContent = 'Ativo';
                        } else {
                            buttonElement.classList.remove('btn-danger');
                            buttonElement.classList.add('btn-secondary');
                            statusTextElement.textContent = 'Inativo';
                        }
                        
                        // Atualizamos o campo hidden no formulário para o próximo clique
                        formElement.querySelector('input[name="is_ghost"]').value = isNowGhost ? '0' : '1';
                        
                        // Mensagem de sucesso
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Status Ghost atualizado com sucesso!');
                        } else {
                            alert('Status Ghost atualizado com sucesso!');
                        }
                    } else {
                        // Mensagem de erro
                        var errorMsg = data.message || 'Erro desconhecido';
                        console.error('Erro ao atualizar status:', errorMsg);
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Erro: ' + errorMsg);
                        } else {
                            alert('Erro: ' + errorMsg);
                        }
                    }
                })
                .catch(function(error) {
                    console.error('Erro na requisição:', error);
                    
                    // Removemos indicação visual de processamento
                    buttonElement.disabled = false;
                    if (iconElement) {
                        iconElement.classList.remove('fa-spin');
                    }
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Erro na comunicação com o servidor: ' + error.message);
                    } else {
                        alert('Erro na comunicação com o servidor: ' + error.message);
                    }
                });
            });
        });
    });
</script>
@endpush
