<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicExternalPagesController;

/*
|--------------------------------------------------------------------------
| API Pública Routes
|--------------------------------------------------------------------------
|
| Rotas da API pública que requerem autenticação via API key
|
*/

// Grupo de rotas autenticadas com a chave de API pública
Route::middleware('public_api_auth')->group(function () {
    // Rota para obter dados de um domínio/subdomínio específico
    Route::get('domain_external/{identifier}', [PublicExternalPagesController::class, 'getDomainData']);
});
