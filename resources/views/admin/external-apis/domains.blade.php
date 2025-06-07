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
                            <h4 class="mb-0">{{ count(array_filter($domains, function($d) { return $d['status'] == 'active'; })) }}</h4>
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
                            <h4 class="mb-0">{{ count(array_filter($domains, function($d) { return $d['status'] != 'active'; })) }}</h4>
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
            <div>
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
            
            @if($error)
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
                                    <td>{{ $domain['id'] }}</td>
                                    <td>{{ $domain['name'] }}</td>
                                    <td>
                                        @if($domain['status'] == 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-warning">{{ $domain['status'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($domain['nameservers']))
                                            <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#nameservers-{{ $loop->index }}" aria-expanded="false">
                                                Mostrar ({{ count($domain['nameservers']) }})
                                            </button>
                                            <div class="collapse mt-2" id="nameservers-{{ $loop->index }}">
                                                <div class="card card-body">
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($domain['nameservers'] as $ns)
                                                            <li>{{ $ns }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Não disponível</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $domain['records_count'] ?? 'N/A' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                            <input class="form-check-input ghost-toggle" type="checkbox" role="switch" 
                                                id="ghost-{{ $domain['id'] }}" 
                                                data-domain-id="{{ $domain['id'] }}" 
                                                {{ isset($domain['is_ghost']) && $domain['is_ghost'] ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2" for="ghost-{{ $domain['id'] }}">
                                                <i class="fas fa-ghost {{ isset($domain['is_ghost']) && $domain['is_ghost'] ? 'text-danger' : 'text-secondary' }}"></i>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        {{ isset($domain['created_at']) ? date('d/m/Y H:i', strtotime($domain['created_at'])) : date('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        {{ isset($domain['updated_at']) ? date('d/m/Y H:i', strtotime($domain['updated_at'])) : date('d/m/Y H:i') }}
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
    $(document).ready(function() {
        // Sincronização de domínio
        $('.sync-domain-btn').on('click', function() {
            var domainId = $(this).data('domain-id');
            var domainName = $(this).data('domain-name');
            
            $('#syncDomainName').text(domainName);
            $('#syncZoneId').val(domainId);
        });
        
        // Toggle de status Ghost/Phishing
        $('.ghost-toggle').on('change', function() {
            const domainId = $(this).data('domain-id');
            const isGhost = $(this).prop('checked');
            const iconElement = $(this).siblings('label').find('i.fa-ghost');
            
            // Efeito visual imediato
            if (isGhost) {
                iconElement.removeClass('text-secondary').addClass('text-danger');
            } else {
                iconElement.removeClass('text-danger').addClass('text-secondary');
            }
            
            // Enviar status para o backend via AJAX
            $.ajax({
                url: '{{ route("admin.external-apis.update-ghost-status") }}',
                method: 'POST',
                data: {
                    domain_id: domainId,
                    is_ghost: isGhost ? 1 : 0,
                    api_id: '{{ $api->id }}',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Mostrar notificação de sucesso
                        toastr.success('Status Ghost atualizado com sucesso!');
                    } else {
                        toastr.error('Erro ao atualizar status Ghost: ' + response.message);
                        // Reverter o toggle em caso de erro
                        $('#ghost-' + domainId).prop('checked', !isGhost).trigger('change');
                    }
                },
                error: function() {
                    toastr.error('Erro na comunicação com o servidor');
                    // Reverter o toggle em caso de erro
                    $('#ghost-' + domainId).prop('checked', !isGhost).trigger('change');
                }
            });
        });
    });
</script>
@endpush
