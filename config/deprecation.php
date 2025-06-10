<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações de Depreciação da API Legada
    |--------------------------------------------------------------------------
    |
    | Este arquivo define as datas importantes para o processo de depreciação
    | do sistema legado de links bancários. Estas datas são usadas nos avisos
    | e nos relatórios administrativos.
    |
    */

    // Data de início da fase de depreciação
    'start_date' => '01/06/2025',

    // Data de intensificação dos avisos
    'warning_date' => '01/10/2025',

    // Data de desativação completa da API antiga
    'end_date' => '31/12/2025',

    // Retenção de logs (em dias)
    'log_retention_days' => 90,

    // URL da documentação de migração
    'migration_docs_url' => 'https://docs.jokerlab.com.br/migracao-dns',
];
