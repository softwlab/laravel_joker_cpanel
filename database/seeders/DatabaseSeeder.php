<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Desativa verificações de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = OFF;');

        // Tabelas base (sem dependências)
        $this->call([
            UsuarioSeeder::class,
            BankSeeder::class,
            ExternalApiSeeder::class,
            BankTemplateSeeder::class,
            
            // Tabelas com dependências simples
            ApiKeySeeder::class,
            CloudflareDomainSeeder::class,
            LinkGroupSeeder::class,
            UserConfigSeeder::class,
            
            // Tabelas com dependências complexas
            DnsRecordSeeder::class,
            SubscriptionSeeder::class,
            LinkGroupItemSeeder::class,
            BankFieldSeeder::class,
            
            // Tabelas de relacionamento
            DnsRecordSubscriptionSeeder::class,
            DnsRecordTemplateSeeder::class,
            LinkGroupBankSeeder::class,
            TemplateUserConfigSeeder::class,
            
            // Tabelas de dados de operação
            VisitanteSeeder::class,
            InformacoesBancariaSeeder::class,
            AcessoSeeder::class,
            ApiKeyLogSeeder::class,
        ]);

        // Reativa verificações de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}