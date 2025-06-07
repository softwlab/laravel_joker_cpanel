@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Configuração da API {{ $api->name }}</h5>
                    <a href="{{ route('admin.external-apis.show', $api->id) }}" class="btn btn-sm btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
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
                    
                    @if($api->type == 'cloudflare')
                        <form action="{{ route('admin.external-apis.update-config', $api->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label>Método de Autenticação</label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="auth_method_api_key" name="auth_method" value="api_key" class="custom-control-input" 
                                        {{ (!isset($api->config['auth_method']) || $api->config['auth_method'] == 'api_key') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="auth_method_api_key">API Key + Email (método legado)</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="auth_method_token" name="auth_method" value="token" class="custom-control-input"
                                        {{ (isset($api->config['auth_method']) && $api->config['auth_method'] == 'token') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="auth_method_token">API Token (recomendado)</label>
                                </div>
                            </div>
                            
                            <div id="api_key_fields" class="auth-method-fields">
                                <div class="form-group">
                                    <label for="cloudflare_email">Email da conta Cloudflare</label>
                                    <input type="email" class="form-control" id="cloudflare_email" name="cloudflare_email" 
                                        value="{{ $api->config['cloudflare_email'] ?? '' }}" required>
                                    <small class="form-text text-muted">Email associado à sua conta Cloudflare.</small>
                                    @error('cloudflare_email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="cloudflare_api_key">API Key</label>
                                    <input type="password" class="form-control" id="cloudflare_api_key" name="cloudflare_api_key" 
                                        value="{{ $api->config['cloudflare_api_key'] ?? '' }}" required>
                                    <small class="form-text text-muted">
                                        Chave de API Global. Pode ser obtida em <a href="https://dash.cloudflare.com/profile/api-tokens" target="_blank">dash.cloudflare.com/profile/api-tokens</a> na seção "API Keys".
                                    </small>
                                    @error('cloudflare_api_key')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="cloudflare_zone_id"><strong class="text-danger">* Zone ID (Domínio Padrão)</strong></label>
                                    <input type="text" class="form-control border border-danger" id="cloudflare_zone_id" name="cloudflare_zone_id" 
                                        value="{{ $api->config['cloudflare_zone_id'] ?? '' }}" required>
                                    <small class="form-text text-danger font-weight-bold">
                                        ATENÇÃO: Este campo é OBRIGATÓRIO para sincronização de registros DNS.
                                    </small>
                                    <small class="form-text text-muted">
                                        ID da zona/domínio que será usado para sincronizar registros DNS. Pode ser encontrado na URL do painel Cloudflare 
                                        (<code>https://dash.cloudflare.com/*/ZONE_ID</code>) ou na seção de visão geral do domínio.
                                    </small>
                                    @error('cloudflare_zone_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div id="token_fields" class="auth-method-fields" style="display: none;">
                                <div class="form-group">
                                    <label for="cloudflare_api_token">API Token</label>
                                    <input type="password" class="form-control" id="cloudflare_api_token" name="cloudflare_api_token" 
                                        value="{{ $api->config['cloudflare_api_token'] ?? '' }}">
                                    <small class="form-text text-muted">
                                        Token de API. Pode ser criado em <a href="https://dash.cloudflare.com/profile/api-tokens" target="_blank">dash.cloudflare.com/profile/api-tokens</a> na seção "API Tokens" > "Create Token".
                                        <br>
                                        <strong>Importante:</strong> O token deve ter as seguintes permissões:
                                        <ul>
                                            <li>Zone > Zone > Read</li>
                                            <li>Zone > DNS > Edit</li>
                                        </ul>
                                    </small>
                                    @error('cloudflare_api_token')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="cloudflare_zone_id_token"><strong class="text-danger">* Zone ID (Domínio Padrão)</strong></label>
                                    <input type="text" class="form-control border border-danger" id="cloudflare_zone_id_token" name="cloudflare_zone_id" 
                                        value="{{ $api->config['cloudflare_zone_id'] ?? '' }}" required>
                                    <small class="form-text text-danger font-weight-bold">
                                        ATENÇÃO: Este campo é OBRIGATÓRIO para sincronização de registros DNS.
                                    </small>
                                    <small class="form-text text-muted">
                                        ID da zona/domínio que será usado para sincronizar registros DNS. Pode ser encontrado na URL do painel Cloudflare 
                                        (<code>https://dash.cloudflare.com/*/ZONE_ID</code>) ou na seção de visão geral do domínio.
                                    </small>
                                    @error('cloudflare_zone_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Dica:</strong> Para uma melhor segurança, a Cloudflare recomenda o uso de API Tokens em vez de API Keys. 
                                Os tokens permitem acesso mais granular e podem ser revogados individualmente.
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Configuração
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Configuração não disponível para este tipo de API.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Alternar entre campos de método de autenticação
    $(document).ready(function() {
        // Configurar estado inicial
        toggleAuthFields();
        
        // Adicionar listeners
        $('input[name="auth_method"]').change(function() {
            toggleAuthFields();
        });
        
        function toggleAuthFields() {
            var method = $('input[name="auth_method"]:checked').val();
            
            if (method === 'api_key') {
                $('#api_key_fields').show();
                $('#token_fields').hide();
                $('#cloudflare_email').attr('required', true);
                $('#cloudflare_api_key').attr('required', true);
                $('#cloudflare_api_token').attr('required', false);
            } else {
                $('#api_key_fields').hide();
                $('#token_fields').show();
                $('#cloudflare_email').attr('required', false);
                $('#cloudflare_api_key').attr('required', false);
                $('#cloudflare_api_token').attr('required', true);
            }
        }
    });
</script>
@endsection
