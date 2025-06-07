<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'API key não fornecida'], 401);
        }

        $keyExists = ApiKey::where('key', $apiKey)
            ->where('active', true)
            ->exists();

        if (!$keyExists) {
            return response()->json(['error' => 'API key inválida'], 401);
        }

        return $next($request);
    }
}
