<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Services\DnsStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the client's subscriptions
     */
    public function index(Request $request)
    {
        // Buscar apenas assinaturas do usuário logado
        $query = Subscription::where('user_id', Auth::id())->with('dnsRecords');
        
        // Filtragem por busca
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('uuid', 'like', "%{$search}%");
            });
        }
        
        // Filtragem por status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        $subscriptions = $query->latest()->paginate(10);
        return view('cliente.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Display the specified subscription
     */
    public function show(string $id, DnsStatisticsService $dnsStats)
    {
        // Buscar assinatura e verificar se pertence ao usuário logado
        $subscription = Subscription::with('dnsRecords')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        return view('cliente.subscriptions.show', compact('subscription'));
    }
}
