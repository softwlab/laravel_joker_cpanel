<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\CloudflareDomain;
use App\Models\DnsRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DomainDebugController extends Controller
{
    public function showDebug($userId)
    {
        $user = Usuario::findOrFail($userId);
        
        // Consulta direta na tabela pivot
        $pivotData = DB::table('cloudflare_domain_usuario')
            ->where('usuario_id', $userId)
            ->get();
            
        // Todos os domínios disponíveis
        $allDomains = CloudflareDomain::all();
        
        // Registros DNS associados ao usuário
        $userDnsRecords = DnsRecord::where('user_id', $userId)->get();
        
        // Log para diagnóstico
        Log::info('Executando diagnóstico para usuário ' . $userId);
        Log::info('Registros na tabela pivot: ' . $pivotData->count());
        
        return view('admin.domain-debug', compact('user', 'pivotData', 'allDomains', 'userDnsRecords'));
    }
    
    public function associateDomain(Request $request)
    {
        // Validação
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'cloudflare_domain_id' => 'required|exists:cloudflare_domains,id',
        ]);
        
        $userId = $request->input('usuario_id');
        $domainId = $request->input('cloudflare_domain_id');
        
        // Verificar se já existe associação
        $exists = DB::table('cloudflare_domain_usuario')
            ->where('usuario_id', $userId)
            ->where('cloudflare_domain_id', $domainId)
            ->exists();
            
        if ($exists) {
            return redirect()->back()->with('info', 'Este domínio já está associado a este usuário.');
        }
        
        // Criar associação
        DB::table('cloudflare_domain_usuario')->insert([
            'usuario_id' => $userId,
            'cloudflare_domain_id' => $domainId,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        Log::info("Associação criada: Usuário $userId com Domínio $domainId");
        
        return redirect()->back()->with('success', 'Domínio associado com sucesso!');
    }
}
