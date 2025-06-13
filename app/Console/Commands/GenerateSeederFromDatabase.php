<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class GenerateSeederFromDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:generate-seeder {table : Nome da tabela para gerar o seeder} {--limit=100 : Limite de registros a incluir no seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera um seeder a partir dos dados existentes em uma tabela do banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $table = $this->argument('table');
        $limit = $this->option('limit');

        if (!Schema::hasTable($table)) {
            $this->error("A tabela '{$table}' não existe no banco de dados.");
            return 1;
        }

        $this->info("Gerando seeder para a tabela '{$table}'...");

        $data = DB::table($table)->limit($limit)->get();

        if ($data->isEmpty()) {
            $this->warn("A tabela '{$table}' não possui registros.");
            return 0;
        }

        $className = Str::studly(Str::singular($table)) . 'Seeder';
        $output = $this->generateSeederContent($className, $table, $data);
        
        $filePath = database_path("seeders/{$className}.php");
        
        if (File::exists($filePath) && !$this->confirm("O arquivo {$className}.php já existe. Deseja sobrescrevê-lo?")) {
            $this->info('Operação cancelada pelo usuário.');
            return 0;
        }
        
        File::put($filePath, $output);
        $this->info("Seeder gerado com sucesso em {$filePath}");
        
        return 0;
    }

    /**
     * Gera o conteúdo do seeder com base nos dados da tabela.
     *
     * @param string $className
     * @param string $table
     * @param \Illuminate\Support\Collection $data
     * @return string
     */
    protected function generateSeederContent($className, $table, $data)
    {
        $rows = [];
        
        foreach ($data as $row) {
            $values = [];
            
            foreach ((array) $row as $key => $value) {
                $formattedValue = $this->formatValue($value);
                $values[] = "'{$key}' => {$formattedValue}";
            }
            
            $rows[] = "            [\n                " . implode(",\n                ", $values) . "\n            ]";
        }
        
        $rowsString = implode(",\n", $rows);
        
        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$className} extends Seeder
{
    /**
     * Run the database seeds.
     * Gerado automaticamente a partir dos dados existentes.
     */
    public function run(): void
    {
        // Desativa as verificações de chaves estrangeiras temporariamente
        DB::statement('PRAGMA foreign_keys = OFF;');

        \$rows = [
{$rowsString}
        ];

        foreach (\$rows as \$row) {
            DB::table('{$table}')->insert(\$row);
        }

        // Reativa as verificações de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}
PHP;
    }

    /**
     * Formata o valor para inclusão no código PHP.
     *
     * @param mixed $value
     * @return string
     */
    protected function formatValue($value)
    {
        if (is_null($value)) {
            return 'null';
        }
        
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        if (is_numeric($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            // Escape single quotes and other special characters
            $value = str_replace("'", "\\'", $value);
            $value = str_replace("\n", "\\n", $value);
            return "'{$value}'";
        }
        
        if (is_array($value) || is_object($value)) {
            return "'" . json_encode($value) . "'";
        }
        
        return "'{$value}'";
    }
}
