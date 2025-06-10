<?php

namespace App\Services;

/**
 * Serviço base para todos os serviços do sistema
 * Fornece métodos utilitários comuns
 */
abstract class BaseService
{
    /**
     * Formata datas para serem compatíveis com SQLite e MySQL
     * 
     * @param string $format Formato de data desejado
     * @param string $column Nome da coluna de data
     * @return string Expressão SQL compatível
     */
    protected function formatDate($format, $column)
    {
        // Usar função strftime em vez de DATE_FORMAT para compatibilidade com SQLite
        return "strftime('$format', $column)";
    }
}
