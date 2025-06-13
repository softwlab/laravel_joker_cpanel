<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Gerado automaticamente a partir dos dados existentes.
     */
    public function run(): void
    {
        // Desativa as verificações de chaves estrangeiras temporariamente
        DB::statement('PRAGMA foreign_keys = OFF;');

        $rows = [
            [
                'id' => 1,
                'uuid' => '158810bc-8b0c-4e80-a248-03a1bb89f803',
                'user_id' => 1,
                'name' => 'Plano Básico',
                'description' => 'Plano básico para teste',
                'value' => 29.9,
                'start_date' => '2025-06-13 01:28:29',
                'end_date' => '2026-06-13 01:28:29',
                'status' => 'active',
                'created_at' => '2025-06-13 01:28:29',
                'updated_at' => '2025-06-13 01:28:29'
            ]
        ];

        foreach ($rows as $row) {
            DB::table('subscriptions')->insert($row);
        }

        // Reativa as verificações de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}