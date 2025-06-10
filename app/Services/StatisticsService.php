<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Serviço base para estatísticas
 * 
 * Esta classe serve como base para todos os serviços de estatísticas,
 * fornecendo métodos comuns para cache e formatação de dados.
 */
abstract class StatisticsService
{
    /**
     * Tempo de cache padrão em minutos
     */
    protected int $cacheTime = 60;
    
    /**
     * Prefixo usado para as chaves de cache
     */
    protected string $cachePrefix = 'stats';
    
    /**
     * Obtém uma estatística específica com suporte a cache
     *
     * @param string $key Identificador da estatística
     * @param callable $callback Função que gera a estatística se não estiver em cache
     * @param int|null $customCacheTime Tempo de cache personalizado em minutos
     * @return mixed
     */
    protected function getCachedStat(string $key, callable $callback, ?int $customCacheTime = null)
    {
        $cacheKey = $this->getCacheKey($key);
        $cacheTime = $customCacheTime ?? $this->cacheTime;
        
        return Cache::remember($cacheKey, $cacheTime * 60, $callback);
    }
    
    /**
     * Invalida o cache de uma estatística específica
     *
     * @param string $key Identificador da estatística
     * @return void
     */
    public function invalidateCache(string $key = '')
    {
        if (empty($key)) {
            // Se não foi especificada uma chave, limpa todos os caches relacionados
            $this->invalidateAllCache();
            return;
        }
        
        Cache::forget($this->getCacheKey($key));
    }
    
    /**
     * Invalida todo o cache relacionado a este serviço de estatísticas
     *
     * @return void
     */
    public function invalidateAllCache()
    {
        // Implementação base que pode ser estendida em serviços específicos
        // Em uma implementação real, poderíamos usar tags ou padrões de chave
        // para limpar caches relacionados
    }
    
    /**
     * Gera uma chave de cache padronizada
     *
     * @param string $key Identificador da estatística
     * @return string
     */
    protected function getCacheKey(string $key): string
    {
        // Formata: stats:subclasse:chave
        $className = strtolower(class_basename($this));
        return "{$this->cachePrefix}:{$className}:{$key}";
    }
}
