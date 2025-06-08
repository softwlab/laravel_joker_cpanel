@extends('layouts.app')

@section('title', 'Painel Principal')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Painel Principal</h1>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ is_object($banks) ? $banks->count() : count($banks ?? []) }}</h4>
                        <p class="mb-0">Total de Links Bancários</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-university fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ is_object($banks) ? $banks->where('active', true)->count() : 0 }}</h4>
                        <p class="mb-0">Links Bancários Ativos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ is_object($banks) ? $banks->where('active', false)->count() : 0 }}</h4>
                        <p class="mb-0">Links Bancários Inativos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-pause-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ is_object($linkGroups) ? $linkGroups->count() : count($linkGroups ?? []) }}</h4>
                        <p class="mb-0">Grupos Organizados</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-link fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(is_object($banks) ? $banks->count() > 0 : count($banks ?? []) > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Meus Links Bancários</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover border-bottom">
                        <thead>
                            <tr>
                                <th>Identificador</th>
                                <th>Nome do Link</th>
                                <th>Estado</th>
                                <th>Instituição Bancária</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banks as $bank)
                            <tr>
                                <td>
                                    @php
                                        // Extraímos apenas o número do final do slug para exibição mais compacta
                                        $displayId = $bank->slug ? preg_replace('/^.*-(\d+)$/', 'ID-$1', $bank->slug) : $bank->id;
                                    @endphp
                                    <span class="badge bg-light text-dark border" title="{{ $bank->slug ?? $bank->id }}">
                                        <code class="fs-6">{{ $displayId }}</code>
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $bank->name }}</strong>
                                </td>
                                <td>
                                    @if($bank->active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-danger">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($bank->template)
                                        <div class="d-flex align-items-center">
                                            <img 
                                                src="https://opoderdodinheiro.com.br/wp-content/uploads/2021/10/Como-os-bancos-ganham-dinheiro1-1.jpg" 
                                                class="me-2" 
                                                alt="{{ $bank->template->name }}" 
                                                width="24" height="24">
                                            <span>{{ $bank->template->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Sem instituição definida</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('cliente.banks.show', $bank->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-success" 
                                           title="Copiar link" data-bs-toggle="tooltip">
                                            <i class="fas fa-copy"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary" 
                                           title="Adicionar a grupo" data-bs-toggle="tooltip">
                                            <i class="fas fa-folder-plus"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Você ainda não possui links bancários associados à sua conta.
            Todos os links são criados e gerenciados por um administrador do sistema.
        </div>
    </div>
</div>
@endif

<!-- Seção Unificada de Domínios e Páginas -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Minhas Páginas e Domínios</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover border-bottom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Domínio/Página</th>
                                <th>Serviço</th>
                                <th>Template Associado</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Registros DNS como páginas individuais -->
                            @if(isset($dnsRecords) && $dnsRecords->count() > 0)
                                @foreach($dnsRecords as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>
                                        <span data-bs-toggle="tooltip" title="{{ $record->name }}">
                                            {{ Str::limit($record->name, 30) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($record->externalApi)
                                            <span class="badge bg-info" data-bs-toggle="tooltip" title="{{ $record->externalApi->name }}">
                                                {{ Str::limit($record->externalApi->name, 15) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Serviço Web</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->bankTemplate)
                                            <span class="badge bg-warning" data-bs-toggle="tooltip" title="{{ $record->bankTemplate->name }}">
                                                {{ $record->bankTemplate->name }}
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
                                        @if($record->bankTemplate)
                                            <a href="{{ route('cliente.banks', ['template_id' => $record->bankTemplate->id, 'record_id' => $record->id]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-cog"></i> Configurar Página
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fas fa-cog"></i> Sem Template
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @endif

                            <!-- Domínios Cloudflare como categorias principais -->
                            @if(isset($user->cloudflareDomains) && $user->cloudflareDomains->count() > 0)
                                @foreach($user->cloudflareDomains as $domain)
                                <tr class="table-primary">
                                    <td>-</td> <!-- ID não aplicável para domínio principal -->
                                    <td>
                                        <strong>{{ $domain->name }}</strong>
                                        <span class="badge bg-primary ms-2">Domínio Principal</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">Cloudflare</span>
                                    </td>
                                    <td>

                                    @php
                                        $templates = [];
                                        $banks = [];
                                        
                                        // Coletando templates e bancos associados a este domínio
                                        // A modificação no controller já garante que domain->dnsRecords contém apenas registros do usuário atual
                                        if (isset($domain->dnsRecords) && is_object($domain->dnsRecords) && $domain->dnsRecords->count() > 0) {
                                            foreach ($domain->dnsRecords as $record) {
                                                // Verifica se o registro pertence ao usuário atual (controlador já filtra, mas verificamos novamente)
                                                if ($record->bankTemplate && !in_array($record->bankTemplate->id, array_column($templates, 'id'))) {
                                                    $templates[] = [
                                                        'id' => $record->bankTemplate->id,
                                                        'name' => $record->bankTemplate->name
                                                    ];
                                                }
                                                
                                                if ($record->bank && $record->bank->template && !in_array($record->bank->template->id, array_column($templates, 'id'))) {
                                                    $templates[] = [
                                                        'id' => $record->bank->template->id,
                                                        'name' => $record->bank->template->name
                                                    ];
                                                }
                                                
                                                if ($record->bank && !in_array($record->bank->id, array_column($banks, 'id'))) {
                                                    $banks[] = [
                                                        'id' => $record->bank->id,
                                                        'name' => $record->bank->name
                                                    ];
                                                }
                                            }
                                        }
                                    @endphp
                                    
                                    @if(count($templates) > 0)
                                        @foreach($templates as $template)
                                            <span class="badge bg-primary me-1">Template: {{ $template['name'] }}</span>
                                        @endforeach
                                    @endif
                                    
                                    @if(count($banks) > 0)
                                        @foreach($banks as $bank)
                                            <span class="badge bg-info me-1">Link: {{ $bank['name'] }}</span>
                                        @endforeach
                                    @endif
                                    
                                    @if(count($templates) == 0 && count($banks) == 0)
                                        <span class="badge bg-secondary">Nenhum template associado</span>
                                    @endif
                                    </td>
                                    <td>
                                        @if($domain->pivot->status === 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @elseif($domain->pivot->status === 'paused')
                                            <span class="badge bg-warning">Pausado</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($domain->pivot->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(count($templates) > 0)
                                            <a href="{{ route('cliente.banks', ['domain_id' => $domain->id]) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-cog"></i> Configurar Página
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fas fa-cog"></i> Sem Template
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mapeamento de logos dos principais bancos brasileiros
    const bankLogos = {
        // Bancos tradicionais
        'banco-do-brasil': 'https://logodownload.org/wp-content/uploads/2014/05/banco-do-brasil-logo-0-2.png',
        'caixa-economica-federal': 'https://logodownload.org/wp-content/uploads/2014/05/caixa-logo-1-2.png',
        'itau': 'https://logodownload.org/wp-content/uploads/2014/05/itau-logo-1-1.png',
        'bradesco': 'https://logodownload.org/wp-content/uploads/2014/02/bradesco-logo-4.png',
        'santander': 'https://logodownload.org/wp-content/uploads/2016/10/Santander-logo-3.png',
        
        // Bancos digitais
        'nubank': 'https://logodownload.org/wp-content/uploads/2019/08/nubank-logo-0-2.png',
        'inter': 'https://logodownload.org/wp-content/uploads/2019/11/banco-inter-logo-0-1.png',
        'original': 'https://logodownload.org/wp-content/uploads/2020/02/banco-original-logo-0-2.png',
        'c6': 'https://logodownload.org/wp-content/uploads/2020/11/c6-bank-logo-0-1.png',
        'mercado-pago': 'https://logodownload.org/wp-content/uploads/2018/01/mercado-pago-logo-1.png',
        'picpay': 'https://logodownload.org/wp-content/uploads/2018/11/picpay-logo-1-1.png',
        
        // Exchanges
        'binance': 'https://logodownload.org/wp-content/uploads/2021/04/binance-logo-0-1.png',
        
        // Outros bancos
        'sicoob': 'https://www.sicoob.com.br/documents/44162/146306/sicoob.svg',
        'sicredi': 'https://www.sicredi.com.br/html/portal/assets/themes/sicredi-default/images/logo.svg',
        'banrisul': 'https://www.banrisul.com.br/img/logos/logo_banrisul.svg',
        'bmg': 'https://www.bancobmg.com.br/wp-content/themes/bmg/assets/dist/images/logo-bmg.svg',
        'pan': 'https://www.bancopan.com.br/gf-pan/img/logo/logo-pan.svg',
        'will-bank': 'https://www.willbank.com.br/-/media/Images/Willbank/logo/logo-will.svg',
        
        // Email e outros serviços
        'gmail': 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Gmail_icon_%282020%29.svg/1024px-Gmail_icon_%282020%29.svg.png',
        'outlook': 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/df/Microsoft_Office_Outlook_%282018%E2%80%93present%29.svg/512px-Microsoft_Office_Outlook_%282018%E2%80%93present%29.svg.png',
        
        // Email genérico - adicionamos várias variações para melhorar a correspondência
        'e-mail-generico': 'https://cdn-icons-png.flaticon.com/512/561/561127.png',
        'email': 'https://cdn-icons-png.flaticon.com/512/561/561127.png',
        'e-mail': 'https://cdn-icons-png.flaticon.com/512/561/561127.png',
        'email-generico': 'https://cdn-icons-png.flaticon.com/512/561/561127.png',
        
        // Logo padrão
        'default': 'https://opoderdodinheiro.com.br/wp-content/uploads/2021/10/Como-os-bancos-ganham-dinheiro1-1.jpg'
    };
    
    /**
     * Obtém a URL do logo de um banco pelo seu slug ou nome
     * @param {string} bankNameOrSlug - O slug ou nome do banco
     * @returns {string} URL do logo do banco, ou logo padrão se não encontrado
     */
    function getBankLogo(bankNameOrSlug) {
        if (!bankNameOrSlug) return bankLogos['default'];
        
        // Normaliza o nome/slug para comparação (lowercase e remove acentos)
        const normalized = bankNameOrSlug.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, '-');
        
        // Verifica correspondências exatas
        if (bankLogos[normalized]) {
            return bankLogos[normalized];
        }
        
        // Verifica correspondências parciais
        for (const key in bankLogos) {
            if (normalized.includes(key) || key.includes(normalized)) {
                return bankLogos[key];
            }
        }
        
        // Retorna logo padrão se nenhuma correspondência for encontrada
        return bankLogos['default'];
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Carrega os ícones dos bancos
        document.querySelectorAll('.bank-logo').forEach(function(img) {
            var bankSlug = img.getAttribute('data-bank');
            
            console.log('Tentando carregar ícone para:', bankSlug);
            
            // Abordagem direta: definir imagem imediatamente para evitar atraso
            if (bankSlug === 'email' || bankSlug === 'e-mail-generico' || bankSlug.includes('email') || bankSlug.includes('mail')) {
                console.log('Detectado email, usando ícone genérico de email');
                img.src = 'https://cdn-icons-png.flaticon.com/512/561/561127.png';
            } else {
                try {
                    // Verifica se a função getBankLogo existe
                    if (typeof getBankLogo === 'function') {
                        const logoUrl = getBankLogo(bankSlug);
                        console.log('URL do logo encontrada:', logoUrl, 'para slug:', bankSlug);
                        img.src = logoUrl;
                    } else {
                        // Fallback direto caso a função não exista
                        console.error('Função getBankLogo não encontrada!');
                        img.src = 'https://opoderdodinheiro.com.br/wp-content/uploads/2021/10/Como-os-bancos-ganham-dinheiro1-1.jpg';
                    }
                } catch (error) {
                    console.error('Erro ao carregar ícone:', error);
                    img.src = 'https://opoderdodinheiro.com.br/wp-content/uploads/2021/10/Como-os-bancos-ganham-dinheiro1-1.jpg';
                }
            }
            
            // Se a imagem falhar ao carregar, usa o ícone padrão
            img.onerror = function() {
                console.log('Imagem falhou ao carregar:', this.src);
                this.src = 'https://opoderdodinheiro.com.br/wp-content/uploads/2021/10/Como-os-bancos-ganham-dinheiro1-1.jpg';
                this.onerror = null; // Evita loops infinitos
            };
        });
    });
</script>
@endpush