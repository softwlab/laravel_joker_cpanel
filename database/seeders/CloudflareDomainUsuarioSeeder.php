<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CloudflareDomain;
use App\Models\Usuario;
use App\Models\ExternalApi;

class CloudflareDomainUsuarioSeeder extends Seeder
{
    /**
     * Criar domínios Cloudflare e associá-los aos usuários
     */
    public function run()
    {
        $this->command->info('Criando associações de domínios Cloudflare com usuários...');

        // Obter a API do Cloudflare
        $api = ExternalApi::where('type', 'cloudflare')->first();
        
        if (!$api) {
            $this->command->error('API do Cloudflare não encontrada. Execute antes o script atualizar-cloudflare.php');
            return;
        }
        
        // Garantir que a API está ativa
        if ($api->status !== 'active') {
            $api->status = 'active';
            $api->save();
            $this->command->info('API do Cloudflare ativada com sucesso!');
        }

        // Obter todos os usuários clientes
        $usuarios = Usuario::where('nivel', '!=', 'admin')->get();
        
        if ($usuarios->isEmpty()) {
            $this->command->info('Nenhum usuário cliente encontrado.');
            return;
        }

        // Criar ou obter domínios Cloudflare
        $domains = CloudflareDomain::all();
        if ($domains->isEmpty()) {
            // Criar alguns domínios de exemplo
            for ($i = 1; $i <= 3; $i++) {
                $domain = CloudflareDomain::create([
                    'external_api_id' => $api->id,
                    'zone_id' => 'zone' . $i . 'dde98dbb6ac93710412de79b3272acd8',
                    'name' => 'example' . $i . '.com',
                    'status' => 'active',
                    'paused' => 0,
                    'is_ghost' => 0,
                    'records_count' => rand(1, 5),
                    'name_servers' => json_encode(['ns1.cloudflare.com', 'ns2.cloudflare.com']),
                    'meta' => json_encode(['created_on' => now()->toDateTimeString()])
                ]);
                
                $this->command->info("Domínio Cloudflare criado: {$domain->name}");
                $domains->push($domain);
            }
        }

        // Associar domínios aos usuários
        foreach ($usuarios as $usuario) {
            // Associar 1-2 domínios aleatórios a cada usuário
            $randomDomains = $domains->random(rand(1, min(2, $domains->count())));
            
            foreach ($randomDomains as $domain) {
                // Verificar se a associação já existe
                $exists = \DB::table('cloudflare_domain_usuario')
                    ->where('cloudflare_domain_id', $domain->id)
                    ->where('usuario_id', $usuario->id)
                    ->exists();
                
                if (!$exists) {
                    \DB::table('cloudflare_domain_usuario')->insert([
                        'cloudflare_domain_id' => $domain->id,
                        'usuario_id' => $usuario->id,
                        'status' => 'active',
                        'config' => json_encode(['auto_create_dns' => true]),
                        'notes' => 'Associação automática via seeder',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $this->command->info("Associação criada: Usuário {$usuario->nome} com domínio {$domain->name}");
                }
            }
        }
        
        $this->command->info('Finalizado! Associações de domínios Cloudflare com usuários criadas com sucesso.');
    }
}
