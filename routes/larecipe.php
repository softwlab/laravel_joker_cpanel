<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

// Redirecionamento da documentação do LaRecipe para o Scribe (que já está funcionando)
Route::prefix('docs')->group(function () {
    // Redireciona qualquer requisição à documentação para a documentação gerada pelo Scribe
    Route::get('/{any?}', function () {
        return Redirect::to('/docs');
    })->where('any', '.*');
});

// Rotas para compatibilidade com o sistema
Route::name('larecipe.')->group(function () {
    Route::get('/index-placeholder', function () {
        return Redirect::to('/docs');
    })->name('index');
    
    Route::get('/show-placeholder', function () {
        return Redirect::to('/docs');
    })->name('show');
});
