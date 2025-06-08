<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Para exportar os dados existentes no banco de dados para seeders
        // Descomentar a linha abaixo e executar: php artisan db:seed --class=ExportExistingDataSeeder
        // $this->call(ExportExistingDataSeeder::class);
        
        // Call our custom seeders in the correct order
        $this->call([
            ClearDataSeeder::class,        // Limpar dados antigos antes de recriar
            AdminUserSeeder::class,        // Admin user com username login
            UsuarioSeeder::class,          // Usuários regulares (clientes)
            BankTemplateSeeder::class,     // Templates de bancos (necessários para associar aos registros DNS)
            ExternalApiSeeder::class,      // API do Cloudflare
            
            // Para gerar dados de domínios e DNS, você pode usar um dos seguintes métodos:
            
            // OPÇÃO 1: Usar os seeders separados (legado)
            // CloudflareDomainSeeder::class, // Domínios do Cloudflare
            // DnsRecordSeeder::class,        // Registros DNS para os domínios
            
            // OPÇÃO 2: Usar o seeder unificado (gera dados ficticios)
            // UnifiedDomainsAndDnsSeeder::class, // Criação unificada de domínios e registros DNS

            // OPÇÃO 3: Usar dados exportados do sistema atual (recomendado)
            // Depois de executar o ExportExistingDataSeeder, descomentar as linhas abaixo:
            // \Database\Seeders\Exports\CloudflareDomainExportSeeder::class,
            // \Database\Seeders\Exports\DnsRecordExportSeeder::class,
            // \Database\Seeders\Exports\DomainUserAssociationExportSeeder::class,
            
            VisitanteSeeder::class,        // Visitantes do sistema
        ]);
    }
}
