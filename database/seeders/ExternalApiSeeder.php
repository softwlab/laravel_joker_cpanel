<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExternalApi;

class ExternalApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar a API do Cloudflare
        $cloudflareApi = ExternalApi::create([
            'name' => 'Cloudflare DNS API',
            'description' => 'API para gerenciamento de domínios e registros DNS via Cloudflare',
            'external_link_api' => 'https://api.cloudflare.com/client/v4/',
            'key_external_api' => 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7',
            'type' => 'cloudflare',
            'status' => 'active',
            'config' => [
                'cloudflare_email' => 'andressaworking1707@gmail.com',
                'cloudflare_api_key' => 'uxWv_7jt-2wLhO7lb4ASYrp-AQ1GKXfHICBrPvS7',
                'auth_method' => 'api_key'
            ]
        ]);

        $this->command->info('API Cloudflare criada com sucesso!');
    }
}
