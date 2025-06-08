<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CloudflareDomain;
use App\Models\ExternalApi;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class CloudflareDomainSeeder extends Seeder
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

        // Obter os usuários
        $usuarios = Usuario::where('nivel', 'cliente')->get();

        if ($usuarios->isEmpty()) {
            $this->command->error('Nenhum usuário cliente encontrado. Execute o UsuarioSeeder primeiro.');
            return;
        }

        $this->command->info('Criando domínios Cloudflare para os usuários...');

        // Lista de TLDs comuns para criar domínios variados
        $tlds = ['.com', '.com.br', '.net', '.org', '.io', '.app', '.site', '.online', '.digital', '.tech'];

        // Criar domínios fictícios do Cloudflare
        $domainCount = 15; // Total de domínios a serem criados
        
        for ($i = 1; $i <= $domainCount; $i++) {
            // Gerar um nome de domínio aleatório
            $domainName = 'exemplo' . $i . $faker->randomElement($tlds);
            
            // Criar o domínio no Cloudflare
            $domain = CloudflareDomain::create([
                'external_api_id' => $cloudflareApi->id,
                'zone_id' => Str::uuid(),
                'name' => $domainName,
                'status' => 'active',
                'is_ghost' => false,
                'name_servers' => json_encode(['ns1.cloudflare.com', 'ns2.cloudflare.com']),
                'records_count' => $faker->numberBetween(3, 15)
            ]);
            
            // Atribuir o domínio a 1 ou 2 usuários aleatórios
            $userCount = $faker->numberBetween(1, 2);
            $selectedUsers = $usuarios->random($userCount);
            
            foreach ($selectedUsers as $usuario) {
                // Tabela pivot cloudflare_domain_usuario
                DB::table('cloudflare_domain_usuario')->insert([
                    'cloudflare_domain_id' => $domain->id,
                    'usuario_id' => $usuario->id,
                    'status' => 'active',
                    'config' => json_encode([
                        'auto_ssl' => $faker->boolean(80),
                        'always_use_https' => $faker->boolean(70),
                        'minify' => ['js' => $faker->boolean(60), 'css' => $faker->boolean(60), 'html' => $faker->boolean(40)]
                    ]),
                    'notes' => $faker->optional(0.7)->sentence(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $this->command->info("Domínio {$domainName} associado ao usuário {$usuario->nome}");
            }
        }

        $this->command->info('✓ Total de ' . $domainCount . ' domínios Cloudflare criados com sucesso!');
    }
}
