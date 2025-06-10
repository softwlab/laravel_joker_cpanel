<?php

namespace App\Console\Commands;

use App\Models\Visitante;
use App\Models\DnsRecord;
use App\Models\LinkGroupItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MigrateLinksToDnsRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jokerlab:migrate-links-to-dns {--force : Forçar migração sem confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra visitantes do sistema antigo de links para o novo sistema baseado em DNS Records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migração de visitantes do sistema antigo para o sistema DNS...');
        
        // Buscar visitantes que não foram migrados ainda e têm link_id, mas não dns_record_id
        $visitantes = Visitante::whereNotNull('link_id')
                        ->whereNull('dns_record_id')
                        ->where('migrated_to_dns', false)
                        ->get();
        
        $totalVisitantes = $visitantes->count();
        
        if ($totalVisitantes === 0) {
            $this->info('Não há visitantes para migrar. Todos os registros já foram processados.');
            return 0;
        }
        
        $this->info("Total de {$totalVisitantes} visitantes para migrar.");
        
        if (!$this->option('force')) {
            if (!$this->confirm('Deseja continuar com a migração?')) {
                $this->info('Migração cancelada pelo usuário.');
                return 1;
            }
        }
        
        $progressBar = $this->output->createProgressBar($totalVisitantes);
        $progressBar->start();
        
        $migrated = 0;
        $errors = 0;
        
        DB::beginTransaction();
        
        try {
            foreach ($visitantes as $visitante) {
                // Buscar o LinkGroupItem associado ao visitante
                $linkItem = LinkGroupItem::find($visitante->link_id);
                
                if (!$linkItem) {
                    $this->error("Link ID {$visitante->link_id} não encontrado para o visitante {$visitante->id}");
                    $errors++;
                    $progressBar->advance();
                    continue;
                }
                
                // Verificar se existe um DnsRecord para o mesmo usuário
                // Estratégia: associar ao primeiro DNS record do mesmo usuário
                // Caso real exigiria uma regra de negócio específica para mapear links antigos aos DNS records
                $dnsRecord = DnsRecord::where('usuario_id', $visitante->usuario_id)->first();
                
                if (!$dnsRecord) {
                    // Criar um DNS Record para o usuário se não existir
                    $dnsRecord = DnsRecord::create([
                        'usuario_id' => $visitante->usuario_id,
                        'name' => 'Migrado de ' . $linkItem->title,
                        'domain' => 'migrado-' . $linkItem->id . '.exemplo.com',
                        'cloudflare_id' => 'migrado-' . $linkItem->id,
                        'active' => true
                    ]);
                }
                
                // Atualizar o visitante com o dns_record_id
                $visitante->dns_record_id = $dnsRecord->id;
                $visitante->migrated_to_dns = true;
                $visitante->save();
                
                $migrated++;
                $progressBar->advance();
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Erro durante a migração: " . $e->getMessage());
            Log::error("Erro na migração de links para DNS: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Migração concluída: {$migrated} visitantes migrados, {$errors} erros.");
        
        return 0;
    }
}
