# Migração para Laravel - Guia Detalhado

Este guia apresenta os passos para migrar a aplicação atual para o framework Laravel, mantendo as funcionalidades existentes e o banco de dados SQLite.

## 1. Configuração do Ambiente Laravel

### 1.1. Criar novo projeto Laravel

```bash
# Em um diretório limpo
composer create-project --prefer-dist laravel/laravel jokerlab-laravel

# Acessar o diretório do projeto
cd jokerlab-laravel
```

### 1.2. Configurar o Docker para Laravel

Crie um arquivo `docker-compose.yml` na raiz do projeto:

```yaml
version: '3'

services:
  # Serviço web com PHP
  app:
    image: php:8.1-apache
    container_name: panel_jokerlab
    ports:
      - "8080:80"
      - "8443:443"
    volumes:
      - ./:/var/www/html
      - ./docker/apache/000-default.conf:/etc/apache2/sites-available/000-default.conf
    environment:
      - APP_ENV=development
    depends_on:
      - sqlite-data

  # Container de dados SQLite (reutilizando o existente)
  sqlite-data:
    image: alpine:latest
    container_name: panel_jokerlab-sqlite-data
    volumes:
      - ./database:/var/www/database
    command: "tail -f /dev/null"
```

Crie o diretório docker e o arquivo de configuração do Apache:
```bash
mkdir -p docker/apache
```

