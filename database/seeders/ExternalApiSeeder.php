<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExternalApiSeeder extends Seeder
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
                'id' => 2,
                'external_link_api' => 'https://api.cloudflare.com/client/v4/',
                'key_external_api' => 'HqfCnRleMrYmXeddjX2EDtqLRQAhg3BjbJRYIva9',
                'status' => 'active',
                'json' => null,
                'type' => 'cloudflare',
                'name' => 'Cloudflare Backup',
                'description' => 'API de backup para gerenciamento de domínios e registros DNS via Cloudflare',
                'created_at' => '2025-06-13 01:57:37',
                'updated_at' => '2025-06-13 02:05:05',
                'config' => '"{\"cloudflare_email\":\"andressaworking1707@gmail.com\",\"cloudflare_api_token\":\"5Z059JMN1m3Amev2M3ar3I4QJUq7KFBZ1uw2WXOt\",\"auth_method\":\"token\"}"'
            ]
        ];

        foreach ($rows as $row) {
            DB::table('external_apis')->insert($row);
        }

        // Reativa as verificações de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}