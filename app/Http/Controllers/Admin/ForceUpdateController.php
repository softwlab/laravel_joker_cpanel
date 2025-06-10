<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DnsRecord;
use App\Services\DnsStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para forçar a atualização dos registros DNS
 * quando há problemas com o processo normal de atualização.
 */
class ForceUpdateController extends Controller
{
    protected $dnsStats;
    
    public function __construct(DnsStatisticsService $dnsStats)
    {
        $this->dnsStats = $dnsStats;
    }
    
    /**
     * Atualiza diretamente o campo bank_template_id no registro DNS
     * usando DB Query Builder para evitar problemas com Eloquent ORM.
     */
    public function updateDnsTemplate(Request $request, $id)
    {
        // Validar a requisição
        $request->validate([
            'bank_template_id' => 'nullable|exists:bank_templates,id'
        ]);
        
        // Verificar se o registro existe
        $record = DnsRecord::find($id);
        if (!$record) {
            return redirect()->route('admin.dns-records.index')
                ->with('error', 'Registro DNS não encontrado.');
        }
        
        try {
            // Registrar valores atuais
            Log::info('ForceUpdateController: Atualizando template do registro DNS', [
                'id' => $id,
                'template_atual' => $record->bank_template_id,
                'template_novo' => $request->input('bank_template_id')
            ]);
            
            // Atualizar diretamente pelo query builder
            $templateId = $request->input('bank_template_id');
            DB::table('dns_records')
                ->where('id', $id)
                ->update(['bank_template_id' => $templateId ?: null]);
                
            // Invalidar cache
            if (method_exists($this->dnsStats, 'invalidateCache')) {
                $this->dnsStats->invalidateCache($id);
            }
            
            // Verificar se a atualização foi bem-sucedida
            $updatedRecord = DB::table('dns_records')->where('id', $id)->first();
            Log::info('ForceUpdateController: Template atualizado com sucesso', [
                'id' => $id,
                'template_atualizado' => $updatedRecord->bank_template_id
            ]);
            
            return redirect()->route('admin.dns-records.show', $id)
                ->with('success', 'Template bancário atualizado com sucesso!');
                
        } catch (\Exception $e) {
            Log::error('ForceUpdateController: Erro ao atualizar template', [
                'id' => $id,
                'erro' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.dns-records.edit', $id)
                ->with('error', 'Erro ao atualizar o template bancário: ' . $e->getMessage());
        }
    }
}
