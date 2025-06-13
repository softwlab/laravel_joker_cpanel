<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TesteApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Encontrar os usuários administradores
        $adminUser = Usuario::where('email', 'admin@jokerlab.com')->first();
        $adminTesteUser = Usuario::where('email', 'admin.teste@jokerlab.com')->first();
        
        // API Key pública para visitantes DNS
        ApiKey::create([
            'name' => 'API Key para Visitantes DNS',
            'key' => 'ekSsEyrtOAbRjEp3041789082UiazYEZXJYgzpfePLg1vkxoz5jMHVXNDFs4HaYm',
            'description' => 'Chave de API para registrar visitantes DNS e informações bancárias',
            'active' => 1,
            'usuario_id' => $adminUser->id ?? null,
        ]);
        
        // API Key para administração
        ApiKey::create([
            'name' => 'API Key Admin',
            'key' => Str::random(60),
            'description' => 'Chave de API para administração do sistema',
            'active' => 1,
            'usuario_id' => $adminUser->id ?? null,
        ]);
        
        // API Key para testes de desenvolvimento
        ApiKey::create([
            'name' => 'API Key de Testes',
            'key' => 'test_api_key_' . Str::random(30),
            'description' => 'Chave de API para testes de desenvolvimento',
            'active' => 1,
            'usuario_id' => $adminTesteUser->id ?? null,
        ]);
        
        $this->command->info('Chaves de API criadas com sucesso!');
    }
}
