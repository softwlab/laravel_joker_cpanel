<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        
        // API pública - limitação mais rigorosa
        RateLimiter::for('public_api', function (Request $request) {
            return Limit::perMinute(30)->by($request->header('X-API-KEY') ?: $request->ip());
        });

        $this->routes(function () {
            // Rotas de API padrão
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
                
            // Rotas da API pública - nova adição
            Route::middleware(['api', 'throttle:public_api'])
                ->prefix('api/public')
                ->name('api.public.')
                ->group(base_path('routes/api_public.php'));

            // Rotas web
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
                
            // Rotas do LaRecipe (documentação)
            Route::middleware('web')
                ->group(base_path('routes/larecipe.php'));
        });
    }
}
