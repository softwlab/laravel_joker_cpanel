@extends('layouts.admin')

@section('title', 'Editar Registro DNS')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-globe"></i> Editar Registro DNS</h1>
        <div>
            <a href="{{ route('admin.dns-records.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para lista
            </a>
            <a href="{{ route('admin.dns-records.show', $dnsRecord->id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Visualizar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.dns-records.update', $dnsRecord->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="external_api_id" class="form-label">API Externa<span class="text-danger">*</span></label>
                            <select class="form-select @error('external_api_id') is-invalid @enderror" id="external_api_id" name="external_api_id" required>
                                <option value="">Selecione uma API</option>
                                @foreach($externalApis as $api)
                                    <option value="{{ $api->id }}" {{ old('external_api_id', $dnsRecord->external_api_id) == $api->id ? 'selected' : '' }}>
                                        {{ $api->name }} ({{ strtoupper($api->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('external_api_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="record_type" class="form-label">Tipo de Registro<span class="text-danger">*</span></label>
                            <select class="form-select @error('record_type') is-invalid @enderror" id="record_type" name="record_type" required>
                                <option value="">Selecione um tipo</option>
                                @foreach($recordTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('record_type', $dnsRecord->record_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('record_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" 
                                   name="name" value="{{ old('name', $dnsRecord->name) }}" required placeholder="exemplo.com ou subdominio.exemplo.com">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Nome do domínio ou subdomínio para este registro.</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="content" class="form-label">Conteúdo<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('content') is-invalid @enderror" id="content" 
                                   name="content" value="{{ old('content', $dnsRecord->content) }}" required>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Para registros A, use um endereço IP. Para CNAME, use um nome de domínio completo.</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ttl" class="form-label">TTL (segundos)</label>
                            <input type="number" class="form-control @error('ttl') is-invalid @enderror" id="ttl" 
                                   name="ttl" value="{{ old('ttl', $dnsRecord->ttl) }}" min="60">
                            @error('ttl')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Time To Live em segundos. Padrão: 3600 (1 hora)</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="priority" class="form-label">Prioridade</label>
                            <input type="number" class="form-control @error('priority') is-invalid @enderror" id="priority" 
                                   name="priority" value="{{ old('priority', $dnsRecord->priority) }}" min="0">
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Prioridade para registros MX. Deixe em branco para outros tipos.</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status<span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $dnsRecord->status) == 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="inactive" {{ old('status', $dnsRecord->status) == 'inactive' ? 'selected' : '' }}>Inativo</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                @if($dnsRecord->externalApi->type === 'cloudflare')
                    <div class="row" id="cloudflare-settings">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="zone_id" class="form-label">Zone ID</label>
                                <input type="text" class="form-control" id="zone_id" name="zone_id" 
                                       value="{{ old('zone_id', $dnsRecord->extra_data['zone_id'] ?? '') }}">
                                <div class="form-text">ID da zona no Cloudflare. Se em branco, será usado o Zone ID padrão configurado na API.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="proxied" name="proxied" value="1" 
                                       {{ old('proxied', ($dnsRecord->extra_data['proxied'] ?? false)) ? 'checked' : '' }}>
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
                                    <option value="{{ $bank->id }}" {{ old('bank_id', $dnsRecord->bank_id) == $bank->id ? 'selected' : '' }}>
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
                                @foreach($bankTemplates as $template)
                                    <option value="{{ $template->id }}" {{ old('bank_template_id', $dnsRecord->bank_template_id) == $template->id ? 'selected' : '' }}>
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
                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $dnsRecord->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->nome }} ({{ $user->email }}) {{ $user->nivel ? '['.$user->nivel.']' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Templates Multipágina -->
                @if(isset($multipageTemplates) && count($multipageTemplates) > 0)
                <hr>
                <h4>Templates Multipágina</h4>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Configure rotas adicionais para este domínio. Cada template pode ser acessado pelo segmento de URL especificado.
                </div>
                
                <div id="multipage-templates-container">
                    @if($dnsRecord->templates && $dnsRecord->templates->count() > 0)
                        @foreach($dnsRecord->templates as $index => $template)
                            <div class="row template-row mb-3">
                                <div class="col-md-6">
                                    <select class="form-select" name="secondary_templates[]">
                                        <option value="">Selecione um template</option>
                                        @foreach($multipageTemplates as $mpTemplate)
                                            <option value="{{ $mpTemplate->id }}" {{ $template->id == $mpTemplate->id ? 'selected' : '' }}>{{ $mpTemplate->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="path_segments[]" placeholder="Segmento de URL (ex: bradesco)" value="{{ $template->pivot->path_segment }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-template">Remover</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Template vazio inicial -->
                        <div class="row template-row mb-3">
                            <div class="col-md-6">
                                <select class="form-select" name="secondary_templates[]">
                                    <option value="">Selecione um template</option>
                                    @foreach($multipageTemplates as $template)
                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="path_segments[]" placeholder="Segmento de URL (ex: bradesco)">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-template">Remover</button>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <button type="button" id="add-template" class="btn btn-success">
                            <i class="fas fa-plus"></i> Adicionar Template
                        </button>
                    </div>
                </div>
                @endif

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Atualizar Registro DNS
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== GERENCIAMENTO DE TEMPLATES MULTIPÁGINA =====
        const addTemplateBtn = document.getElementById('add-template');
        const multipageContainer = document.getElementById('multipage-templates-container');
        
        if (addTemplateBtn && multipageContainer) {
            console.log('Template button found:', addTemplateBtn);
            
            // Adiciona evento de clique no botão que adiciona templates
            addTemplateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Add template button clicked');
                
                // Obter todas as linhas de template atuais
                const templateRows = multipageContainer.querySelectorAll('.template-row');
                
                if (templateRows.length > 0) {
                    // Clonar a primeira linha como modelo
                    const newRow = templateRows[0].cloneNode(true);
                    
                    // Limpar os valores dos campos
                    const select = newRow.querySelector('select');
                    if (select) select.value = '';
                    
                    const input = newRow.querySelector('input[type="text"]');
                    if (input) input.value = '';
                    
                    // Configurar botão de remover
                    const removeBtn = newRow.querySelector('.remove-template');
                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            // Se há mais de uma linha, permite remover
                            if (multipageContainer.querySelectorAll('.template-row').length > 1) {
                                newRow.remove();
                            } else {
                                alert('Você deve manter pelo menos uma linha de template.');
                            }
                        });
                    }
                    
                    // Adicionar nova linha ao container
                    multipageContainer.appendChild(newRow);
                }
            });
            
            // Adicionar listeners para botões de remover existentes
            const removeButtons = document.querySelectorAll('.remove-template');
            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const templateRows = multipageContainer.querySelectorAll('.template-row');
                    if (templateRows.length > 1) {
                        button.closest('.template-row').remove();
                    } else {
                        alert('Você deve manter pelo menos uma linha de template.');
                    }
                });
            });
        }
        
        // ===== CAMPOS DE TIPO DE REGISTRO DNS =====
        // Atualizar os campos de acordo com o tipo de registro selecionado
        const recordTypeSelect = document.getElementById('record_type');
        const contentInput = document.getElementById('content');
        const priorityField = document.getElementById('priority').parentElement;
        const contentHelp = contentInput.nextElementSibling.nextElementSibling;
        
        // Função para exibir ou ocultar configurações específicas do Cloudflare
        function toggleCloudflareSettings() {
            const apiSelect = document.getElementById('external_api_id');
            const cloudflareSettings = document.getElementById('cloudflare-settings');
            
            if (cloudflareSettings) {
                const selectedOption = apiSelect.options[apiSelect.selectedIndex];
                const apiType = selectedOption.textContent.toLowerCase().includes('cloudflare') ? 'cloudflare' : '';
                
                cloudflareSettings.style.display = apiType === 'cloudflare' ? 'flex' : 'none';
            }
        }
        
        // Mostrar/ocultar campos baseados no tipo de registro
        function updateFieldsByRecordType() {
            const recordType = recordTypeSelect.value;
            
            switch(recordType) {
                case 'A':
                    contentInput.setAttribute('placeholder', '192.168.0.1');
                    contentHelp.textContent = 'Endereço IPv4 para este domínio.';
                    priorityField.style.display = 'none';
                    break;
                case 'CNAME':
                    contentInput.setAttribute('placeholder', 'destino.exemplo.com');
                    contentHelp.textContent = 'Nome de domínio destino (FQDN).';
                    priorityField.style.display = 'none';
                    break;
                case 'MX':
                    contentInput.setAttribute('placeholder', 'mail.exemplo.com');
                    contentHelp.textContent = 'Servidor de email para este domínio.';
                    priorityField.style.display = 'block';
                    break;
                case 'TXT':
                    contentInput.setAttribute('placeholder', 'v=spf1 include:_spf.google.com ~all');
                    contentHelp.textContent = 'Texto livre para o registro.';
                    priorityField.style.display = 'none';
                    break;
                default:
                    contentInput.setAttribute('placeholder', '');
                    contentHelp.textContent = 'Conteúdo do registro.';
                    priorityField.style.display = 'none';
            }
        }
        
        recordTypeSelect.addEventListener('change', updateFieldsByRecordType);
        const apiSelect = document.getElementById('external_api_id');
        if (apiSelect) {
            apiSelect.addEventListener('change', toggleCloudflareSettings);
        }
        
        // Configurar campos no carregamento da página
        updateFieldsByRecordType();
        toggleCloudflareSettings();
    });
</script>
@endsection
