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
use Illuminate\Support\Facades\DB;

class UnifiedDomainsAndDnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Este seeder cria domínios Cloudflare e registros DNS de forma unificada,
     * refletindo o conceito de que são fundamentalmente a mesma coisa no sistema.
     * Os domínios são registros DNS raiz, e os registros DNS são subdomínios.
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

        // Obter os usuários
        $usuarios = Usuario::where('nivel', 'cliente')->get();

        if ($usuarios->isEmpty()) {
            $this->command->error('Nenhum usuário cliente encontrado. Execute o UsuarioSeeder primeiro.');
            return;
        }

        // Obter templates de banco
        $bankTemplates = BankTemplate::all();

        if ($bankTemplates->isEmpty()) {
            $this->command->info('Nenhum template de banco encontrado. Apenas registros DNS padrão serão criados.');
        }

        $this->command->info('Criando domínios Cloudflare e registros DNS de forma unificada...');

        // Lista de TLDs comuns para criar domínios variados
        $tlds = ['.com', '.com.br', '.net', '.org', '.io', '.app', '.site', '.online', '.digital', '.tech'];
        
        // Tipos de registros DNS comuns
        $recordTypes = ['A', 'CNAME', 'MX', 'TXT', 'AAAA'];
        
        // Contadores para estatísticas
        $domainCount = 0;
        $dnsRecordCount = 0;
        
        // Criar entre 10 e 15 domínios principais
        $totalDomains = $faker->numberBetween(10, 15);
        
        for ($i = 1; $i <= $totalDomains; $i++) {
            // Gerar um nome de domínio base
            $domainName = $faker->randomElement(['site', 'loja', 'app', 'empresa', 'tech', 'digital']) . 
                          $i . $faker->randomElement($tlds);
            
            // 1. CRIAR DOMÍNIO CLOUDFLARE (que também é um registro DNS conceitualmente)
            $domain = CloudflareDomain::create([
                'external_api_id' => $cloudflareApi->id,
                'zone_id' => Str::uuid(),
                'name' => $domainName,
                'status' => 'active',
                'is_ghost' => false,
                'name_servers' => json_encode(['ns1.cloudflare.com', 'ns2.cloudflare.com']),
                'records_count' => 0 // Será atualizado depois
            ]);
            
            $domainCount++;
            
            // Associar este domínio a 1-2 usuários aleatórios
            $selectedUserCount = $faker->numberBetween(1, 2);
            $selectedUsers = $usuarios->random($selectedUserCount);
            
            foreach ($selectedUsers as $usuario) {
                // Criar associação na tabela pivot
                DB::table('cloudflare_domain_usuario')->insert([
                    'cloudflare_domain_id' => $domain->id,
                    'usuario_id' => $usuario->id,
                    'status' => $faker->randomElement(['active', 'paused', 'pending']),
                    'config' => json_encode([
                        'auto_ssl' => $faker->boolean(80),
                        'always_use_https' => $faker->boolean(70),
                    ]),
                    'notes' => $faker->optional(0.5)->sentence(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // 2. CRIAR REGISTROS DNS PARA ESTE DOMÍNIO
                // Estes são conceitualmente subdomínios do domínio principal
                $subdomainsCount = $faker->numberBetween(2, 6);
                
                for ($j = 0; $j < $subdomainsCount; $j++) {
                    $recordType = $faker->randomElement($recordTypes);
                    $subdomain = $faker->randomElement(['www', 'mail', 'blog', 'api', 'app', 'admin', 'portal', '']);
                    $name = $subdomain ? $subdomain . '.' . $domainName : $domainName;
                    
                    // Conteúdo específico para o tipo de registro
                    $content = $this->generateDnsContent($faker, $recordType);
                    
                    // Criar o registro DNS (subdomínio)
                    DnsRecord::create([
                        'external_api_id' => $cloudflareApi->id,
                        'user_id' => $usuario->id,
                        'record_type' => $recordType,
                        'name' => $name,
                        'content' => $content,
                        'ttl' => $faker->randomElement([60, 120, 300, 600, 1800, 3600]),
                        'priority' => $recordType === 'MX' ? $faker->numberBetween(1, 20) : null,
                        'status' => 'active',
                        'extra_data' => json_encode([
                            'proxied' => $faker->boolean(70),
                            'created_at' => now()->toDateTimeString(),
                        ])
                    ]);
                    
                    $dnsRecordCount++;
                }
                
                // 3. CRIAR REGISTROS DNS PARA BANCOS (se houver templates)
                if (!$bankTemplates->isEmpty()) {
                    // Cada usuário terá entre 1-3 templates de banco aplicados
                    $bankCount = $faker->numberBetween(1, 3);
                    $selectedTemplates = $bankTemplates->random($bankCount);
                    
                    foreach ($selectedTemplates as $template) {
                        // Gerar subdomínio com base no nome do banco
                        $bankSlug = Str::slug($template->name);
                        $subdomain = $bankSlug . '-' . strtolower(Str::random(3));
                        $name = $subdomain . '.' . $domainName;
                        
                        // Criar registro DNS para o banco (sempre tipo A)
                        DnsRecord::create([
                            'external_api_id' => $cloudflareApi->id,
                            'user_id' => $usuario->id,
                            'bank_template_id' => $template->id,
                            'record_type' => 'A',
                            'name' => $name,
                            'content' => $faker->ipv4,
                            'ttl' => 3600,
                            'status' => 'active',
                            'extra_data' => json_encode([
                                'proxied' => true,
                                'bank_type' => $template->slug,
                                'created_at' => now()->toDateTimeString(),
                            ])
                        ]);
                        
                        $dnsRecordCount++;
                    }
                }
            }
            
            // Atualizar a contagem de registros DNS para este domínio
            $recordCount = DnsRecord::where('external_api_id', $cloudflareApi->id)
                                  ->where('name', 'like', '%' . $domainName)
                                  ->count();
            $domain->records_count = $recordCount;
            $domain->save();
        }
        
        $this->command->info('✓ Total de ' . $domainCount . ' domínios Cloudflare criados.');
        $this->command->info('✓ Total de ' . $dnsRecordCount . ' registros DNS (subdomínios) criados.');
        $this->command->info('✓ Seeder unificado concluído com sucesso!');
    }
    
    /**
     * Gerar conteúdo específico para cada tipo de registro DNS
     */
    private function generateDnsContent($faker, $recordType)
    {
        switch ($recordType) {
            case 'A':
                return $faker->ipv4;
            case 'AAAA':
                return $faker->ipv6;
            case 'CNAME':
                return $faker->domainName;
            case 'MX':
                return 'mx' . $faker->numberBetween(1, 3) . '.' . $faker->domainName;
            case 'TXT':
                return 'v=spf1 include:_spf.google.com ~all';
            default:
                return $faker->domainName;
        }
    }
}
