<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDuplicateBankFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:clean-duplicate-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove campos duplicados da tabela bank_fields';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando limpeza de campos duplicados...');

        // Obter todas as combinações únicas
        $uniqueFields = DB::table('bank_fields')
            ->select('bank_template_id', 'field_key', 'field_type', DB::raw('MIN(id) as keep_id'))
            ->groupBy('bank_template_id', 'field_key', 'field_type')
            ->get();

        // Array para manter IDs que queremos preservar
        $keepIds = $uniqueFields->pluck('keep_id')->toArray();

        $this->info('Total de campos únicos: ' . count($keepIds));

        // Excluir todos os registros exceto os que queremos manter
        $deleted = DB::table('bank_fields')
            ->whereNotIn('id', $keepIds)
            ->delete();

        $this->info('Limpeza concluída!');
        $this->info("Registros duplicados excluídos: {$deleted}");

        return 0;
    }
}
