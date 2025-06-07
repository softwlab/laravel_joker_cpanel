<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\VisitanteApiController;
use Illuminate\Support\Facades\Route;

// Rotas públicas que usam identificação por domínio ou token
Route::get('user/details', [ApiController::class, 'getUserDetails']);
Route::get('banks', [ApiController::class, 'getBanks']);

// API para visitantes e informações bancárias
Route::post('visitantes', [VisitanteApiController::class, 'registrarVisitante']);
Route::post('informacoes-bancarias', [VisitanteApiController::class, 'registrarInformacaoBancaria']);

// Rotas protegidas por API Key
Route::middleware('api_key')->group(function() {
    // Atualização de configurações
    Route::post('user/config', [ApiController::class, 'updateUserConfig']);
    Route::post('banks/links', [ApiController::class, 'updateBankLinks']);
});

// Rotas para o dashboard que requerem autenticação por token
Route::middleware('auth:sanctum')->prefix('dashboard')->group(function() {
    Route::get('stats', [ApiController::class, 'getDashboardStats']);
    Route::get('activity', [ApiController::class, 'getRecentActivity']);
});