<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Bank;
use App\Models\CloudflareDomain;
use App\Models\DnsRecord;
use App\Models\Acesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = Usuario::count();
        $totalBanks = Bank::count();
        $recentAccess = Acesso::with('usuario')
            ->orderBy('data_acesso', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'totalBanks', 'recentAccess'));
    }

    public function users()
    {
        $users = Usuario::with(['banks', 'acessos'])
            ->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function showUser($id)
    {
        Log::info('Acessando usuário ID: ' . $id);
        
        // Tentando identificar associações diretas na tabela pivot
        $associacoesDiretas = DB::table('cloudflare_domain_usuario')
                ->where('usuario_id', $id)
                ->get();
        
        Log::info('Associações diretas na tabela pivot: ' . $associacoesDiretas->count());
        
        // Carregando domínios diretamente da tabela pivot para ter certeza
        $dominiosIds = $associacoesDiretas->pluck('cloudflare_domain_id')->toArray();
        $dominiosAssociados = collect([]);
        
        if (!empty($dominiosIds)) {
            $dominiosAssociados = CloudflareDomain::whereIn('id', $dominiosIds)->get();
            Log::info('Domínios encontrados por consulta direta: ' . $dominiosAssociados->count());
        }
        
        // Carregando registros DNS associados ao usuário com todas as suas relações
        $dnsRecords = DnsRecord::where('user_id', $id)
            ->with(['externalApi', 'bank', 'bankTemplate', 'linkGroup'])
            ->get();
        Log::info('Registros DNS associados ao usuário: ' . $dnsRecords->count());
        
        // Carregando usuário com suas relações
        $user = Usuario::with([
            'acessos', 
            'userConfig', 
            'linkGroups.items', 
            'cloudflareDomains'
        ])->findOrFail($id);
        
        // Verificando os domínios carregados pelo Eloquent
        Log::info('Domínios Cloudflare carregados pelo Eloquent: ' . $user->cloudflareDomains->count());
        if ($user->cloudflareDomains->count() > 0) {
            foreach ($user->cloudflareDomains as $domain) {
                Log::info('Domínio carregado: ID=' . $domain->id . ', Nome=' . $domain->name . ', Status=' . $domain->pivot->status);
            }
        } else {
            Log::warning('Nenhum domínio Cloudflare encontrado para o usuário, mesmo após eager loading');
        }
        
        return view('admin.user-details', compact('user', 'dominiosAssociados', 'dnsRecords'));
    }

    public function createUser()
    {
        return view('admin.create-user');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'senha' => 'required|string|min:6',
            'nivel' => 'required|in:admin,cliente',
            'ativo' => 'boolean'
        ]);

        $validated['senha'] = Hash::make($validated['senha']);
        $validated['ativo'] = $request->has('ativo');

        Usuario::create($validated);

        return redirect()->route('admin.users')
            ->with('success', 'Usuário criado com sucesso');
    }

    public function editUser($id)
    {
        $user = Usuario::findOrFail($id);
        return view('admin.edit-user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = Usuario::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $user->id,
            'nivel' => 'required|in:admin,cliente',
            'ativo' => 'boolean'
        ]);

        if ($request->filled('senha')) {
            $validated['senha'] = Hash::make($request->senha);
        }

        $validated['ativo'] = $request->has('ativo');

        $user->update($validated);

        return redirect()->route('admin.users')
            ->with('success', 'Usuário atualizado com sucesso');
    }

    public function deleteUser($id)
    {
        $user = Usuario::findOrFail($id);
        
        // Não permitir deletar o próprio usuário
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Você não pode deletar seu próprio usuário');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Usuário deletado com sucesso');
    }

    public function banks()
    {
        $banks = Bank::with('usuario')->paginate(15);
        return view('admin.banks', compact('banks'));
    }

    public function logs()
    {
        $logs = Acesso::with('usuario')
            ->orderBy('data_acesso', 'desc')
            ->paginate(20);

        return view('admin.logs', compact('logs'));
    }
    
    public function createBank()
    {
        $usuarios = Usuario::where('nivel', 'cliente')->where('ativo', true)->get();
        return view('admin.create-bank', compact('usuarios'));
    }
    
    public function storeBank(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:banks,slug',
            'description' => 'nullable|string',
            'url' => 'nullable|string|url',
            'active' => 'boolean',
            'usuario_id' => 'required|exists:usuarios,id'
        ]);
        
        $validated['active'] = $request->has('active');
        
        // JSON encoded links data if provided
        if ($request->filled('links')) {
            $validated['links'] = json_encode($request->links);
        }
        
        Bank::create($validated);
        
        return redirect()->route('admin.banks')
            ->with('success', 'Banco criado com sucesso');
    }
    
    public function showBank($id)
    {
        $bank = Bank::with('usuario')->findOrFail($id);
        return view('admin.bank-details', compact('bank'));
    }
    
    public function editBank($id)
    {
        $bank = Bank::findOrFail($id);
        $usuarios = Usuario::where('nivel', 'cliente')->where('ativo', true)->get();
        return view('admin.edit-bank', compact('bank', 'usuarios'));
    }
    
    public function updateBank(Request $request, $id)
    {
        $bank = Bank::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:banks,slug,' . $bank->id,
            'description' => 'nullable|string',
            'url' => 'nullable|string|url',
            'active' => 'boolean',
            'usuario_id' => 'required|exists:usuarios,id'
        ]);
        
        $validated['active'] = $request->has('active');
        
        // JSON encoded links data if provided
        if ($request->filled('links')) {
            $validated['links'] = json_encode($request->links);
        }
        
        $bank->update($validated);
        
        return redirect()->route('admin.banks')
            ->with('success', 'Banco atualizado com sucesso');
    }
    
    public function deleteBank($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->delete();
        
        return redirect()->route('admin.banks')
            ->with('success', 'Banco deletado com sucesso');
    }
}
