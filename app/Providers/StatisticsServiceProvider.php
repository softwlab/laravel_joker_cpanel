<?php

namespace App\Providers;

use App\Services\UserStatisticsService;
use App\Services\DnsStatisticsService;
use App\Services\BankingStatisticsService;
use Illuminate\Support\ServiceProvider;

class StatisticsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registra os serviços como singletons para melhor performance
        $this->app->singleton(UserStatisticsService::class);
        $this->app->singleton(DnsStatisticsService::class);
        $this->app->singleton(BankingStatisticsService::class);
        
        // Registra aliases mais curtos para facilitar o uso
        $this->app->alias(UserStatisticsService::class, 'user.stats');
        $this->app->alias(DnsStatisticsService::class, 'dns.stats');
        $this->app->alias(BankingStatisticsService::class, 'banking.stats');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
