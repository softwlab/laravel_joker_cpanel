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
        // Create 5 client users
        for ($i = 1; $i <= 5; $i++) {
            Usuario::create([
                'nome' => 'Cliente ' . $i,
                'email' => 'cliente' . $i . '@example.com',
                'senha' => Hash::make('cliente' . $i),
                'ativo' => true,
                'nivel' => 'cliente',
                'api_token' => Str::random(60),
            ]);
            
            $this->command->info('Cliente ' . $i . ' criado com sucesso');
        }
    }
}
