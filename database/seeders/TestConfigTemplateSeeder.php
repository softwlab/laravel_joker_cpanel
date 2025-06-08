<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DnsRecord;
use App\Models\BankTemplate;
use App\Models\Usuario;
use App\Models\ExternalApi;
use Illuminate\Support\Facades\Hash;

class TestConfigTemplateSeeder extends Seeder
{
    /**
     * Criar registros DNS e templates para testar a configuração de templates
     */
    public function run()
    {
        // Verificar se há usuários no sistema
        $userCount = Usuario::count();
        $this->command->info("Total de usuários encontrados: {$userCount}");
        
        // Criar usuários clientes se necessário
        $usuarios = Usuario::where('nivel', '!=', 'admin')->limit(2)->get();
        
        if ($usuarios->isEmpty()) {
            $this->command->info('Criando usuários clientes para teste...');
            
            // Criar alguns usuários clientes
            for ($i = 1; $i <= 2; $i++) {
                $usuario = Usuario::create([
                    'nome' => "Cliente Teste {$i}",
                    'email' => "cliente{$i}@teste.com",
                    'senha' => Hash::make("cliente{$i}"),
                    'nivel' => 'cliente',
                    'ativo' => 1
                ]);
                
                $this->command->info("Usuário cliente {$usuario->nome} criado com sucesso.");
                $usuarios->push($usuario);
            }
        }
        
        // Obter templates disponíveis
        $templates = BankTemplate::with('fields')->get();
        
        if ($templates->isEmpty()) {
            $this->command->info('Nenhum template encontrado. Execute o BankTemplateSeeder primeiro.');
            return;
        }
        
        // Verificar se existe uma API externa ou criar uma
        $api = ExternalApi::first();
        
        if (!$api) {
            $api = ExternalApi::create([
                'name' => 'Cloudflare Teste',
                'type' => 'cloudflare',
                'active' => 1,
                'config' => json_encode([
                    'api_token' => 'test_token_12345',
                    'email' => 'teste@example.com'
                ])
            ]);
            $this->command->info("API externa Cloudflare criada com sucesso, ID: {$api->id}");
        } else {
            $this->command->info("Usando API externa existente, ID: {$api->id}");
        }
        
        $this->command->info('Criando registros DNS para testar a configuração de templates...');
        
        foreach ($usuarios as $usuario) {
            // Criar 2 registros DNS para cada usuário
            for ($i = 1; $i <= 2; $i++) {
                $template = $templates->random();
                $dnsRecord = DnsRecord::create([
                    'user_id' => $usuario->id,
                    'external_api_id' => $api->id,
                    'record_type' => 'A',
                    'name' => 'teste-'.$usuario->id.'-'.$i.'.example.com',
                    'content' => '192.168.1.'.$i,
                    'bank_template_id' => $template->id,
                    'ttl' => 3600,
                    'priority' => 0,
                    'status' => 'active',
                    'extra_data' => json_encode(['zone_id' => 'zone_'.$usuario->id.'_'.$i])
                ]);
                
                $this->command->info("Registro DNS {$dnsRecord->name} criado para o usuário {$usuario->nome} com template #{$dnsRecord->bank_template_id}");
            }
        }
        
        $this->command->info('Registros DNS criados com sucesso!');
        
        // Criando algumas configurações de template para teste
        $this->command->info('Criando configurações de template para os registros DNS...');
        
        $dnsRecords = DnsRecord::whereIn('user_id', $usuarios->pluck('id'))
            ->whereNotNull('bank_template_id')
            ->get();
            
        foreach ($dnsRecords as $record) {
            if (!$record->bankTemplate) {
                continue;
            }
            
            $template = $record->bankTemplate;
            $fields = $template->fields;
            
            if ($fields->isEmpty()) {
                continue;
            }
            
            // Configurar os campos (ativar todos inicialmente)
            $config = [];
            foreach ($fields as $index => $field) {
                $config[$field->field_name] = [
                    'active' => true,
                    'order' => $field->order
                ];
            }
            
            // Criar a configuração para o usuário
            $templateConfig = \App\Models\TemplateUserConfig::updateOrCreate(
                [
                    'user_id' => $record->user_id,
                    'template_id' => $template->id,
                    'record_id' => $record->id
                ],
                ['config' => $config]
            );
            
            $this->command->info("Configuração de template criada para o registro {$record->name} do usuário #{$record->user_id}");
        }
        
        $this->command->info('Configurações de templates criadas com sucesso!');
    }
}
