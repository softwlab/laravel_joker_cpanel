<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Se necessário para operações específicas, desabilite temporariamente restrições FK
        if (config('database.connections.sqlite.foreign_key_constraints') === true && 
            request()->is('admin/dns-records/*')) {
            \DB::statement('PRAGMA foreign_keys = OFF;');
        }
    }
}
