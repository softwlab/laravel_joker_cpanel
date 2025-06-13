<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeder manual criado com os dados existentes.
     */
    public function run(): void
    {
        // Desativa as verificações de chaves estrangeiras temporariamente
        DB::statement('PRAGMA foreign_keys = OFF;');

        $templates = [
            [
                'id' => 1,
                'name' => 'Banco do Brasil',
                'slug' => 'banco-do-brasil',
                'description' => 'Template para acessos ao Banco do Brasil (BB)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Caixa Econômica Federal',
                'slug' => 'caixa-economica-federal',
                'description' => 'Template para acessos à Caixa Econômica Federal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Nubank',
                'slug' => 'nubank',
                'description' => 'Template para acessos ao Nubank',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Binance',
                'slug' => 'binance',
                'description' => 'Template para acessos à exchange Binance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'E-mail Genérico',
                'slug' => 'email',
                'description' => 'Template para acessos a serviços de e-mail (Gmail, Outlook, etc)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Inserir cada registro, ignorando possíveis duplicações
        foreach ($templates as $template) {
            // Verificar se já existe um registro com este ID
            $exists = DB::table('bank_templates')
                ->where('id', $template['id'])
                ->exists();

            if (!$exists) {
                DB::table('bank_templates')->insert($template);
            }
        }

        // Reativa as verificações de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}