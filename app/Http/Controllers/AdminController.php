<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Acesso;
use App\Models\Usuario;
use App\Mail\UserApproved;
use App\Models\UserConfig;
use App\Models\Subscription;
use App\Mail\NovoUsuario;
use Illuminate\Http\Request;
use App\Models\DnsRecord;
use App\Models\CloudflareDomain;
use App\Models\ExternalApi;
use App\Models\TemplateUserConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\UserStatisticsService;
use App\Services\DnsStatisticsService;
use App\Models\Visitante;
use App\Models\InformacaoBancaria;
use App\Models\Bank;
use App\Models\BankTemplate;
use App\Models\ApiKey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = Usuario::count();
        $recentAccess = Acesso::with('usuario')
            ->orderBy('data_acesso', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'recentAccess'));
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
        
        // Obtendo as estatísticas do usuário através do serviço dedicado
        $userStatsService = new \App\Services\UserStatisticsService();
        $userStats = $userStatsService->getUserStats($id);
        
        // Tentando identificar associações diretas na tabela pivot
        $associacoesDiretas = DB::table('cloudflare_domain_usuario')
                ->where('usuario_id', $id)
                ->get();
        
        // Carregando domínios diretamente da tabela pivot para ter certeza
        $dominiosIds = $associacoesDiretas->pluck('cloudflare_domain_id')->toArray();
        $dominiosAssociados = collect([]);
        
        if (!empty($dominiosIds)) {
            $dominiosAssociados = CloudflareDomain::whereIn('id', $dominiosIds)->get();
        }
        
        // Carregando registros DNS associados ao usuário com todas as suas relações
        $dnsRecords = DnsRecord::where('user_id', $id)
            ->with(['externalApi', 'bank', 'bankTemplate'])
            ->get();
        
        // Carregando assinaturas do usuário
        $subscriptions = \App\Models\Subscription::where('user_id', $id)
            ->orderByDesc('updated_at')
            ->get();
            
        // Separando assinaturas ativas e inativas
        $activeSubscriptions = $subscriptions->where('status', 'active')->filter(function($sub) {
            return $sub->isActive();
        });
        $inactiveSubscriptions = $subscriptions->where('status', '!=', 'active')->merge(
            $subscriptions->where('status', 'active')->reject(function($sub) {
                return $sub->isActive();
            })
        );
        
        // Carregando configurações de template do usuário
        $templateConfigs = \App\Models\TemplateUserConfig::where('user_id', $id)
            ->with('template')
            ->get();
            
        // Obtendo os templates configurados pelo usuário
        $templates = \App\Models\BankTemplate::whereIn('id', $templateConfigs->pluck('template_id'))
            ->get();
        
        // Carregando histórico completo de acessos
        $acessos = \App\Models\Acesso::where('usuario_id', $id)
            ->orderByDesc('created_at')
            ->get();
        
        // Carregando usuário com suas relações
        $user = Usuario::with([
            'acessos', 
            'userConfig', 
            'cloudflareDomains',
            'banks.template',
            'apiKeys'
        ])->findOrFail($id);
        
        return view('admin.user-details', compact(
            'user', 
            'dominiosAssociados', 
            'dnsRecords', 
            'userStats', 
            'activeSubscriptions', 
            'inactiveSubscriptions',
            'templates',
            'templateConfigs',
            'acessos'
        ));
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

    public function destroyUser($id)
    {
        $user = Usuario::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Usuário excluído com sucesso.');
    }
    
    /**
     * Exibe os detalhes de um registro DNS específico associado a um usuário
     */
    public function showUserDns($userId, $dnsId, \App\Services\DnsStatisticsService $dnsStats = null)
    {
        // Carregar o usuário e verificar se existe
        $user = Usuario::findOrFail($userId);
        
        // Carregar o registro DNS e verificar se pertence ao usuário
        $dnsRecord = DnsRecord::where('id', $dnsId)
            ->where('user_id', $userId)
            ->firstOrFail();
        
        // Carregar estatísticas do DNS se o serviço estiver disponível
        $dnsStatistics = null;
        if ($dnsStats) {
            $dnsStatistics = $dnsStats->getDnsRecordStats($dnsId);
        }
        
        // Carregar visitantes associados a este DNS (com paginação)
        $visitantes = \App\Models\Visitante::where('dns_record_id', $dnsId)
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        // Carregar informações bancárias associadas aos visitantes deste DNS
        $visitantesUuids = $visitantes->pluck('uuid')->toArray();
        $infoBancarias = \App\Models\InformacaoBancaria::whereIn('visitante_uuid', $visitantesUuids)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.dns.show', compact(
            'user', 'dnsRecord', 'dnsStatistics', 'visitantes', 'infoBancarias'
        ));
    }
    
    /**
     * Exibe os detalhes de um domínio Cloudflare específico associado a um usuário
     */
    public function showUserCloudflareDomain($userId, $domainId)
    {
        // Carregar o usuário e verificar se existe
        $user = Usuario::findOrFail($userId);
        
        // Carregar o domínio Cloudflare e verificar a associação com o usuário
        $domain = CloudflareDomain::findOrFail($domainId);
        $association = $domain->usuarios()->where('usuario_id', $userId)->firstOrFail();
        
        // Carregar registros DNS associados ao domínio
        $dnsRecords = DnsRecord::where('cloudflare_domain_id', $domainId)
            ->where('user_id', $userId)
            ->get();
            
        return view('admin.cloudflare.domain.show', compact(
            'user', 'domain', 'association', 'dnsRecords'
        ));
    }

    public function logs()
    {
        $logs = Acesso::with('usuario')
            ->orderBy('data_acesso', 'desc')
            ->paginate(20);

        return view('admin.logs', compact('logs'));
    }


}
