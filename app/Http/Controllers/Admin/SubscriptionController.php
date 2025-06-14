<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Usuario;
use App\Models\DnsRecord;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['user', 'dnsRecords']);
        
        // Filtragem por busca
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('uuid', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $subscriptions = $query->latest()->paginate(10);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = Usuario::all();
        $dnsRecords = DnsRecord::all();
        return view('admin.subscriptions.create', compact('users', 'dnsRecords'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:usuarios,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,expired',
            'dns_records' => 'nullable|array',
            'dns_records.*' => 'exists:dns_records,id',
        ]);

        try {
            DB::beginTransaction();
            
            // Formata o valor monetário - converte de R$ 1.234,56 para 1234.56
            $value = $request->value;
            $value = str_replace('.', '', $value); // Remove pontos
            $value = str_replace(',', '.', $value); // Substitui vírgula por ponto
            
            $subscription = Subscription::create([
                'uuid' => Str::uuid(),
                'user_id' => $request->user_id,
                'name' => $request->name,
                'description' => $request->description,
                'value' => $value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
            ]);

            // Associar registros DNS se foram fornecidos
            if ($request->filled('dns_records')) {
                $subscription->dnsRecords()->attach($request->dns_records);
            }
            
            DB::commit();
            
            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Assinatura criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar assinatura: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subscription = Subscription::with(['user', 'dnsRecords'])->findOrFail($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $subscription = Subscription::with('dnsRecords')->findOrFail($id);
        $users = Usuario::all();
        $dnsRecords = DnsRecord::all();
        
        return view('admin.subscriptions.edit', compact('subscription', 'users', 'dnsRecords'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'user_id' => 'required|exists:usuarios,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,expired',
            'dns_records' => 'nullable|array',
            'dns_records.*' => 'exists:dns_records,id',
        ]);

        try {
            DB::beginTransaction();
            
            $subscription = Subscription::findOrFail($id);
            
            // Formata o valor monetário - converte de R$ 1.234,56 para 1234.56
            $value = $request->value;
            $value = str_replace('.', '', $value); // Remove pontos
            $value = str_replace(',', '.', $value); // Substitui vírgula por ponto
            
            $subscription->update([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'description' => $request->description,
                'value' => $value,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
            ]);
            
            // Sincroniza os registros DNS se fornecidos (remove os antigos e adiciona os novos)
            if ($request->filled('dns_records')) {
                $subscription->dnsRecords()->sync($request->dns_records);
            } else {
                // Se não foram fornecidos registros, remove todos os existentes
                $subscription->dnsRecords()->detach();
            }
            
            DB::commit();
            
            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Assinatura atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar assinatura: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $subscription = Subscription::findOrFail($id);
            
            // Remove as relações com registros DNS
            $subscription->dnsRecords()->detach();
            
            // Remove a assinatura
            $subscription->delete();
            
            DB::commit();
            
            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'Assinatura excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Erro ao excluir assinatura: ' . $e->getMessage());
        }
    }
}
