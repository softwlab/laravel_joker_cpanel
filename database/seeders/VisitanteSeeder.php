<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visitante;
use App\Models\InformacaoBancaria;
use App\Models\LinkGroupItem;
use App\Models\Usuario;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class VisitanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        
        // Verifica se existem usuários e links para criar dados
        $usuarios = Usuario::all();
        if ($usuarios->isEmpty()) {
            $this->command->error('Nenhum usuário encontrado. Execute o seeder de usuários primeiro.');
            return;
        }
        
        $totalVisitantesCreated = 0;
        $totalInfoBancariasCreated = 0;
        $minVisitantes = 100; // Mínimo de 100 visitantes
        
        // Calcular aproximadamente quantos visitantes por usuário
        $visitantesPorUsuario = ceil($minVisitantes / count($usuarios));
        
        // Dados de navegadores e sistemas operacionais para user agents mais realistas
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'Samsung Browser'];
        $os = ['Windows', 'macOS', 'Android', 'iOS', 'Linux'];
        $versions = ['10', '11', '12', '13', '14', '15', '100', '101', '102', '103', '104', '105'];
        
        // Lista de referrers comuns
        $referrers = [
            'https://google.com',
            'https://facebook.com',
            'https://instagram.com',
            'https://youtube.com',
            'https://twitter.com',
            'https://linkedin.com',
            'https://pinterest.com',
            'https://whatsapp.com',
            'https://bing.com',
            'https://yahoo.com',
            null, // Referrer vazio
            ''
        ];
        
        // Nomes de banco para dados mais realistas
        $bancos = [
            'Banco do Brasil', 'Itaú', 'Bradesco', 'Caixa', 'Santander', 'Nubank',
            'Inter', 'C6 Bank', 'Original', 'Next', 'PagBank', 'BTG Pactual', 'Neon',
            'Will Bank', 'Banco Pan', 'Banco Sofisa', 'BMG', 'Banrisul', 'Sicredi', 'Sicoob'
        ];
        
        // Status possíveis para transações
        $statusPossibilities = ['pendente', 'aprovado', 'rejeitado', 'em análise', 'cancelado'];
        
        // Para cada usuário, cria dados de teste
        foreach ($usuarios as $usuario) {
            // Busca links deste usuário
            $links = LinkGroupItem::whereHas('group', function($query) use ($usuario) {
                $query->where('usuario_id', $usuario->id);
            })->get();
            
            if ($links->isEmpty()) {
                $this->command->info("Usuário #{$usuario->id} não tem links. Pulando...");
                continue;
            }
            
            $this->command->info("Criando {$visitantesPorUsuario} visitantes para o usuário #{$usuario->id}...");
            
            for ($i = 0; $i < $visitantesPorUsuario; $i++) {
                // Escolhe um link aleatório deste usuário
                $link = $links->random();
                
                // Gera UserAgent mais realista
                $browser = $faker->randomElement($browsers);
                $version = $faker->randomElement($versions);
                $osSystem = $faker->randomElement($os);
                $userAgent = "Mozilla/5.0 ({$osSystem}) AppleWebKit/537.36 (KHTML, like Gecko) {$browser}/{$version}.0.0.0 Safari/537.36";
                
                // Data de criação (últimos 90 dias)
                $createdAt = $faker->dateTimeBetween('-90 days', 'now');
                
                // Cria um visitante
                $visitante = Visitante::create([
                    'uuid' => (string) Str::uuid(),
                    'usuario_id' => $usuario->id,
                    'link_id' => $link->id,
                    'ip' => $faker->ipv4,
                    'user_agent' => $userAgent,
                    'referrer' => $faker->randomElement($referrers),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                
                $totalVisitantesCreated++;
                
                // 80% de chance de criar informações bancárias para este visitante
                if ($faker->boolean(80)) {
                    // Cria entre 1 e 3 informações bancárias para cada visitante
                    $quantidadeInfos = $faker->numberBetween(1, 3);
                    
                    for ($j = 0; $j < $quantidadeInfos; $j++) {
                        // Dados bancários mais realistas
                        $nomeBanco = $faker->randomElement($bancos);
                        $tipoConta = $faker->randomElement(['corrente', 'poupança', 'salário', 'conjunta']);
                        $status = $faker->randomElement($statusPossibilities);
                        $valor = $faker->randomFloat(2, 50, 15000);
                        
                        // Data da informação bancária (após a criação do visitante)
                        $dataInfo = $faker->dateTimeBetween($visitante->created_at, 'now');
                        
                        // Informações adicionais em formato JSON
                        $infoAdicionais = [
                            'valor' => $valor,
                            'motivo' => $faker->randomElement([
                                'Transferência', 'Pagamento', 'Empréstimo', 'Financiamento', 
                                'Depósito', 'Investimento', 'Saque', 'Estorno', 'Adiantamento'
                            ]),
                            'status' => $status,
                            'tipo_conta' => $tipoConta,
                            'banco' => $nomeBanco,
                            'comprovante_id' => $faker->numerify('COMP-######'),
                            'detalhes' => $faker->boolean(30) ? $faker->sentence(rand(5, 10)) : null
                        ];
                        
                        // Adicionar campos extras aleatoriamente
                        if ($faker->boolean(20)) {
                            $infoAdicionais['pix_key'] = $faker->randomElement([
                                $faker->email, 
                                $faker->numerify('###.###.###-##'), // CPF
                                $faker->numerify('(##) #####-####'), // Telefone
                                $faker->uuid // Chave aleatória
                            ]);
                        }
                        
                        if ($faker->boolean(30)) {
                            $infoAdicionais['taxa'] = $faker->randomFloat(2, 0, 50);
                        }
                        
                        InformacaoBancaria::create([
                            'visitante_uuid' => $visitante->uuid,
                            'data' => $dataInfo,
                            'agencia' => $faker->numerify('####'),
                            'conta' => $faker->numerify('#####-#'),
                            'cpf' => $faker->numerify('###.###.###-##'),
                            'nome_completo' => $faker->name,
                            'telefone' => $faker->numerify('(##) #####-####'),
                            'informacoes_adicionais' => $infoAdicionais,
                            'created_at' => $dataInfo,
                            'updated_at' => $dataInfo,
                        ]);
                        
                        $totalInfoBancariasCreated++;
                    }
                }
            }
        }
        
        $this->command->info("✓ Total de {$totalVisitantesCreated} visitantes e {$totalInfoBancariasCreated} informações bancárias criados com sucesso!");
    }
}
