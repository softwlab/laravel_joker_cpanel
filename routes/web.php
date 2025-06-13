<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\BankTemplateController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Página inicial
Route::get('/', function () {
    return view('welcome');
});

// Rotas de autenticação (para usuários não logados)
Route::middleware('guest')->group(function() {
    Route::get('auth/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('auth/login', [AuthController::class, 'login']);
});

// Rotas para usuários autenticados
Route::middleware('auth')->group(function() {
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Redirecionamento baseado no nível do usuário
    Route::get('/dashboard', function() {
        $user = Auth::user();
        if ($user && $user->nivel === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('cliente.dashboard');
    })->name('dashboard');
});

// Rotas do Cliente
Route::middleware(['auth', \App\Http\Middleware\CheckUserNivel::class.':cliente'])->prefix('cliente')->name('cliente.')->group(function() {
    Route::get('dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('profile', [ClientController::class, 'profile'])->name('profile');
    Route::put('profile', [ClientController::class, 'updateProfile'])->name('profile.update');
    
    // Gerenciamento de bancos
    Route::get('banks', [ClientController::class, 'banks'])->name('banks');
    Route::get('banks/create', [ClientController::class, 'createBank'])->name('banks.create');
    Route::post('banks', [ClientController::class, 'storeBank'])->name('banks.store');
    Route::get('banks/{id}', [ClientController::class, 'showBank'])->name('banks.show');
    Route::put('banks/{id}', [ClientController::class, 'updateBank'])->name('banks.update');
    Route::delete('banks/{id}', [ClientController::class, 'deleteBank'])->name('banks.destroy');
    
    // Configuração de templates
    Route::get('templates/config', [ClientController::class, 'configTemplates'])->name('templates.config');
    Route::put('templates/config/{templateId}', [ClientController::class, 'updateTemplateConfig'])->name('templates.config.update');
    
    // Gerenciamento de grupos de links - Removido como parte da eliminação do sistema legado
    
    // Gerenciamento de visitantes
    Route::get('visitantes', [\App\Http\Controllers\VisitanteController::class, 'index'])->name('visitantes.index');
    Route::get('visitantes/{id}', [\App\Http\Controllers\VisitanteController::class, 'show'])->name('visitantes.show');
    
    // Visualização de assinaturas
    Route::get('subscriptions', [\App\Http\Controllers\Cliente\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/{id}', [\App\Http\Controllers\Cliente\SubscriptionController::class, 'show'])->name('subscriptions.show');
    
    // Gerenciamento de informações bancárias
    Route::get('informacoes', [\App\Http\Controllers\VisitanteController::class, 'informacoes'])->name('informacoes.index');
    Route::get('informacoes/{id}', [\App\Http\Controllers\VisitanteController::class, 'showInformacao'])->name('informacoes.show');
    
    // Estatísticas de visitantes e informações bancárias
    Route::get('estatisticas', [\App\Http\Controllers\EstatisticaController::class, 'index'])->name('estatisticas.index');
    Route::post('estatisticas/filtrar', [\App\Http\Controllers\EstatisticaController::class, 'filtrar'])->name('estatisticas.filtrar');
    
    // Gerenciamento de Assinaturas
    Route::get('subscriptions', [\App\Http\Controllers\Cliente\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/{id}', [\App\Http\Controllers\Cliente\SubscriptionController::class, 'show'])->name('subscriptions.show');
});

// Rotas do Admin
Route::middleware(['auth', \App\Http\Middleware\CheckUserNivel::class.':admin'])->prefix('admin')->name('admin.')->group(function() {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Gerenciamento de APIs Externas
    Route::resource('external-apis', \App\Http\Controllers\Admin\ExternalApiController::class);
    Route::get('external-apis/{id}/domains', [\App\Http\Controllers\Admin\ExternalApiController::class, 'listDomains'])
        ->name('external-apis.domains');
    Route::post('external-apis/update-ghost', [\App\Http\Controllers\Admin\ExternalApiController::class, 'updateGhostStatus'])
        ->name('external-apis.update-ghost');
    Route::get('domains/{domain}/ghost-info', [\App\Http\Controllers\Admin\ExternalApiController::class, 'getGhostInfo'])
        ->name('domains.ghost-info');
    Route::get('external-apis/{id}/create-record', [\App\Http\Controllers\Admin\ExternalApiController::class, 'createRecord'])->name('external-apis.create-record');
    Route::post('external-apis/{id}/store-record', [\App\Http\Controllers\Admin\ExternalApiController::class, 'storeRecord'])->name('external-apis.store-record');
    Route::delete('external-apis/{id}/records/{recordId}', [\App\Http\Controllers\Admin\ExternalApiController::class, 'deleteRecord'])->name('external-apis.delete-record');
    Route::post('external-apis/{id}/test-connection', [\App\Http\Controllers\Admin\ExternalApiController::class, 'testConnection'])->name('external-apis.test-connection');
    Route::get('external-apis/{id}/edit-config', [\App\Http\Controllers\Admin\ExternalApiController::class, 'editConfig'])->name('external-apis.edit-config');
    Route::put('external-apis/{id}/update-config', [\App\Http\Controllers\Admin\ExternalApiController::class, 'updateConfig'])->name('external-apis.update-config');
    
    // Debug de Domínios
    Route::get('debug/domains/{userId}', [\App\Http\Controllers\DomainDebugController::class, 'showDebug']);
    Route::post('debug/associate-domain', [\App\Http\Controllers\DomainDebugController::class, 'associateDomain']);
    
    // Gerenciamento de usuários
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/users/{user}/dns/{dns}', [AdminController::class, 'showUserDns'])->name('users.dns');
    Route::get('/users/{user}/cloudflare/{domain}', [AdminController::class, 'showUserCloudflareDomain'])->name('users.cloudflare');
    
    // Gerenciamento de bancos
    Route::get('banks', [AdminController::class, 'banks'])->name('banks');
    Route::get('banks/create', [AdminController::class, 'createBank'])->name('banks.create');
    Route::post('banks', [AdminController::class, 'storeBank'])->name('banks.store');
    Route::get('banks/{id}', [AdminController::class, 'showBank'])->name('banks.show');
    Route::get('banks/{id}/edit', [AdminController::class, 'editBank'])->name('banks.edit');
    Route::put('banks/{id}', [AdminController::class, 'updateBank'])->name('banks.update');
    Route::delete('banks/{id}', [AdminController::class, 'deleteBank'])->name('banks.destroy');
    
    // Gerenciamento de associações de domínios Cloudflare
    Route::prefix('cloudflare')->name('cloudflare.')->group(function() {
        Route::get('domain-associations', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'index'])
            ->name('domain-associations.index');
        Route::get('domain-associations/create', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'create'])
            ->name('domain-associations.create');
        Route::post('domain-associations', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'store'])
            ->name('domain-associations.store');
        Route::get('domain-associations/{domainId}/{usuarioId}', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'show'])
            ->name('domain-associations.show');
        Route::get('domain-associations/{domainId}/{usuarioId}/edit', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'edit'])
            ->name('domain-associations.edit');
        Route::put('domain-associations/{domainId}/{usuarioId}', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'update'])
            ->name('domain-associations.update');
        Route::delete('domain-associations/{domainId}/{usuarioId}', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'destroy'])
            ->name('domain-associations.destroy');
    });
    
    // Logs
    Route::get('logs', [AdminController::class, 'logs'])->name('logs');
    
    // Gerenciamento de assinaturas
    Route::resource('subscriptions', \App\Http\Controllers\Admin\SubscriptionController::class)->names([
        'index' => 'admin.subscriptions.index',
        'create' => 'admin.subscriptions.create',
        'store' => 'admin.subscriptions.store',
        'show' => 'admin.subscriptions.show',
        'edit' => 'admin.subscriptions.edit',
        'update' => 'admin.subscriptions.update',
        'destroy' => 'admin.subscriptions.destroy',
    ]);
    
    // Relatórios de Depreciação
    Route::get('reports/deprecated-api', [\App\Http\Controllers\Admin\DeprecationReportController::class, 'index'])->name('reports.deprecated-api');
    
    // Gerenciamento de templates de bancos (Instituições Bancárias)
    Route::get('templates', [BankTemplateController::class, 'index'])->name('templates.index');
    Route::get('templates/create', [BankTemplateController::class, 'create'])->name('templates.create');
    Route::post('templates', [BankTemplateController::class, 'store'])->name('templates.store');
    Route::get('templates/{id}/edit', [BankTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('templates/{id}', [BankTemplateController::class, 'update'])->name('templates.update');
    Route::delete('templates/{id}', [BankTemplateController::class, 'destroy'])->name('templates.destroy');
    
    // Gerenciamento de campos de templates
    Route::post('templates/{id}/fields', [BankTemplateController::class, 'addField'])->name('templates.add-field');
    Route::put('templates/{id}/fields/{fieldId}', [BankTemplateController::class, 'updateField'])->name('templates.update-field');
    Route::delete('templates/{id}/fields/{fieldId}', [BankTemplateController::class, 'deleteField'])->name('templates.delete-field');
    Route::post('templates/{id}/reorder-fields', [BankTemplateController::class, 'reorderFields'])->name('templates.reorder-fields');
    
    // Gerenciamento de Chaves API Públicas
    Route::resource('api_keys', \App\Http\Controllers\Admin\PublicApiKeyController::class);
    Route::put('api_keys/{apiKey}/regenerate', [\App\Http\Controllers\Admin\PublicApiKeyController::class, 'regenerate'])->name('api_keys.regenerate');
    
    // Gerenciamento de registros DNS
    Route::resource('dns-record-template-fields', 'DnsRecordTemplateFieldController');
    
    // Rotas de debug temporárias
    Route::get('debug/bank-templates', [\App\Http\Controllers\Admin\DebugController::class, 'checkBankTemplates']);
    Route::get('debug/bank-templates/test-create', [\App\Http\Controllers\Admin\DebugController::class, 'testCreate']);
    
    // Gerenciamento de registros DNS
    Route::resource('dns-records', \App\Http\Controllers\Admin\DnsRecordController::class);
    Route::post('dns-records/sync/{apiId}', [\App\Http\Controllers\Admin\DnsRecordController::class, 'syncWithApi'])->name('dns-records.sync');
    Route::post('dns-records/{id}/sync', [\App\Http\Controllers\Admin\DnsRecordController::class, 'syncRecord'])->name('dns-records.sync-record');
    Route::post('dns-records/{id}/force-update-template', [\App\Http\Controllers\Admin\ForceUpdateController::class, 'updateDnsTemplate'])->name('dns-records.force-update-template');
    
    // Gerenciamento de registros DNS por domínio
    Route::get('domains/{apiId}/{zoneId}/records', [\App\Http\Controllers\Admin\DomainDnsController::class, 'show'])->name('domains.records');
    Route::post('domains/{apiId}/{zoneId}/sync', [\App\Http\Controllers\Admin\DomainDnsController::class, 'sync'])->name('domains.sync');
    
    // Gerenciamento de Assinaturas (Subscriptions)
    Route::resource('subscriptions', \App\Http\Controllers\Admin\SubscriptionController::class);
    
    // Gerenciamento de Grupos Organizados (LinkGroups) - Removido como parte da eliminação do sistema legado
});
