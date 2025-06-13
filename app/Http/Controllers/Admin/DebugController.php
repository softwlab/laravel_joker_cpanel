<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebugController extends Controller
{
    public function checkBankTemplates()
    {
        try {
            // Obter todos os templates de banco
            $templates = BankTemplate::all();
            
            // Criar um log detalhado
            $log = "==== DADOS DA TABELA BANK_TEMPLATES ====\n";
            $log .= "Total de registros: " . $templates->count() . "\n\n";
            
            foreach ($templates as $template) {
                $log .= "ID: " . $template->id . "\n";
                $log .= "Nome: " . $template->name . "\n";
                $log .= "Slug: " . $template->slug . "\n";
                $log .= "Ativo: " . ($template->active ? 'Sim' : 'Não') . " (valor raw: " . var_export($template->getAttributes()['active'], true) . ")\n";
                $log .= "Multipágina: " . ($template->is_multipage ? 'Sim' : 'Não') . " (valor raw: " . var_export($template->getAttributes()['is_multipage'], true) . ")\n";
                $log .= "-------------------------\n";
            }
            
            // Verificar estrutura da tabela
            $columns = DB::select('PRAGMA table_info(bank_templates)');
            $log .= "\n==== ESTRUTURA DA TABELA ====\n";
            foreach ($columns as $column) {
                $log .= "{$column->name} ({$column->type})\n";
            }
            
            // Salvar log em arquivo
            file_put_contents(
                storage_path('logs/debug_bank_structure.log'), 
                $log,
                FILE_APPEND
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Verificação concluída. Verifique o arquivo debug_bank_structure.log'
            ]);
        } catch (\Exception $e) {
            file_put_contents(
                storage_path('logs/debug_bank_structure.log'), 
                "ERRO: " . $e->getMessage() . "\n" . $e->getTraceAsString(),
                FILE_APPEND
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ]);
        }
    }
    
    public function testCreate()
    {
        try {
            // Criar um template de teste diretamente
            $template = new BankTemplate();
            $template->name = 'Banco Teste ' . time();
            $template->slug = 'teste-' . time();
            $template->description = 'Template criado pelo DebugController';
            $template->active = true;
            $template->is_multipage = true;
            $template->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Template criado com ID: ' . $template->id,
                'template' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
