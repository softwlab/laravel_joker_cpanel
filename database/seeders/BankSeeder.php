<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;
use App\Models\Usuario;
use App\Models\BankTemplate;
use App\Models\LinkGroup;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class BankSeeder extends Seeder
{
    /**
     * Criar links bancários baseados nos templates disponíveis
     * seguindo a nova arquitetura onde bancos são templates e clientes 
     * possuem links associados a esses templates.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        
        // Verifica se existem usuários e templates para criar dados
        $usuarios = Usuario::all();
        if ($usuarios->isEmpty()) {
            $this->command->error('Nenhum usuário encontrado. Execute o seeder de usuários primeiro.');
            return;
        }
        
        $templates = BankTemplate::with('fields')->where('active', true)->get();
        if ($templates->isEmpty()) {
            $this->command->error('Nenhum template de banco encontrado. Execute o BankTemplateSeeder primeiro.');
            return;
        }
        
        // Lista de nomes personalizados para links bancários
        $customLinkNames = [
            'Minha Conta Corrente', 'Conta Salário', 'Conta Investimento', 'Conta Conjunta',
            'Conta Digital', 'Conta Premium', 'Conta Poupança', 'Conta Empresarial',
            'Conta PJ', 'Banco Principal', 'Investimentos', 'Conta Internacional',
            'Banco Secundário', 'Cartão de Crédito', 'Financiamentos', 'Previdência'
        ];
        
        $this->command->info("Criando links bancários para os usuários...");
        $totalLinksCreated = 0;
        
        // Para cada usuário, cria links bancários associados a templates
        foreach ($usuarios as $usuario) {
            // Dividir os links entre os usuários disponíveis
            $linksPerUser = ceil(50 / count($usuarios));
            
            $this->command->info("Criando {$linksPerUser} links bancários para o usuário #{$usuario->id}...");
            
            for ($i = 0; $i < $linksPerUser; $i++) {
                // Seleciona aleatoriamente um template de banco
                $template = $templates->random();
                
                // Escolhe um nome personalizado para o link ou gera um com base no template
                if ($i < count($customLinkNames)) {
                    $linkName = $customLinkNames[$i] . ' - ' . $template->name;
                } else {
                    $prefix = $faker->randomElement(['Acesso ', 'Link ', 'Conta ', 'Banco ']);
                    $linkName = $prefix . $template->name;
                }
                
                $slug = Str::slug($linkName);
                
                // Gera URLs para este link bancário
                $mainUrl = "https://www.{$template->slug}.com.br/login";
                $secondaryURLs = [
                    "https://app.{$template->slug}.com.br/acessar",
                    "https://mobile.{$template->slug}.com.br/login",
                    "https://banking.{$template->slug}.com.br",
                    "https://net.{$template->slug}.com.br/login",
                ];
                
                // Cria entre 1 e 3 URLs alternativas para redirecionamento
                $redirUrls = [];
                $numRedirs = rand(1, 3);
                for ($r = 0; $r < $numRedirs; $r++) {
                    $redirUrls[] = $faker->randomElement($secondaryURLs);
                }
                
                // Gera links no formato correto
                $links = [
                    'atual' => $mainUrl,
                    'redir' => $redirUrls
                ];
                
                // Definir datas para o link
                $createdAt = $faker->dateTimeBetween('-90 days', 'now');
                $updatedAt = $faker->dateTimeBetween($createdAt, 'now');
                
                // Assegura que o slug seja adequado para uso em URLs
                $uniqueSlug = Str::slug($linkName) . '-' . $faker->unique()->numberBetween(1000, 9999);
                
                // 90% de chance de estar ativo
                $isActive = $faker->boolean(90);
                
                // Gera valores para os campos dinâmicos do template
                $fieldValues = [];
                foreach ($template->fields as $field) {
                    if (!$field->active) continue;
                    
                    switch ($field->field_type) {
                        case 'email':
                            $fieldValues[$field->field_name] = $faker->email;
                            break;
                        case 'password':
                            $fieldValues[$field->field_name] = $faker->password(8, 15);
                            break;
                        case 'textarea':
                            $fieldValues[$field->field_name] = $faker->paragraph(1);
                            break;
                        case 'number':
                            $fieldValues[$field->field_name] = $faker->randomNumber(6);
                            break;
                        case 'text':
                        default:
                            // Formatação específica para campos comuns
                            if ($field->field_name == 'agencia') {
                                $fieldValues[$field->field_name] = $faker->numberBetween(1000, 9999);
                            } elseif ($field->field_name == 'conta') {
                                $fieldValues[$field->field_name] = $faker->numberBetween(10000, 99999) . '-' . $faker->numberBetween(0, 9);
                            } elseif ($field->field_name == 'cpf') {
                                $fieldValues[$field->field_name] = $faker->cpf(false);
                            } else {
                                $fieldValues[$field->field_name] = $faker->text(30);
                            }
                            break;
                    }
                }
                
                // Cria um link bancário com o template selecionado
                $bank = Bank::create([
                    'name' => $linkName,
                    'slug' => $uniqueSlug,
                    'description' => $faker->sentence(rand(3, 8)),
                    'url' => $links['atual'],
                    'links' => $links,
                    'active' => $isActive,
                    'usuario_id' => $usuario->id,
                    'bank_template_id' => $template->id,
                    'field_values' => $fieldValues,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt
                ]);
                
                $totalLinksCreated++;
            }
        }
        
        $this->command->info("✓ Total de {$totalLinksCreated} links bancários criados com sucesso!");
    }
}
