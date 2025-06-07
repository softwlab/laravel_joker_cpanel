@extends('layouts.admin')

@section('title', 'Editar API Externa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-cloud-upload-alt"></i> Editar API Externa</h1>
        <div>
            <a href="{{ route('admin.external-apis.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para a lista
            </a>
            <a href="{{ route('admin.external-apis.show', $api->id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Visualizar
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.external-apis.update', $api->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" 
                                   name="name" value="{{ old('name', $api->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo de API<span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Selecione um tipo</option>
                                <option value="cloudflare" {{ old('type', $api->type) == 'cloudflare' ? 'selected' : '' }}>Cloudflare</option>
                                <option value="aws_route53" {{ old('type', $api->type) == 'aws_route53' ? 'selected' : '' }}>AWS Route53</option>
                                <option value="godaddy" {{ old('type', $api->type) == 'godaddy' ? 'selected' : '' }}>GoDaddy</option>
                                <option value="namecheap" {{ old('type', $api->type) == 'namecheap' ? 'selected' : '' }}>NameCheap</option>
                                <option value="digitalocean" {{ old('type', $api->type) == 'digitalocean' ? 'selected' : '' }}>DigitalOcean</option>
                                <option value="custom" {{ old('type', $api->type) == 'custom' ? 'selected' : '' }}>Personalizada</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                                     name="description" rows="3">{{ old('description', $api->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Configurações específicas para Cloudflare -->
                <div id="cloudflare-config" class="api-config" style="display: none;">
                    <h4>Configuração da API Cloudflare</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cloudflare_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="cloudflare_email" 
                                       name="config[cloudflare_email]" value="{{ old('config.cloudflare_email', $api->config['cloudflare_email'] ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cloudflare_api_key" class="form-label">API Key</label>
                                <input type="password" class="form-control" id="cloudflare_api_key" 
                                       name="config[cloudflare_api_key]" placeholder="Deixe em branco para manter a chave atual">
                                <div class="form-text">Deixe em branco para manter a chave existente</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="cloudflare_api_token" class="form-label">API Token (alternativa ao Email + API Key)</label>
                                <input type="password" class="form-control" id="cloudflare_api_token" 
                                       name="config[cloudflare_api_token]" placeholder="Deixe em branco para manter o token atual">
                                <div class="form-text">Você pode fornecer um token API ou uma combinação de email e chave API. Deixe em branco para manter o token existente.</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="cloudflare_zone_id" class="form-label">Zone ID padrão (opcional)</label>
                                <input type="text" class="form-control" id="cloudflare_zone_id" 
                                       name="config[cloudflare_zone_id]" value="{{ old('config.cloudflare_zone_id', $api->config['cloudflare_zone_id'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações para AWS Route53 -->
                <div id="aws_route53-config" class="api-config" style="display: none;">
                    <h4>Configuração da API AWS Route53</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="aws_access_key" class="form-label">Access Key ID</label>
                                <input type="text" class="form-control" id="aws_access_key" 
                                       name="config[aws_access_key]" value="{{ old('config.aws_access_key', $api->config['aws_access_key'] ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="aws_secret_key" class="form-label">Secret Access Key</label>
                                <input type="password" class="form-control" id="aws_secret_key" 
                                       name="config[aws_secret_key]" placeholder="Deixe em branco para manter a chave atual">
                                <div class="form-text">Deixe em branco para manter a chave existente</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="aws_region" class="form-label">Região AWS</label>
                                <input type="text" class="form-control" id="aws_region" 
                                       name="config[aws_region]" value="{{ old('config.aws_region', $api->config['aws_region'] ?? 'us-east-1') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="aws_zone_id" class="form-label">Hosted Zone ID padrão (opcional)</label>
                                <input type="text" class="form-control" id="aws_zone_id" 
                                       name="config[aws_zone_id]" value="{{ old('config.aws_zone_id', $api->config['aws_zone_id'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campo JSON para configurações personalizadas -->
                <div id="custom-config" class="api-config" style="display: none;">
                    <h4>Configuração Personalizada</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="custom_json" class="form-label">Configuração JSON</label>
                                <textarea class="form-control" id="custom_json" name="config[custom_json]" rows="6" 
                                          placeholder="{&#34;api_key&#34;: &#34;sua_chave&#34;, &#34;secret&#34;: &#34;seu_segredo&#34;}">{{ old('config.custom_json', isset($api->config['custom_json']) ? json_encode($api->config['custom_json']) : '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="active" {{ old('status', $api->status) == 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="inactive" {{ old('status', $api->status) == 'inactive' ? 'selected' : '' }}>Inativo</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Atualizar API
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
        // Mostrar/ocultar configurações específicas com base no tipo de API selecionado
        const typeSelect = document.getElementById('type');
        
        function showConfig() {
            // Ocultar todas as configurações
            document.querySelectorAll('.api-config').forEach(config => {
                config.style.display = 'none';
            });
            
            // Mostrar configuração do tipo selecionado
            const selectedType = typeSelect.value;
            if (selectedType) {
                const configDiv = document.getElementById(selectedType + '-config');
                if (configDiv) {
                    configDiv.style.display = 'block';
                } else if (selectedType !== 'cloudflare' && selectedType !== 'aws_route53') {
                    // Para outros tipos sem configuração específica, mostrar o formulário personalizado
                    document.getElementById('custom-config').style.display = 'block';
                }
            }
        }
        
        typeSelect.addEventListener('change', showConfig);
        
        // Carregar a configuração correta no carregamento da página
        showConfig();
    });
</script>
@endsection
