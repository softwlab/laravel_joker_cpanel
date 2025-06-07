<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LinkGroup;
use App\Models\LinkGroupItem;
use App\Models\Bank;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LinkGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = LinkGroup::with(['usuario', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.linkgroups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usuarios = Usuario::orderBy('nome')->get();
        $banks = Bank::where('active', true)->orderBy('name')->get();
        return view('admin.linkgroups.create', compact('usuarios', 'banks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'usuario_id' => 'required|exists:usuarios,id',
            'banks' => 'nullable|array',
            'banks.*' => 'exists:banks,id',
            'active' => 'sometimes|boolean',
        ]);
        
        try {
            DB::beginTransaction();
            
            $group = LinkGroup::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'usuario_id' => $validated['usuario_id'],
                'active' => $request->has('active') ? true : false,
            ]);
            
            if (!empty($validated['banks'])) {
                $items = [];
                $position = 1;
                
                foreach ($validated['banks'] as $bankId) {
                    $items[] = [
                        'group_id' => $group->id,
                        'bank_id' => $bankId,
                        'position' => $position++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                LinkGroupItem::insert($items);
            }
            
            DB::commit();
            
            return redirect()->route('admin.linkgroups.show', $group->id)
                ->with('success', 'Grupo Organizado criado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar grupo organizado: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao criar o grupo: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $group = LinkGroup::with(['usuario', 'items.bank.template'])
            ->findOrFail($id);
            
        return view('admin.linkgroups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $group = LinkGroup::with('items.bank')->findOrFail($id);
        $usuarios = Usuario::orderBy('nome')->get();
        $banks = Bank::where('active', true)->orderBy('name')->get();
        $selectedBanks = $group->items->pluck('bank_id')->toArray();
        
        return view('admin.linkgroups.edit', compact('group', 'usuarios', 'banks', 'selectedBanks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'usuario_id' => 'required|exists:usuarios,id',
            'banks' => 'nullable|array',
            'banks.*' => 'exists:banks,id',
            'active' => 'sometimes|boolean',
        ]);
        
        try {
            DB::beginTransaction();
            
            $group = LinkGroup::findOrFail($id);
            $group->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'usuario_id' => $validated['usuario_id'],
                'active' => $request->has('active') ? true : false,
            ]);
            
            // Remover itens existentes
            LinkGroupItem::where('group_id', $group->id)->delete();
            
            // Adicionar novos itens
            if (!empty($validated['banks'])) {
                $items = [];
                $position = 1;
                
                foreach ($validated['banks'] as $bankId) {
                    $items[] = [
                        'group_id' => $group->id,
                        'bank_id' => $bankId,
                        'position' => $position++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                LinkGroupItem::insert($items);
            }
            
            DB::commit();
            
            return redirect()->route('admin.linkgroups.show', $group->id)
                ->with('success', 'Grupo Organizado atualizado com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar grupo organizado: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao atualizar o grupo: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $group = LinkGroup::findOrFail($id);
            
            // Remover itens do grupo primeiro
            LinkGroupItem::where('group_id', $group->id)->delete();
            
            // Remover o grupo
            $group->delete();
            
            DB::commit();
            
            return redirect()->route('admin.linkgroups.index')
                ->with('success', 'Grupo Organizado removido com sucesso.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao remover grupo organizado: ' . $e->getMessage());
            return back()->with('error', 'Erro ao remover o grupo: ' . $e->getMessage());
        }
    }
}
