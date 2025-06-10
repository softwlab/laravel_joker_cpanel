<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\CloudflareDomain;
use App\Models\DnsRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class DomainService
{
    /**
     * Obtém dados de diagnóstico para um usuário específico
     *
     * @param int $userId ID do usuário
     * @return array Dados de diagnóstico
     */
    public function getUserDomainDiagnostics($userId)
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
        
        return [
            'user' => $user,
            'pivotData' => $pivotData,
            'allDomains' => $allDomains,
            'userDnsRecords' => $userDnsRecords
        ];
    }
    
    /**
     * Associa um domínio a um usuário
     *
     * @param int $userId ID do usuário
     * @param int $domainId ID do domínio Cloudflare
     * @return array Resultado da operação
     */
    public function associateDomain($userId, $domainId)
    {
        // Verificar se já existe associação
        $exists = DB::table('cloudflare_domain_usuario')
            ->where('usuario_id', $userId)
            ->where('cloudflare_domain_id', $domainId)
            ->exists();
            
        if ($exists) {
            return [
                'success' => false,
                'message' => 'Este domínio já está associado a este usuário.'
            ];
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
        
        return [
            'success' => true,
            'message' => 'Domínio associado com sucesso!'
        ];
    }
    
    /**
     * Desassocia um domínio de um usuário
     *
     * @param int $userId ID do usuário
     * @param int $domainId ID do domínio Cloudflare
     * @return array Resultado da operação
     */
    public function dissociateDomain($userId, $domainId)
    {
        // Verificar se existe a associação
        $exists = DB::table('cloudflare_domain_usuario')
            ->where('usuario_id', $userId)
            ->where('cloudflare_domain_id', $domainId)
            ->exists();
            
        if (!$exists) {
            return [
                'success' => false,
                'message' => 'Este domínio não está associado a este usuário.'
            ];
        }
        
        // Remover a associação
        DB::table('cloudflare_domain_usuario')
            ->where('usuario_id', $userId)
            ->where('cloudflare_domain_id', $domainId)
            ->delete();
        
        Log::info("Associação removida: Usuário $userId com Domínio $domainId");
        
        return [
            'success' => true,
            'message' => 'Domínio desassociado com sucesso!'
        ];
    }
    
    /**
     * Obtém todos os domínios disponíveis para associação
     *
     * @return Collection Coleção de domínios Cloudflare
     */
    public function getAllDomains()
    {
        return CloudflareDomain::all();
    }
    
    /**
     * Obtém domínios associados a um usuário específico
     *
     * @param int $userId ID do usuário
     * @return Collection Coleção de domínios associados
     */
    public function getUserDomains($userId)
    {
        $user = Usuario::findOrFail($userId);
        return $user->cloudflareDomains;
    }
}
