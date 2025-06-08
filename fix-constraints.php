<?php

require 'vendor/autoload.php';

// Iniciar aplicação Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Usar DB para manipulação direta do banco
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\BankTemplate;
use App\Models\Usuario;

echo "Iniciando correção das restrições de chave estrangeira...\n";

try {
    // 1. Verificar se existem os templates de banco
    echo "Verificando templates de banco...\n";
    $bankTemplates = BankTemplate::count();
    
    if ($bankTemplates == 0) {
        echo "Nenhum template de banco encontrado. Executando seeder...\n";
        
        // Executar o BankTemplateSeeder
        $seeder = new \Database\Seeders\BankTemplateSeeder();
        $seeder->run();
        
        echo "Templates de banco criados com sucesso!\n";
    } else {
        echo "Encontrados {$bankTemplates} templates de banco.\n";
    }
    
    // Verificar especificamente o template de ID 1
    $template = BankTemplate::find(1);
    if (!$template) {
        echo "Template ID 1 não encontrado. Criando template padrão...\n";
        $template = BankTemplate::create([
            'name' => 'Template Padrão',
            'slug' => 'template-padrao',
            'description' => 'Template padrão para correção de chave estrangeira',
            'active' => true
        ]);
        echo "Template ID {$template->id} criado com sucesso!\n";
    }
    
    // 2. Verificar usuários
    echo "\nVerificando usuários...\n";
    $user = Usuario::find(2);
    if (!$user) {
        echo "Usuário ID 2 não encontrado. Criando usuário cliente...\n";
        $user = new Usuario();
        $user->id = 2; // forçar ID 2
        $user->name = 'Cliente Padrão';
        $user->email = 'cliente@example.com';
        $user->password = bcrypt('password');
        $user->nivel = 'cliente';
        $user->save();
        
        echo "Usuário ID {$user->id} criado com sucesso!\n";
    } else {
        echo "Usuário ID 2 existe: {$user->name}\n";
    }
    
    // 3. Verificar registro DNS específico
    echo "\nVerificando registro DNS ID 1...\n";
    $dnsRecord = DB::table('dns_records')->where('id', 1)->first();
    if ($dnsRecord) {
        echo "Encontrado registro DNS ID 1\n";
        
        // Garantir que os relacionamentos são válidos
        DB::table('dns_records')
            ->where('id', 1)
            ->update([
                'external_api_id' => 1,
                'bank_template_id' => $template->id,
                'user_id' => 2,
                'updated_at' => now()
            ]);
        
        echo "Atualizado registro DNS com chaves estrangeiras válidas!\n";
    } else {
        echo "Registro DNS ID 1 não encontrado.\n";
    }
    
    echo "\nProcesso de correção concluído com sucesso!\n";

} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
