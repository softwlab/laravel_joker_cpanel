<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcessoSeeder extends Seeder
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
                'usuario_id' => 1,
                'ip' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:139.0) Gecko/20100101 Firefox/139.0',
                'data_acesso' => '2025-06-13 01:22:19',
                'ultimo_acesso' => '2025-06-13 01:22:19',
                'created_at' => '2025-06-13 01:22:19',
                'updated_at' => '2025-06-13 01:22:19'
            ]
        ];

        foreach ($rows as $row) {
            DB::table('acessos')->insert($row);
        }

        // Reativa as verificações de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}