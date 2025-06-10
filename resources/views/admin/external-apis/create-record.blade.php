@extends('layouts.admin')

@section('title', 'Novo Registro DNS')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-globe"></i> Novo Registro DNS</h1>
        <div>
            <a href="{{ route('admin.external-apis.show', $api->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para API {{ $api->name }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.external-apis.store-record', $api->id) }}" method="POST">
                @csrf
                
                <input type="hidden" name="external_api_id" value="{{ $api->id }}">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="record_type" class="form-label">Tipo de Registro<span class="text-danger">*</span></label>
                            <select class="form-select @error('record_type') is-invalid @enderror" id="record_type" name="record_type" required>
                                <option value="">Selecione um tipo</option>
                                @foreach($recordTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('record_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('record_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" 
                                   name="name" value="{{ old('name') }}" required placeholder="exemplo.com ou subdominio.exemplo.com">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Nome do domínio ou subdomínio para este registro.</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="content" class="form-label">Conteúdo<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('content') is-invalid @enderror" id="content" 
                                   name="content" value="{{ old('content', $clientIpAddress) }}" required>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Para registros A, use um endereço IP. Para CNAME, use um nome de domínio completo.</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="ttl" class="form-label">TTL (segundos)</label>
                            <input type="number" class="form-control @error('ttl') is-invalid @enderror" id="ttl" 
                                   name="ttl" value="{{ old('ttl', 3600) }}" min="60">
                            @error('ttl')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Time To Live em segundos. Padrão: 3600 (1 hora)</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="priority" class="form-label">Prioridade</label>
                            <input type="number" class="form-control @error('priority') is-invalid @enderror" id="priority" 
                                   name="priority" value="{{ old('priority') }}" min="0">
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Prioridade para registros MX. Deixe em branco para outros tipos.</div>
                        </div>
                    </div>
                </div>

                @if($api->type === 'cloudflare')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="zone_id" class="form-label">Zone ID</label>
                                <input type="text" class="form-control" id="zone_id" name="zone_id" 
                                       value="{{ old('zone_id', $api->config['cloudflare_zone_id'] ?? '') }}">
                                <div class="form-text">ID da zona no Cloudflare. Se em branco, será usado o Zone ID padrão configurado na API.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="proxied" name="proxied" value="1" 
                                       {{ old('proxied') ? 'checked' : '' }}>
                                <label class="form-check-label" for="proxied">Proxy através do Cloudflare</label>
                                <div class="form-text">Se marcado, o tráfego será proxeado pelo Cloudflare (CDN, proteção contra DDoS, etc)</div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <hr>
                <h4>Associações (Opcional)</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bank_id" class="form-label">Link Bancário</label>
                            <select class="form-select" id="bank_id" name="bank_id">
                                <option value="">Nenhum</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bank_template_id" class="form-label">Template de Banco</label>
                            <select class="form-select" id="bank_template_id" name="bank_template_id">
                                <option value="">Nenhum</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" {{ old('bank_template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Usuário</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">Nenhum</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->nome }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Criar Registro DNS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Atualizar os campos de acordo com o tipo de registro selecionado
        const recordTypeSelect = document.getElementById('record_type');
        const contentInput = document.getElementById('content');
        const priorityInput = document.getElementById('priority');
        const contentHelp = contentInput.nextElementSibling.nextElementSibling;
        
        recordTypeSelect.addEventListener('change', function() {
            const recordType = this.value;
            
            switch(recordType) {
                case 'A':
                    contentInput.setAttribute('placeholder', '192.168.0.1');
                    contentHelp.textContent = 'Endereço IPv4 para este domínio.';
                    priorityInput.parentElement.style.display = 'none';
                    break;
                case 'CNAME':
                    contentInput.setAttribute('placeholder', 'destino.exemplo.com');
                    contentHelp.textContent = 'Nome de domínio destino (FQDN).';
                    priorityInput.parentElement.style.display = 'none';
                    break;
                case 'MX':
                    contentInput.setAttribute('placeholder', 'mail.exemplo.com');
                    contentHelp.textContent = 'Servidor de email para este domínio.';
                    priorityInput.parentElement.style.display = 'block';
                    break;
                case 'TXT':
                    contentInput.setAttribute('placeholder', 'v=spf1 include:_spf.google.com ~all');
                    contentHelp.textContent = 'Texto livre para o registro.';
                    priorityInput.parentElement.style.display = 'none';
                    break;
                case 'SPF':
                    contentInput.setAttribute('placeholder', 'v=spf1 include:_spf.google.com ~all');
                    contentHelp.textContent = 'Registro SPF para validar servidores de email.';
                    priorityInput.parentElement.style.display = 'none';
                    break;
                case 'DKIM':
                    contentInput.setAttribute('placeholder', 'v=DKIM1; k=rsa; p=MIGfMA0GCSqGSIb...');
                    contentHelp.textContent = 'Chave pública DKIM para autenticação de email.';
                    priorityInput.parentElement.style.display = 'none';
                    break;
                case 'DMARC':
                    contentInput.setAttribute('placeholder', 'v=DMARC1; p=none; rua=mailto:reports@exemplo.com');
                    contentHelp.textContent = 'Política DMARC para autenticação de email.';
                    priorityInput.parentElement.style.display = 'none';
                    break;
                default:
                    contentInput.setAttribute('placeholder', '');
                    contentHelp.textContent = 'Conteúdo do registro.';
                    priorityInput.parentElement.style.display = 'none';
            }
        });
        
        // Dispare o evento change para configurar com base no valor inicial
        if (recordTypeSelect.value) {
            const event = new Event('change');
            recordTypeSelect.dispatchEvent(event);
        }
    });
</script>
@endsection
