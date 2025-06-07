<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user with username 'admin'
        Usuario::create([
            'nome' => 'Administrador',
            'email' => 'admin',  // Use this as username for login
            'senha' => Hash::make('admin'),
            'ativo' => true,
            'nivel' => 'admin',
            'api_token' => Str::random(60),
        ]);

        $this->command->info('Admin user created: username = admin, password = admin');
    }
}
