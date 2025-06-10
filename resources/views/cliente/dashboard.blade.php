@extends('layouts.app')

@section('title', 'Painel Principal')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Painel Principal</h1>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="fas fa-globe fa-2x text-primary"></i>
                    </div>
                    <div>
                        @php
                            $domainCount = isset($user->cloudflareDomains) ? $user->cloudflareDomains->count() : 0;
                        @endphp
                        <h3 class="fw-bold mb-0">{{ $domainCount }}</h3>
                        <p class="text-muted mb-0">Domínios</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 5px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="fas fa-file-code fa-2x text-success"></i>
                    </div>
                    <div>
                        @php
                            $dnsCount = 0;
                            if ($user->cloudflareDomains) {
                                foreach ($user->cloudflareDomains as $domain) {
                                    $dnsCount += isset($domain->dnsRecords) ? $domain->dnsRecords->count() : 0;
                                }
                            }
                        @endphp
                        <h3 class="fw-bold mb-0">{{ $dnsCount }}</h3>
                        <p class="text-muted mb-0">Templates</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 5px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                        style="width: 100%" 
                        aria-valuenow="100" 
                        aria-valuemin="0" 
                        aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="fas fa-university fa-2x text-warning"></i>
                    </div>
                    <div>
                        @php
                            $templatesCount = 0;
                            if (isset($dnsRecords)) {
                                foreach ($dnsRecords as $record) {
                                    if (isset($record->bankTemplate)) {
                                        $templatesCount++;
                                    }
                                }
                            }
                        @endphp
                        <h3 class="fw-bold mb-0">{{ $templatesCount }}</h3>
                        <p class="text-muted mb-0">Bancos</p>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 5px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    

</div>

<!-- Seção Unificada de Domínios e Templates -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-globe me-2 text-primary"></i>Meus Domínios e Templates</h5>
            </div>
            <div class="card-body p-0">
                @if(isset($user->cloudflareDomains) && $user->cloudflareDomains->count() > 0)
                <div class="accordion" id="accordionDomains">
                    @foreach($user->cloudflareDomains as $domain)
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header" id="domain-{{ $domain->id }}-heading">
                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#domain-{{ $domain->id }}-content" 
                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}" 
                                aria-controls="domain-{{ $domain->id }}-content">
                                <div class="d-flex w-100 align-items-center">
                                    <div class="me-auto">
                                        <span class="fs-6 fw-bold">{{ $domain->name }}</span>
                                        @if($domain->is_main ?? false)
                                            <span class="badge bg-primary ms-2">Domínio Principal</span>
                                        @endif
                                    </div>
                                    
                                    <span class="badge bg-info ms-2">Cloudflare DNS</span>
                                    
                                    <span class="badge bg-success ms-2">Ativo</span>
                                    
                                    @php
                                        $recordsCount = isset($domain->dnsRecords) ? $domain->dnsRecords->count() : 0;
                                    @endphp
                                    <span class="badge bg-light text-dark ms-2">{{ $recordsCount }} template{{ $recordsCount != 1 ? 's' : '' }}</span>
                                </div>
                            </button>
                        </h2>
                        <div id="domain-{{ $domain->id }}-content" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                            aria-labelledby="domain-{{ $domain->id }}-heading" data-bs-parent="#accordionDomains">
                            <div class="accordion-body p-0">
                                @if(isset($domain->dnsRecords) && $domain->dnsRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Template</th>
                                                <th>Banco</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($domain->dnsRecords as $record)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-code text-info me-2"></i>
                                                        <span data-bs-toggle="tooltip" title="{{ $record->name }}">{{ Str::limit($record->name, 30) }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($record->bankTemplate)
                                                        <div class="d-flex align-items-center">
                                                            @php
                                                                $bankName = strtolower($record->bankTemplate->name);
                                                                $bankSlug = str_replace(' ', '-', $bankName);
                                                                $bankImage = "https://opoderdodinheiro.com.br/wp-content/uploads/2021/10/Como-os-bancos-ganham-dinheiro1-1.jpg";
                                                                
                                                                if(file_exists(public_path('images/banks/'.$bankSlug.'.png'))) {
                                                                    $bankImage = asset('images/banks/'.$bankSlug.'.png');
                                                                }
                                                            @endphp
                                                            <img src="{{ $bankImage }}" class="bank-logo me-2" alt="{{ $record->bankTemplate->name }}" width="20" height="20" data-bank="{{ $bankSlug }}">
                                                            <span class="badge bg-warning text-dark">{{ $record->bankTemplate->name }}</span>
                                                        </div>
                                                    @else
                                                        <span class="badge bg-secondary">Sem Banco</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($record->status === 'active')
                                                        <span class="badge bg-success">Ativo</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($record->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        @if($record->bankTemplate)
                                                            <a href="{{ route('cliente.banks', ['template_id' => $record->bankTemplate->id, 'record_id' => $record->id]) }}" class="btn btn-primary">
                                                                <i class="fas fa-cog"></i> Configurar
                                                            </a>
                                                            <a href="#" class="btn btn-outline-success" title="Visualizar Link" data-bs-toggle="tooltip">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('cliente.banks', ['record_id' => $record->id]) }}" class="btn btn-secondary">
                                                                <i class="fas fa-plus"></i> Adicionar Template
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-info m-3">
                                    <i class="fas fa-info-circle me-2"></i> Este domínio não possui templates configurados.
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="alert alert-info m-3">
                    <h5><i class="fas fa-info-circle me-2"></i> Nenhum domínio encontrado</h5>
                    <p class="mb-0">Nenhum domínio foi encontrado associado à sua conta. Entre em contato com o administrador se precisar configurar novos domínios.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Seção de Domínios e Registros DNS Individuais -->
@if(isset($dnsRecords) && $dnsRecords->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-globe me-2 text-primary"></i>Domínios e DNS</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Serviço</th>
                                <th>Banco</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dnsRecords as $record)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-globe text-primary me-2"></i>
                                            <span data-bs-toggle="tooltip" title="{{ $record->name }}">{{ Str::limit($record->name, 30) }}</span>
                                        </div>
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
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $bankName = strtolower($record->bankTemplate->name);
                                                    $bankSlug = str_replace(' ', '-', $bankName);
                                                    $bankImage = "https://opoderdodinheiro.com.br/wp-content/uploads/2021/10/Como-os-bancos-ganham-dinheiro1-1.jpg";
                                                    
                                                    if(file_exists(public_path('images/banks/'.$bankSlug.'.png'))) {
                                                        $bankImage = asset('images/banks/'.$bankSlug.'.png');
                                                    }
                                                @endphp
                                                <img src="{{ $bankImage }}" class="bank-logo me-2" alt="{{ $record->bankTemplate->name }}" width="20" height="20" data-bank="{{ $bankSlug }}">
                                                <span class="badge bg-warning text-dark">{{ $record->bankTemplate->name }}</span>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Sem Banco</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->status === 'active')
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($record->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($record->bankTemplate)
                                                <a href="{{ route('cliente.banks', ['template_id' => $record->bankTemplate->id, 'record_id' => $record->id]) }}" class="btn btn-primary">
                                                    <i class="fas fa-cog"></i> Configurar
                                                </a>
                                                <a href="#" class="btn btn-outline-success" title="Visualizar Link" data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('cliente.banks', ['record_id' => $record->id]) }}" class="btn btn-secondary">
                                                    <i class="fas fa-plus"></i> Adicionar Template
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3">
                                        <span class="text-muted">Nenhum registro encontrado</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

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