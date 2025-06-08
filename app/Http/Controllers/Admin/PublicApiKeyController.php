<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PublicApiKey;
use App\Models\PublicApiKeyLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PublicApiKeyController extends Controller
{
    /**
     * Exibe a lista de chaves de API públicas
     */
    public function index()
    {
        $apiKeys = PublicApiKey::withCount('logs')->orderBy('created_at', 'desc')->get();
        
        return view('admin.api_keys.index', compact('apiKeys'));
    }
    
    /**
     * Mostra o formulário para criar uma nova chave de API
     */
    public function create()
    {
        return view('admin.api_keys.create');
    }
    
    /**
     * Armazena uma nova chave de API
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Gerar nova chave
        $key = PublicApiKey::generateKey();
        
        // Criar registro no banco
        $apiKey = PublicApiKey::create([
            'name' => $request->name,
            'key' => $key,
            'description' => $request->description,
            'active' => true,
        ]);
        
        // Registrar log de criação
        $apiKey->logAction('created', ['admin_id' => Auth::id()], Auth::id());
        
        return redirect()->route('admin.api_keys.index')
                         ->with('success', 'Chave de API criada com sucesso.');
    }
    
    /**
     * Exibe detalhes de uma chave de API
     */
    public function show(PublicApiKey $apiKey)
    {
        // Carregar logs relacionados
        $logs = $apiKey->logs()->with('admin')->latest()->paginate(15);
        
        return view('admin.api_keys.show', compact('apiKey', 'logs'));
    }
    
    /**
     * Mostra o formulário para editar uma chave de API
     */
    public function edit(PublicApiKey $apiKey)
    {
        return view('admin.api_keys.edit', compact('apiKey'));
    }
    
    /**
     * Atualiza uma chave de API
     */
    public function update(Request $request, PublicApiKey $apiKey)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $oldStatus = $apiKey->active;
        $newStatus = (bool)$request->active;
        
        $apiKey->update([
            'name' => $request->name,
            'description' => $request->description,
            'active' => $newStatus,
        ]);
        
        // Registrar log de atualização
        $details = [
            'name_changed' => $request->name != $apiKey->getOriginal('name'),
            'description_changed' => $request->description != $apiKey->getOriginal('description'),
            'status_changed' => $oldStatus !== $newStatus,
        ];
        
        $apiKey->logAction('updated', $details, Auth::id());
        
        return redirect()->route('admin.api_keys.index')
                         ->with('success', 'Chave de API atualizada com sucesso.');
    }
    
    /**
     * Remove uma chave de API (soft delete)
     */
    public function destroy(PublicApiKey $apiKey)
    {
        // Registrar log antes de excluir
        $apiKey->logAction('deleted', null, Auth::id());
        
        $apiKey->delete();
        
        return redirect()->route('admin.api_keys.index')
                         ->with('success', 'Chave de API removida com sucesso.');
    }
    
    /**
     * Regenera uma chave de API existente
     */
    public function regenerate(PublicApiKey $apiKey)
    {
        // Guardar a chave antiga para o log
        $oldKey = $apiKey->key;
        
        // Gerar nova chave
        $newKey = PublicApiKey::generateKey();
        
        // Atualizar a chave
        $apiKey->update([
            'key' => $newKey
        ]);
        
        // Registrar log
        $apiKey->logAction('regenerated', [
            'old_key_fragment' => substr($oldKey, 0, 8) . '...',
            'admin_id' => Auth::id()
        ], Auth::id());
        
        return redirect()->route('admin.api_keys.show', $apiKey->id)
                         ->with('success', 'Chave de API regenerada com sucesso.');
    }
}
