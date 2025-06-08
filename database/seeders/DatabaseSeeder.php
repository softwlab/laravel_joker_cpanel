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
        // Call our custom seeders in the correct order
        $this->call([
            ClearDataSeeder::class,        // Limpar dados antigos antes de recriar
            AdminUserSeeder::class,        // Admin user com username login
            UsuarioSeeder::class,          // Usuários regulares (clientes)
            BankTemplateSeeder::class,     // Templates de bancos (necessários para associar aos registros DNS)
            ExternalApiSeeder::class,      // API do Cloudflare
            CloudflareDomainSeeder::class, // Domínios do Cloudflare
            DnsRecordSeeder::class,        // Registros DNS para os domínios
            VisitanteSeeder::class,        // Visitantes do sistema
        ]);
    }
}
