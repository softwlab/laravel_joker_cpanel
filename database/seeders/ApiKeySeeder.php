<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApiKeySeeder extends Seeder
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
                'name' => 'API Key Padrão',
                'key' => 'ekSsEyrtOAbRjEp3041789082UiazYEZXJYgzpfePLg1vkxoz5jMHVXNDFs4HaYm',
                'description' => 'Chave API para testes',
                'active' => 1,
                'usuario_id' => null,
                'created_at' => '2025-06-13 01:30:16',
                'updated_at' => '2025-06-13 01:30:16',
                'deleted_at' => null
            ]
        ];

        foreach ($rows as $row) {
            DB::table('api_keys')->insert($row);
        }

        // Reativa as verificações de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}