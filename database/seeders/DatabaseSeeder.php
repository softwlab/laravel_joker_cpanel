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
            ClearDataSeeder::class, // Limpar dados antigos antes de recriar
            AdminUserSeeder::class, // Admin user com username login
            UsuarioSeeder::class,   // Usuários regulares
            BankTemplateSeeder::class, // Templates de bancos (devem vir antes dos links bancários)
            BankSeeder::class,     // Links bancários baseados nos templates
            LinkGroupSeeder::class, // Grupos de links e associação com links bancários
            VisitanteSeeder::class, // Deve ser executado depois dos links e bancos
        ]);
    }
}
