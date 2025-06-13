<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioSeeder extends Seeder
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
                'nome' => 'Administrador',
                'email' => 'admin',
                'senha' => '$2y$12$6bIz4mdB67n6m0T9vRkSCedboKxX4hBbN8IUmuT0YvMkzDcJgYeFK',
                'ativo' => 1,
                'nivel' => 'admin',
                'api_token' => 'P4BVJx2r3lORAU7kzWupk7MFYcW6IhGSeO78xuqPrLOqoXkurPL9HLkMEvg2',
                'remember_token' => null,
                'created_at' => '2025-06-13 01:22:04',
                'updated_at' => '2025-06-13 01:22:04'
            ],
            [
                'id' => 2,
                'nome' => 'Cliente 1',
                'email' => 'cliente1@example.com',
                'senha' => '$2y$12$RcVqYSN/1N8MPND6HLOxc.lZsjVg5NtRWnIqpC91vhCmwh1PTy37u',
                'ativo' => 1,
                'nivel' => 'cliente',
                'api_token' => '7WNfeB0Zqir11soEwIzLx5LbXysrKWBC3WlZ5Rz8wJE3TSA7lp3AmQKnumUj',
                'remember_token' => null,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 02:09:22'
            ],
            [
                'id' => 3,
                'nome' => 'Cliente 2',
                'email' => 'cliente2@example.com',
                'senha' => '$2y$12$uK9uk3sBwxiwTa4fM/ePEOEFlY5xis6TH.jUHDK1W.2UIVpw6bGa6',
                'ativo' => 1,
                'nivel' => 'cliente',
                'api_token' => 't8PsgGdU8wFwsgxSvYGCFP16ByqzRKRpXWIez3bQRzGYfwQiUB99q1g0ePQr',
                'remember_token' => null,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05'
            ],
            [
                'id' => 4,
                'nome' => 'Cliente 3',
                'email' => 'cliente3@example.com',
                'senha' => '$2y$12$vN91FCo0/ZLudRMkJClHKehC0Jfv5DVaVDC4kOBWWbDgIGqploy9y',
                'ativo' => 1,
                'nivel' => 'cliente',
                'api_token' => 'jtckH8RFnNYEaiiY1u4YgXB6JrcZ5xPKLgzM7TpnGqWFyAiQh2vjiaUe6cK7',
                'remember_token' => null,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05'
            ],
            [
                'id' => 5,
                'nome' => 'Cliente 4',
                'email' => 'cliente4@example.com',
                'senha' => '$2y$12$9miAlDuWRNgw8cgjSN1Pf..1G/t82TORGrp9u3MQS6qZVIpYzzNNq',
                'ativo' => 1,
                'nivel' => 'cliente',
                'api_token' => 'zCHm0BkUyiZltksccZExMMboyu1XsjHvUC6KUr61zK30GrsJxtoE8DEMCDl8',
                'remember_token' => null,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05'
            ],
            [
                'id' => 6,
                'nome' => 'Cliente 5',
                'email' => 'cliente5@example.com',
                'senha' => '$2y$12$Zoi3gSCsPLyP8Yxr.2o8aeYf8HEud0apLv1e4.aduMpX32ArbfjgO',
                'ativo' => 1,
                'nivel' => 'cliente',
                'api_token' => 'sBFerPG7JrHqiGjHuV4zZ6KJu1Ha5O3vp0GN0DN0UkClGtQY41W8HY8jV6uq',
                'remember_token' => null,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05'
            ]
        ];

        foreach ($rows as $row) {
            DB::table('usuarios')->insert($row);
        }

        // Reativa as verificações de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}