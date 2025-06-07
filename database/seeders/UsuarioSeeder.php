<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        Usuario::create([
            'nome' => 'Administrador',
            'email' => 'admin@example.com',
            'senha' => Hash::make('password'),
            'ativo' => true,
            'nivel' => 'admin',
            'api_token' => Str::random(60),
        ]);

        // Create client user
        Usuario::create([
            'nome' => 'Cliente Demo',
            'email' => 'cliente@example.com',
            'senha' => Hash::make('password'),
            'ativo' => true,
            'nivel' => 'cliente',
            'api_token' => Str::random(60),
        ]);
    }
}
