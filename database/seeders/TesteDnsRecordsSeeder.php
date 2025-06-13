<?php

namespace Database\Seeders;

use App\Models\DnsRecord;
use Illuminate\Database\Seeder;

class TesteDnsRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Registros DNS de teste
        DnsRecord::create([
            'record_type' => 'A',
            'name' => 'teste.example.com',
            'content' => '192.168.1.1',
            'ttl' => 3600,
            'priority' => 0,
            'status' => 'active',
            'extra_data' => [
                'proxied' => true,
                'zone_id' => 'zone123',
                'record_id' => 'record123'
            ]
        ]);
        
        DnsRecord::create([
            'record_type' => 'A',
            'name' => 'api.example.com',
            'content' => '192.168.1.2',
            'ttl' => 3600,
            'priority' => 0,
            'status' => 'active',
            'extra_data' => [
                'proxied' => true,
                'zone_id' => 'zone123',
                'record_id' => 'record124'
            ]
        ]);
        
        DnsRecord::create([
            'record_type' => 'A',
            'name' => 'app.example.com',
            'content' => '192.168.1.3',
            'ttl' => 3600,
            'priority' => 0,
            'status' => 'active',
            'extra_data' => [
                'proxied' => true,
                'zone_id' => 'zone123',
                'record_id' => 'record125'
            ]
        ]);
        
        $this->command->info('Registros DNS de teste criados com sucesso!');
    }
}