Arquivo `docker/apache/000-default.conf`:
```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

## 2. Configuração do Banco de Dados SQLite

### 2.1. Configurar o .env

```
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/database/database.db
```

### 2.2. Copiar o banco de dados existente

Copie o arquivo SQLite do projeto atual para o diretório database/ no novo projeto Laravel.

```bash
# Assegure que o diretório existe
mkdir -p database
cp /caminho/para/seu/database.db database/
```

## 3. Migração dos Modelos

### 3.1. Modelo de Usuário

```php
// app/Models/Usuario.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    // Especifica nome da tabela se diferente do padrão Laravel
    protected $table = 'usuarios';
    
    // Chave primária se não for 'id'
    protected $primaryKey = 'id';
    
    // Atributos que podem ser preenchidos em massa
    protected $fillable = [
        'nome', 'email', 'senha', 'ativo', 'nivel'
    ];
    
    // Atributos que devem ser escondidos
    protected $hidden = [
        'senha', 'remember_token',
    ];
    
    // Se o banco usa timestamps (created_at/updated_at)
    public $timestamps = true;
    
    // Renomear campos de senha se necessário
    public function getAuthPassword()
    {
        return $this->senha;
    }
    
    // Relacionamentos
    public function acessos()
    {
        return $this->hasMany(Acesso::class, 'usuario_id');
    }
}
```

### 3.2. Modelo de Acesso (Sessions)

```php
// app/Models/Acesso.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acesso extends Model
{
    protected $table = 'acessos';
    
    protected $fillable = [
        'usuario_id', 'ip', 'user_agent', 'data_acesso', 'ultimo_acesso'
    ];
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
```

### 3.3. Modelos para Links e Grupos

```php
// app/Models/LinkGroup.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkGroup extends Model
{
    protected $table = 'link_groups';
    
    protected $fillable = [
        'title', 'description', 'active', 'user_id'
    ];
    
    public function items()
    {
        return $this->hasMany(LinkGroupItem::class, 'group_id');
    }
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}

// app/Models/LinkGroupItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkGroupItem extends Model
{
    protected $table = 'link_group_items';
    
    protected $fillable = [
        'group_id', 'title', 'url', 'icon', 'order', 'active'
    ];
    
    public function group()
    {
        return $this->belongsTo(LinkGroup::class, 'group_id');
    }
}
```

### 3.4. Modelo de API Keys

```php
// app/Models/ApiKey.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $table = 'api_keys';
    
    protected $fillable = [
        'user_id', 'key', 'description', 'active'
    ];
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}
```

### 3.5. Modelos de Banco

```php
// app/Models/Bank.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks'; // ajuste para o nome da tabela real
    
    protected $fillable = [
        'codigo', 'nome', 'status', 'paginas', 'layout', 
        'engenharia', 'user_id'
    ];
    
    // Para garantir que os links retornem como um objeto quando acessados
    protected $casts = [
        'links' => 'array',
    ];
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
    
    // Método para obter os links como array estruturado
    public function getLinksAttribute($value)
    {
        $links = json_decode($value, true) ?: [];
        
        // Garantir estrutura padrão
        return [
            'atual' => $links['atual'] ?? '',
            'redir' => $links['redir'] ?? []
        ];
    }
    
    // Método para definir os links como JSON
    public function setLinksAttribute($value)
    {
        $this->attributes['links'] = json_encode($value);
    }
}
```

### 3.6. Modelo de Configurações de Usuário

```php
// app/Models/UserConfig.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserConfig extends Model
{
    protected $table = 'user_configs'; // ajuste para o nome da tabela real
    
    protected $fillable = ['user_id', 'config_json'];
    
    // Sempre retornar configurações como array
    protected $casts = [
        'config_json' => 'array',
    ];
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
    
    // Estrutura padrão de configuração
    public static function defaultConfig()
    {
        return [
            'modal' => [
                'active' => 0,
                'text' => null
            ],
            'status' => '1',
            'proxy' => null,
            'api' => [
                'url' => '',
                'token' => ''
            ],
            'telegram' => [
                'bot_token' => null,
                'chat_id' => null
            ],
            'security' => [
                'block_international' => 0
            ]
        ];
    }
    
    // Helper para obter configuração específica
    public function getConfig($key = null)
    {
        $config = $this->config_json ?: self::defaultConfig();
        
        if ($key === null) {
            return $config;
        }
        
        // Permite acessar configurações aninhadas como 'security.block_international'
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                return null;
            }
            $value = $value[$segment];
        }
        
        return $value;
    }
}
```

### 3.7. Outros Modelos

Seguir o mesmo padrão para os demais modelos (BankPageTemplate, Log, etc.)

## 4. Autenticação

### 4.1. Configurar o Laravel para usar o modelo Usuario

No arquivo `config/auth.php`:

```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\Usuario::class,
    ],
],
```

### 4.2. Controlador de Autenticação

```php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Acesso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'senha' => ['required'],
        ]);
        
        if (Auth::attempt([
            'email' => $credentials['email'], 
            'senha' => $credentials['senha']
        ])) {
            // Registrar acesso
            Acesso::create([
                'usuario_id' => Auth::id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'data_acesso' => now(),
                'ultimo_acesso' => now(),
            ]);
            
            $request->session()->regenerate();
            
            $user = Auth::user();
            if ($user->nivel === 'admin') {
                return redirect()->intended('admin/dashboard');
            }
            
            return redirect()->intended('cliente/dashboard');
        }
        
        return back()->withErrors([
            'email' => 'Credenciais inválidas.',
        ])->withInput($request->except('senha'));
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
```

## 5. Rotas

### 5.1. Rotas de Autenticação

```php
// routes/web.php
Route::middleware('guest')->group(function() {
    Route::get('auth/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('auth/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function() {
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('logout');
});
```

### 5.2. Rotas de Cliente

```php
Route::middleware(['auth', 'nivel:cliente'])->prefix('cliente')->name('cliente.')->group(function() {
    Route::get('dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('profile', [ClientController::class, 'profile'])->name('profile');
    // Outras rotas do cliente
});
```

### 5.3. Rotas de Admin

```php
Route::middleware(['auth', 'nivel:admin'])->prefix('admin')->name('admin.')->group(function() {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Outras rotas do admin
});
```

### 5.4. Rotas de API

```php
// routes/api.php
use App\Http\Controllers\ApiController;

// Rotas públicas que usam identificação por domínio ou token
Route::get('user/details', [ApiController::class, 'getUserDetails']);
Route::get('banks', [ApiController::class, 'getBanks']);

// Rotas protegidas por API Key
Route::middleware('api_key')->group(function() {
    // Atualização de configurações
    Route::post('user/config', [ApiController::class, 'updateUserConfig']);
    Route::post('banks/links', [ApiController::class, 'updateBankLinks']);
});

// Rotas para o dashboard que requerem autenticação por token
Route::middleware('auth:sanctum')->prefix('dashboard')->group(function() {
    Route::get('stats', [ApiController::class, 'getDashboardStats']);
    Route::get('activity', [ApiController::class, 'getRecentActivity']);
});
```

## 6. Middlewares

### 6.1. Middleware de Nível de Usuário

```php
// app/Http/Middleware/CheckUserNivel.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserNivel
{
    public function handle(Request $request, Closure $next, ...$niveis)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (! in_array($user->nivel, $niveis)) {
            abort(403, 'Acesso não autorizado');
        }
        
        return $next($request);
    }
}
```

### 6.2. Middleware de API Key

```php
// app/Http/Middleware/ApiKeyMiddleware.php
namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY');
        
        if (!$apiKey) {
            return response()->json(['error' => 'API key não fornecida'], 401);
        }
        
        $keyExists = ApiKey::where('key', $apiKey)
            ->where('active', true)
            ->exists();
            
        if (!$keyExists) {
            return response()->json(['error' => 'API key inválida'], 401);
        }
        
        return $next($request);
    }
}
```

### 6.3. Registrar Middlewares

Em `app/Http/Kernel.php`, adicione:

```php
protected $routeMiddleware = [
    // Outros middlewares...
    'nivel' => \App\Http\Middleware\CheckUserNivel::class,
    'api_key' => \App\Http\Middleware\ApiKeyMiddleware::class,
];
```

## 7. Controladores

### 7.1. Controlador Base

```php
// app/Http/Controllers/Controller.php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    protected function success($message = null, $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    
    protected function error($message = null, $data = null, $statusCode = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
```

### 7.2. ClientController

```php
// app/Http/Controllers/ClientController.php
namespace App\Http\Controllers;

use App\Models\LinkGroup;
use App\Models\LinkGroupItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $linkGroups = LinkGroup::where('user_id', $user->id)->get();
        
        return view('cliente.dashboard', compact('linkGroups'));
    }
    
    public function profile()
    {
        $user = Auth::user();
        return view('cliente.profile', compact('user'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,'.$user->id,
        ]);
        
        $user->update($validated);
        
        return redirect()->route('cliente.profile')
            ->with('success', 'Perfil atualizado com sucesso');
    }
    
    // Outros métodos conforme necessário
}
```

### 7.3. ApiController

```php
// app/Http/Controllers/ApiController.php
namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\UserConfig;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Retorna detalhes do usuário incluindo bancos e configurações
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
            // Esta implementação depende de como você armazena a relação domínio-usuário
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
            $userConfig = new UserConfig();
            $configurations = UserConfig::defaultConfig();
        } else {
            $configurations = $userConfig->getConfig();
        }
        
        // Construir resposta no formato esperado
        return response()->json([
            'user_id' => $user->id,
            'bancos' => $banks->map(function($bank) {
                // Transformação para garantir o formato exato do JSON
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
        // Similar ao getUserDetails mas retorna apenas os bancos
        // Implementação similar ao método acima
    }
    
    /**
     * Atualiza configurações do usuário
     */
    public function updateUserConfig(Request $request)
    {
        // Validação da requisição
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
        // Validação da requisição
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
        $bank->links = $links;
        $bank->save();
        
        return $this->success('Links atualizados com sucesso');
    }
}
```

### 7.4. AdminController e Outros Controladores

Implemente os demais controladores seguindo o mesmo padrão.

## 8. Views com Blade

### 8.1. Layout Principal

```html
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - JokerLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">JokerLab</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        @if(Auth::user()->nivel === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('cliente.dashboard') }}">Dashboard</a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->nome }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('cliente.profile') }}">Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Sair</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Entrar</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-light py-3 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} JokerLab - Todos os direitos reservados</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
```

### 8.2. Views de Autenticação

```html
<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                <form method="POST" action="{{ url('auth/login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                            id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control @error('senha') is-invalid @enderror" 
                            id="senha" name="senha" required>
                        @error('senha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
```

## 9. Comandos para Iniciar a Aplicação

```bash
# Iniciar contêineres Docker
docker-compose up -d

# Entrar no contêiner da aplicação
docker exec -it panel_jokerlab bash

# Instalar dependências
composer install

# Gerar chave da aplicação
php artisan key:generate

# Limpar cache
php artisan optimize:clear
```

## 10. Considerações Finais

### 10.1. Migração Incremental

Se preferir, você pode implementar a migração incrementalmente:

1. Configurar autenticação e rotas básicas
2. Migrar uma funcionalidade de cada vez (ex: gerenciamento de links, depois API, etc)
3. Testar cada parte antes de passar para a próxima

### 10.2. Logs e Debugging

O Laravel possui um sistema de logging robusto. Configure-o em `config/logging.php`.

### 10.3. Validação e Segurança

Utilize os recursos de validação do Laravel em todos os formulários:

```php
$validated = $request->validate([
    'campo' => 'regras|de|validacao',
]);
```

### 10.4. API com Sanctum

Para tornar a API mais segura e moderna, considere usar Laravel Sanctum:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

## 11. Scripts de Migração de Dados (Opcional)

Se o esquema do banco de dados precisar ser modificado, crie migrations:

```bash
php artisan make:migration create_tabela_necessaria
```

E adapte conforme necessário seus modelos e controladores.
