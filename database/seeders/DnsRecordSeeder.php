<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CloudflareDomain;
use App\Models\DnsRecord;
use App\Models\Usuario;
use App\Models\ExternalApi;
use App\Models\BankTemplate;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class DnsRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // Obter a API do Cloudflare
        $cloudflareApi = ExternalApi::where('type', 'cloudflare')->first();

        if (!$cloudflareApi) {
            $this->command->error('API do Cloudflare não encontrada. Execute o ExternalApiSeeder primeiro.');
            return;
        }

        // Obter os domínios Cloudflare
        $domains = CloudflareDomain::all();

        if ($domains->isEmpty()) {
            $this->command->error('Nenhum domínio Cloudflare encontrado. Execute o CloudflareDomainSeeder primeiro.');
            return;
        }

        // Obter templates de banco para criar subdominios de bancos
        $bankTemplates = BankTemplate::all();

        if ($bankTemplates->isEmpty()) {
            $this->command->info('Nenhum template de banco encontrado. Apenas registros DNS padrão serão criados.');
        }

        $this->command->info('Criando registros DNS para os domínios Cloudflare...');

        // Tipos de registros DNS comuns
        $recordTypes = ['A', 'CNAME', 'MX', 'TXT', 'AAAA'];
        
        // Contadores para estatísticas
        $totalRecords = 0;
        
        // Para cada domínio, criar alguns registros DNS
        foreach ($domains as $domain) {
            // Usuários associados a este domínio
            $usuarioIds = $domain->usuarios->pluck('id')->toArray();
            
            if (empty($usuarioIds)) {
                continue; // Pular domínios sem usuários associados
            }
            
            // Criar registros DNS padrão (A, CNAME, MX, etc)
            $recordsCount = $faker->numberBetween(3, 7);
            
            for ($i = 1; $i <= $recordsCount; $i++) {
                $recordType = $faker->randomElement($recordTypes);
                $subdomain = $faker->randomElement(['www', 'api', 'mail', 'blog', 'app', 'portal', 'admin', '']);
                $name = $subdomain ? $subdomain . '.' . $domain->name : $domain->name;
                $userId = $faker->randomElement($usuarioIds);
                
                // Conteúdo específico com base no tipo de registro
                $content = '';
                switch ($recordType) {
                    case 'A':
                        $content = $faker->ipv4;
                        break;
                    case 'AAAA':
                        $content = $faker->ipv6;
                        break;
                    case 'CNAME':
                        $content = $faker->domainName;
                        break;
                    case 'MX':
                        $content = 'mx' . $faker->numberBetween(1, 3) . '.' . $faker->domainName;
                        break;
                    case 'TXT':
                        $content = 'v=spf1 include:_spf.google.com ~all';
                        break;
                    default:
                        $content = $faker->domainName;
                }
                
                // Criar o registro DNS
                DnsRecord::create([
                    'external_api_id' => $cloudflareApi->id,
                    'user_id' => $userId,
                    'record_type' => $recordType,
                    'name' => $name,
                    'content' => $content,
                    'ttl' => $faker->randomElement([60, 120, 300, 600, 1800, 3600]),
                    'priority' => $recordType === 'MX' ? $faker->numberBetween(1, 20) : null,
                    'status' => 'active',
                    'extra_data' => json_encode([
                        'proxied' => $faker->boolean(50),
                        'created_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
                    ])
                ]);
                
                $totalRecords++;
            }
            
            // Se houver templates de banco, criar alguns subdomínios de bancos
            if (!$bankTemplates->isEmpty()) {
                $bankRecordsCount = $faker->numberBetween(2, 5);
                
                for ($i = 1; $i <= $bankRecordsCount; $i++) {
                    $bankTemplate = $bankTemplates->random();
                    $userId = $faker->randomElement($usuarioIds);
                    
                    // Gerar subdomínio com base no nome do banco
                    $bankSlug = Str::slug($bankTemplate->name);
                    $subdomain = $bankSlug . '-' . strtolower(Str::random(5));
                    $name = $subdomain . '.' . $domain->name;
                    
                    // Criar registro DNS tipo A para o banco
                    DnsRecord::create([
                        'external_api_id' => $cloudflareApi->id,
                        'user_id' => $userId,
                        'bank_template_id' => $bankTemplate->id,
                        'record_type' => 'A',
                        'name' => $name,
                        'content' => $faker->ipv4,
                        'ttl' => 3600,
                        'status' => 'active',
                        'extra_data' => json_encode([
                            'proxied' => true,
                            'bank_type' => $bankTemplate->slug,
                            'created_at' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
                        ])
                    ]);
                    
                    $totalRecords++;
                }
            }
        }

        $this->command->info('✓ Total de ' . $totalRecords . ' registros DNS criados com sucesso!');
    }
}
