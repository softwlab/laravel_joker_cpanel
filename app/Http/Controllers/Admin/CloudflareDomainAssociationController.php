<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CloudflareDomain;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;

class CloudflareDomainAssociationController extends Controller
{
    /**
     * Exibe a lista de associações entre domínios Cloudflare e usuários
     */
    public function index()
    {
        $domains = CloudflareDomain::with('usuarios')->get();
        
        return view('admin.cloudflare.domain-associations.index', compact('domains'));
    }
    
    /**
     * Exibe o formulário para criar uma nova associação
     */
    public function create()
    {
        $domains = CloudflareDomain::all();
        $usuarios = Usuario::where('nivel', 'cliente')->where('ativo', true)->get();
        
        return view('admin.cloudflare.domain-associations.create', compact('domains', 'usuarios'));
    }
    
    /**
     * Armazena uma nova associação entre domínio e usuário
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cloudflare_domain_id' => 'required|exists:cloudflare_domains,id',
            'usuario_id' => 'required|exists:usuarios,id',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,paused,pending'
        ]);
        
        try {
            $domain = CloudflareDomain::findOrFail($validated['cloudflare_domain_id']);
            $usuario = Usuario::findOrFail($validated['usuario_id']);
            
            // Verifica se o relacionamento já existe
            if (!$domain->usuarios()->where('usuario_id', $usuario->id)->exists()) {
                $domain->usuarios()->attach($usuario->id, [
                    'status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                    'config' => json_encode([])
                ]);
                
                return redirect()->route('admin.cloudflare.domain-associations.index')
                    ->with('success', 'Associação criada com sucesso');
            } else {
                return redirect()->back()
                    ->with('error', 'Esta associação já existe')
                    ->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Erro ao criar associação de domínio Cloudflare: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Ocorreu um erro ao criar a associação')
                ->withInput();
        }
    }
    
    /**
     * Exibe os detalhes de uma associação específica
     */
    public function show($domainId, $usuarioId)
    {
        $domain = CloudflareDomain::findOrFail($domainId);
        $usuario = $domain->usuarios()->where('usuarios.id', $usuarioId)->firstOrFail();
        
        return view('admin.cloudflare.domain-associations.show', compact('domain', 'usuario'));
    }
    
    /**
     * Exibe o formulário para editar uma associação
     */
    public function edit($domainId, $usuarioId)
    {
        $domain = CloudflareDomain::findOrFail($domainId);
        $usuario = Usuario::findOrFail($usuarioId);
        $association = $domain->usuarios()->where('usuarios.id', $usuarioId)->firstOrFail();
        
        return view('admin.cloudflare.domain-associations.edit', compact('domain', 'usuario', 'association'));
    }
    
    /**
     * Atualiza uma associação existente
     */
    public function update(Request $request, $domainId, $usuarioId)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'status' => 'required|in:active,paused,pending'
        ]);
        
        try {
            $domain = CloudflareDomain::findOrFail($domainId);
            
            // Atualiza os dados do pivot
            $domain->usuarios()->updateExistingPivot($usuarioId, [
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null
            ]);
            
            return redirect()->route('admin.cloudflare.domain-associations.index')
                ->with('success', 'Associação atualizada com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar associação de domínio Cloudflare: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Ocorreu um erro ao atualizar a associação')
                ->withInput();
        }
    }
    
    /**
     * Remove uma associação
     */
    public function destroy($domainId, $usuarioId)
    {
        try {
            $domain = CloudflareDomain::findOrFail($domainId);
            $domain->usuarios()->detach($usuarioId);
            
            return redirect()->route('admin.cloudflare.domain-associations.index')
                ->with('success', 'Associação removida com sucesso');
        } catch (\Exception $e) {
            Log::error('Erro ao remover associação de domínio Cloudflare: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Ocorreu um erro ao remover a associação');
        }
    }
}
