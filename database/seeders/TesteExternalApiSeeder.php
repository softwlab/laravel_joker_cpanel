<?php

namespace Database\Seeders;

use App\Models\ExternalApi;
use Illuminate\Database\Seeder;

class TesteExternalApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // API Cloudflare principal
        ExternalApi::create([
            'name' => 'Cloudflare DNS API',
            'external_link_api' => 'https://api.cloudflare.com/client/v4',
            'key_external_api' => 'cf_api_key_' . uniqid(),
            'status' => 'active',
            'type' => 'dns',
            'description' => 'API de integração com DNS do Cloudflare',
            'json' => [
                'email' => 'admin@jokerlab.com',
                'global_api_key' => 'api_key_cloudflare_' . uniqid()
            ],
            'config' => [
                'zone_id' => 'zone_' . uniqid(),
                'default_ttl' => 3600,
                'use_proxy' => true
            ]
        ]);

        // API Cloudflare secundária
        ExternalApi::create([
            'name' => 'Cloudflare DNS API (Backup)',
            'external_link_api' => 'https://api.cloudflare.com/client/v4',
            'key_external_api' => 'cf_api_key_' . uniqid(),
            'status' => 'inactive',
            'type' => 'dns',
            'description' => 'API de backup para DNS do Cloudflare',
            'json' => [
                'email' => 'backup@jokerlab.com',
                'global_api_key' => 'api_key_cloudflare_' . uniqid()
            ],
            'config' => [
                'zone_id' => 'zone_' . uniqid(),
                'default_ttl' => 1800,
                'use_proxy' => true
            ]
        ]);
        
        $this->command->info('APIs Externas do Cloudflare criadas com sucesso!');
    }
}
