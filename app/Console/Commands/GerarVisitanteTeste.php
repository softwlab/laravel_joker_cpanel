<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visitante;
use App\Models\LinkGroupItem;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class GerarVisitanteTeste extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitante:gerar {quantidade=1} {--link_id=} {--usuario_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gerar visitantes de teste com ou sem informações bancárias';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $quantidade = $this->argument('quantidade');
        $link_id = $this->option('link_id');
        $usuario_id = $this->option('usuario_id');
        
        $faker = Faker::create('pt_BR');
        
        $this->info("Gerando {$quantidade} visitantes de teste...");
        
        $bar = $this->output->createProgressBar($quantidade);
        
        DB::beginTransaction();
        
        try {
            for ($i = 0; $i < $quantidade; $i++) {
                // Se não foi fornecido link_id, escolhe um aleatório
                if (!$link_id) {
                    if ($usuario_id) {
                        $linkItem = LinkGroupItem::whereHas('group', function($q) use ($usuario_id) {
                            $q->where('usuario_id', $usuario_id);
                        })->inRandomOrder()->first();
                    } else {
                        $linkItem = LinkGroupItem::inRandomOrder()->first();
                    }
                    
                    if ($linkItem) {
                        $link_id = $linkItem->id;
                        $usuario_id = $linkItem->group->usuario_id;
                    } else {
                        $this->error("Nenhum link encontrado para criar visitantes.");
                        DB::rollBack();
                        return 1;
                    }
                } else {
                    // Se foi fornecido link_id, verifica se existe
                    $linkItem = LinkGroupItem::find($link_id);
                    if (!$linkItem) {
                        $this->error("Link ID {$link_id} não encontrado.");
                        DB::rollBack();
                        return 1;
                    }
                    $usuario_id = $linkItem->group->usuario_id;
                }
                
                // Cria o visitante
                $visitante = Visitante::create([
                    'usuario_id' => $usuario_id,
                    'link_id' => $link_id,
                    'ip' => $faker->ipv4,
                    'user_agent' => $faker->userAgent,
                    'referrer' => $faker->randomElement(['', 'https://google.com', 'https://facebook.com', null]),
                    'created_at' => $faker->dateTimeBetween('-30 days', 'now'),
                ]);
                
                // 50% de chance de criar informações bancárias para o visitante
                if ($faker->boolean(50)) {
                    $visitante->informacoes()->create([
                        'data' => $faker->dateTimeBetween('-30 days', 'now'),
                        'agencia' => $faker->numerify('####'),
                        'conta' => $faker->numerify('#####-#'),
                        'cpf' => $faker->numerify('###.###.###-##'),
                        'nome_completo' => $faker->name,
                        'telefone' => $faker->numerify('(##) #####-####'),
                        'informacoes_adicionais' => [
                            'valor' => $faker->randomFloat(2, 100, 10000),
                            'motivo' => $faker->sentence(4),
                            'status' => $faker->randomElement(['pendente', 'aprovado', 'rejeitado'])
                        ]
                    ]);
                }
                
                $bar->advance();
            }
            
            DB::commit();
            $bar->finish();
            
            $this->newLine();
            $this->info("✓ {$quantidade} visitantes de teste criados com sucesso!");
            
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Erro ao criar visitantes: " . $e->getMessage());
            return 1;
        }
    }
}
