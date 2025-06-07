<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visitante;
use App\Models\InformacaoBancaria;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class GerarInformacoesTeste extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'informacoes:gerar {visitante_uuid?} {--quantidade=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera informações bancárias de teste para um visitante existente ou para visitantes aleatórios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $visitante_uuid = $this->argument('visitante_uuid');
        $quantidade = $this->option('quantidade');
        
        $faker = Faker::create('pt_BR');
        
        if ($visitante_uuid) {
            // Verificar se o visitante existe
            $visitante = Visitante::where('uuid', $visitante_uuid)->first();
            
            if (!$visitante) {
                $this->error("Visitante com UUID {$visitante_uuid} não encontrado.");
                return 1;
            }
            
            $this->info("Gerando {$quantidade} informações bancárias para o visitante #{$visitante->id}...");
            
            // Gerar informações para este visitante específico
            for ($i = 0; $i < $quantidade; $i++) {
                $this->criarInformacaoBancaria($visitante, $faker);
            }
            
            $this->info("✓ {$quantidade} informações bancárias geradas para o visitante #{$visitante->id}");
        } else {
            // Selecionar visitantes aleatórios sem informações bancárias
            $visitantes = Visitante::doesntHave('informacoes')
                ->inRandomOrder()
                ->limit($quantidade)
                ->get();
            
            if ($visitantes->isEmpty()) {
                $this->error("Nenhum visitante encontrado sem informações bancárias.");
                return 1;
            }
            
            $this->info("Gerando informações bancárias para {$visitantes->count()} visitantes...");
            
            $bar = $this->output->createProgressBar($visitantes->count());
            
            foreach ($visitantes as $visitante) {
                $this->criarInformacaoBancaria($visitante, $faker);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("✓ Informações bancárias geradas com sucesso!");
        }
        
        return 0;
    }
    
    /**
     * Cria uma informação bancária de teste para um visitante
     */
    protected function criarInformacaoBancaria($visitante, $faker)
    {
        return InformacaoBancaria::create([
            'visitante_uuid' => $visitante->uuid,
            'data' => $faker->dateTimeBetween('-30 days', 'now'),
            'agencia' => $faker->numerify('####'),
            'conta' => $faker->numerify('#####-#'),
            'cpf' => $faker->numerify('###.###.###-##'),
            'nome_completo' => $faker->name,
            'telefone' => $faker->numerify('(##) #####-####'),
            'informacoes_adicionais' => [
                'valor' => $faker->randomFloat(2, 100, 10000),
                'motivo' => $faker->sentence(4),
                'status' => $faker->randomElement(['pendente', 'aprovado', 'rejeitado']),
                'tipo_conta' => $faker->randomElement(['corrente', 'poupança']),
                'banco' => $faker->randomElement(['Banco do Brasil', 'Itaú', 'Bradesco', 'Caixa', 'Santander', 'Nubank'])
            ]
        ]);
    }
}
