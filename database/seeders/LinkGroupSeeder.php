<?php

namespace Database\Seeders;

use App\Models\LinkGroup;
use App\Models\LinkGroupItem;
use App\Models\Bank;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LinkGroupSeeder extends Seeder
{
    /**
     * Criar grupos de links e associar links bancários a esses grupos
     * seguindo a nova arquitetura onde clientes possuem grupos de links
     * e cada link está associado a um template bancário.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        
        // Verifica se existem usuários para criar dados
        $usuarios = Usuario::all();
        if ($usuarios->isEmpty()) {
            $this->command->error('Nenhum usuário encontrado. Execute o seeder de usuários primeiro.');
            return;
        }
        
        $this->command->info("Criando grupos de links e associando links bancários...");
        $totalGroupsCreated = 0;
        
        // Categorias comuns para grupos de links
        $groupCategories = [
            'Redes Sociais' => [
                'icons' => ['fab fa-facebook', 'fab fa-instagram', 'fab fa-twitter', 'fab fa-linkedin',
                           'fab fa-youtube', 'fab fa-tiktok', 'fab fa-pinterest', 'fab fa-snapchat'],
                'urls' => ['facebook.com', 'instagram.com', 'twitter.com', 'linkedin.com', 
                          'youtube.com', 'tiktok.com', 'pinterest.com', 'snapchat.com']
            ],
            'Ferramentas Úteis' => [
                'icons' => ['fas fa-tools', 'fas fa-wrench', 'fas fa-cogs', 'fas fa-hammer'],
                'urls' => ['google.com', 'github.com', 'canva.com', 'trello.com', 'notion.so']
            ],
            'Compras' => [
                'icons' => ['fas fa-shopping-cart', 'fas fa-store', 'fas fa-tag', 'fas fa-shopping-bag'],
                'urls' => ['amazon.com.br', 'mercadolivre.com.br', 'americanas.com.br', 'magazineluiza.com.br']
            ],
            'Entretenimento' => [
                'icons' => ['fas fa-film', 'fas fa-gamepad', 'fas fa-music', 'fas fa-tv'],
                'urls' => ['netflix.com', 'youtube.com', 'twitch.tv', 'spotify.com', 'primevideo.com']
            ]
        ];
        
        // Adicionar categorias para os grupos bancários
        $bankGroupCategories = [
            'Contas Pessoais' => [
                'icons' => ['fas fa-university', 'fas fa-wallet', 'fas fa-piggy-bank', 'fas fa-money-bill'],
                'description' => 'Bancos e serviços financeiros pessoais'
            ],
            'Investimentos' => [
                'icons' => ['fas fa-chart-line', 'fas fa-chart-bar', 'fas fa-dollar-sign', 'fas fa-coins'],
                'description' => 'Plataformas de investimento e corretoras'
            ],
            'Contas Empresariais' => [
                'icons' => ['fas fa-building', 'fas fa-briefcase', 'fas fa-landmark', 'fas fa-file-invoice-dollar'],
                'description' => 'Bancos e serviços financeiros para empresas'
            ],
            'Cartões de Crédito' => [
                'icons' => ['fas fa-credit-card', 'far fa-credit-card', 'fas fa-money-check', 'fas fa-money-check-alt'],
                'description' => 'Cartões de crédito e serviços relacionados'
            ],
        ];
        
        // Mesclar as categorias
        $allCategories = array_merge($groupCategories, $bankGroupCategories);
        
        foreach ($usuarios as $usuario) {
            // Para cada usuário, criar entre 4 e 10 grupos de links
            $numGroups = rand(4, 10);
            
            $this->command->info("Criando {$numGroups} grupos de links para o usuário #{$usuario->id}...");
            
            // Obter todos os links bancários do usuário para distribuição nos grupos
            $userBanks = Bank::where('usuario_id', $usuario->id)->get();
            if ($userBanks->isEmpty()) {
                $this->command->warn("O usuário #{$usuario->id} não possui links bancários. Execute o BankSeeder primeiro.");
                continue;
            }
            
            // Dividir os links bancários entre os grupos (alguns ficarão sem grupo intencionalmente)
            $banksForGroups = $userBanks->shuffle()->take(ceil($userBanks->count() * 0.8)); // Usa 80% dos links
            $banksPerGroup = ceil($banksForGroups->count() / ($numGroups / 2)); // Metade dos grupos terão links bancários
            
            $bankIndex = 0;
            $totalItemsCreated = 0;
            
            for ($i = 0; $i < $numGroups; $i++) {
                // Escolhe aleatoriamente uma categoria de grupo
                $categoryName = $faker->randomElement(array_keys($allCategories));
                $categoryData = $allCategories[$categoryName];
                
                // Define um título para o grupo baseado na categoria
                if ($faker->boolean(70)) { // 70% de chance de usar o nome da categoria
                    $groupTitle = $categoryName;
                } else { // 30% de chance de personalizar o nome
                    $prefix = $faker->randomElement(['Meus ', 'Favoritos ', '', 'Top ']);
                    $suffix = $faker->randomElement([' Favoritos', ' Pessoais', ' Importantes', ' VIP', '']);
                    $groupTitle = $prefix . $categoryName . $suffix;
                }
                
                // Cria um grupo de links para o usuário
                $group = LinkGroup::create([
                    'title' => $groupTitle,
                    'description' => isset($categoryData['description']) ? $categoryData['description'] : $faker->sentence(rand(3, 8)),
                    'usuario_id' => $usuario->id,
                    'active' => true,
                    'created_at' => $faker->dateTimeBetween('-60 days', 'now'),
                    'updated_at' => $faker->dateTimeThisMonth()
                ]);
                
                $totalGroupsCreated++;
                
                // Se for uma categoria bancária, adiciona links bancários
                if (array_key_exists($categoryName, $bankGroupCategories) && $bankIndex < $banksForGroups->count()) {
                    $numBanksForThisGroup = min($banksPerGroup, $banksForGroups->count() - $bankIndex);
                    
                    for ($b = 0; $b < $numBanksForThisGroup; $b++) {
                        if ($bankIndex >= $banksForGroups->count()) break;
                        
                        // Associa o banco ao grupo via tabela pivô
                        DB::table('link_group_banks')->insert([
                            'link_group_id' => $group->id,
                            'bank_id' => $banksForGroups[$bankIndex]->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        $bankIndex++;
                    }
                }
                
                // Adiciona itens regulares ao grupo (entre 2 e 8 itens)
                if (isset($categoryData['urls'])) {
                    $numItems = rand(2, 8);
                    
                    for ($j = 0; $j < $numItems; $j++) {
                        $icons = $categoryData['icons'];
                        $urls = $categoryData['urls'];
                        
                        $icon = $faker->randomElement($icons);
                        $baseUrl = $faker->randomElement($urls);
                        
                        // Gera um nome para o item
                        if ($categoryName === 'Redes Sociais') {
                            // Formatação especial para redes sociais
                            $social = pathinfo(parse_url("https://www.{$baseUrl}", PHP_URL_HOST), PATHINFO_FILENAME);
                            $itemName = ucfirst($social); // Primeira letra maiúsula (ex: Facebook)
                        } else {
                            // Para outras categorias, gera um nome aleatório
                            $itemPrefix = $faker->randomElement(['Link para ', '', 'Acesso ', 'Meu ']);
                            $itemName = $itemPrefix . $faker->word;
                        }
                        
                        // Adiciona itens ao grupo
                        LinkGroupItem::create([
                            'title' => $itemName,
                            'url' => "https://www.{$baseUrl}",
                            'icon' => $icon,
                            'group_id' => $group->id,
                            'active' => true,
                            'order' => $j + 1,
                            'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
                            'updated_at' => $faker->dateTimeThisMonth()
                        ]);
                        
                        $totalItemsCreated++;
                    }
                }
            }
        }
        
        $this->command->info("✓ Total de {$totalGroupsCreated} grupos de links criados com sucesso!");
        $this->command->info("✓ Links bancários associados aos grupos com sucesso!");
    }
}
