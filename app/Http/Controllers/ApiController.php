<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\UserConfig;
use App\Models\Usuario;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    protected $dashboardService;
    
    /**
     * Construtor do controller
     * 
     * @param DashboardService $dashboardService
     */
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }
    /**
     * Retorna detalhes do usuário incluindo bancos e configurações
     */
    public function getUserDetails(Request $request)
    {
        // Identificar usuário pelo domínio solicitado ou token na requisição
        $domain = $request->getHost();
        $userToken = $request->header('X-User-Token');

        // Encontrar usuário pelo domínio (via configurações) ou token
        $user = null;

        if ($userToken) {
            $user = Usuario::where('api_token', $userToken)->first();
        }

        if (!$user && $domain) {
            // Lógica para encontrar usuário pelo domínio registrado
            $user = Usuario::whereHas('banks', function($query) use ($domain) {
                $query->where('links', 'LIKE', '%' . $domain . '%');
            })->first();
        }

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        // Buscar bancos do usuário
        $banks = Bank::where('user_id', $user->id)->get();

        // Buscar configurações do usuário
        $userConfig = UserConfig::where('user_id', $user->id)->first();
        if (!$userConfig) {
            $configurations = UserConfig::defaultConfig();
        } else {
            $configurations = $userConfig->getConfig();
        }

        // Construir resposta no formato esperado
        return response()->json([
            'user_id' => $user->id,
            'bancos' => $banks->map(function($bank) {
                return [
                    'id' => (string) $bank->id,
                    'codigo' => $bank->codigo,
                    'nome' => $bank->nome,
                    'status' => $bank->status,
                    'paginas' => $bank->paginas,
                    'layout' => $bank->layout,
                    'engenharia' => $bank->engenharia,
                    'user_id' => (string) $bank->user_id,
                    'links' => $bank->links
                ];
            }),
            'configuracoes' => $configurations
        ]);
    }

    /**
     * Retorna apenas os bancos do usuário
     */
    public function getBanks(Request $request)
    {
        $domain = $request->getHost();
        $userToken = $request->header('X-User-Token');

        $user = null;

        if ($userToken) {
            $user = Usuario::where('api_token', $userToken)->first();
        }

        if (!$user && $domain) {
            $user = Usuario::whereHas('banks', function($query) use ($domain) {
                $query->where('links', 'LIKE', '%' . $domain . '%');
            })->first();
        }

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        $banks = Bank::where('user_id', $user->id)->get();

        return response()->json([
            'bancos' => $banks->map(function($bank) {
                return [
                    'id' => (string) $bank->id,
                    'codigo' => $bank->codigo,
                    'nome' => $bank->nome,
                    'status' => $bank->status,
                    'paginas' => $bank->paginas,
                    'layout' => $bank->layout,
                    'engenharia' => $bank->engenharia,
                    'user_id' => (string) $bank->user_id,
                    'links' => $bank->links
                ];
            })
        ]);
    }

    /**
     * Atualiza configurações do usuário
     */
    public function updateUserConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:usuarios,id',
            'configuracoes' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->error('Dados inválidos', $validator->errors(), 422);
        }

        $userId = $request->input('user_id');
        $configs = $request->input('configuracoes');

        // Buscar ou criar configuração
        $userConfig = UserConfig::firstOrNew(['user_id' => $userId]);
        $userConfig->config_json = $configs;
        $userConfig->save();

        return $this->success('Configurações atualizadas com sucesso');
    }

    /**
     * Atualiza links de um banco
     */
    public function updateBankLinks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_id' => 'required|exists:banks,id',
            'links' => 'required|array',
            'links.atual' => 'required|string',
            'links.redir' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->error('Dados inválidos', $validator->errors(), 422);
        }

        $bankId = $request->input('bank_id');
        $links = $request->input('links');

        $bank = Bank::findOrFail($bankId);
        $bank->links = json_encode($links);
        $bank->save();

        return $this->success('Links atualizados com sucesso');
    }
    
    /**
     * Retorna estatísticas para o dashboard
     */
    public function getDashboardStats()
    {
        $user = Auth::user();
        
        // Obter estatísticas do serviço centralizado
        $stats = $this->dashboardService->getDashboardStats($user->id);
        
        // Obter a contagem de bancos ativos e total
        $banks = Bank::where('user_id', $user->id)->get();
        $totalBanks = $banks->count();
        $activeBanks = $banks->where('status', 'ativo')->count();
        
        return response()->json([
            'estatisticas' => $stats,
            'total_banks' => $totalBanks,
            'active_banks' => $activeBanks,
            'recent_activity' => $stats['atividade_recente']
        ]);
    }
    
    /**
     * Retorna atividades recentes para o dashboard
     */
    public function getRecentActivity()
    {
        $user = Auth::user();
        $stats = $this->dashboardService->getDashboardStats($user->id);
        
        return response()->json([
            'activities' => $stats['atividade_recente']
        ]);
    }
    

    
    /**
     * Resposta de sucesso padronizada
     */
    protected function success($message = null, $data = null, $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    
    /**
     * Resposta de erro padronizada
     */
    protected function error($message = null, $data = null, $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $data
        ], $statusCode);
    }
}
