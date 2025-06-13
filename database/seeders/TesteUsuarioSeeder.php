<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TesteUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrador principal
        Usuario::create([
            'nome' => 'Administrador',
            'email' => 'admin@jokerlab.com',
            'senha' => Hash::make('admin123'),
            'nivel' => 'admin',
            'ativo' => 1,
            'api_token' => Str::random(60),
        ]);
        
        // Admin de teste
        Usuario::create([
            'nome' => 'Admin Teste',
            'email' => 'admin.teste@jokerlab.com',
            'senha' => Hash::make('admin123'),
            'nivel' => 'admin',
            'ativo' => 1,
        ]);
        
        // Clientes
        Usuario::create([
            'nome' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'senha' => Hash::make('cliente123'),
            'nivel' => 'client',
            'ativo' => 1,
        ]);
        
        Usuario::create([
            'nome' => 'Multibanco',
            'email' => 'multibanco@example.com',
            'senha' => Hash::make('cliente123'),
            'nivel' => 'client',
            'ativo' => 1,
        ]);
        
        Usuario::create([
            'nome' => 'Banco Nacional',
            'email' => 'banco.nacional@example.com',
            'senha' => Hash::make('cliente123'),
            'nivel' => 'client',
            'ativo' => 1,
        ]);
        
        $this->command->info('Usuários recriados com sucesso!');
    }
}
