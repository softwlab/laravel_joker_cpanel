<?php

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
            'user' => ['required'],
            'senha' => ['required'],
        ]);

        // Tentar autenticar com usuário e senha
        $user = Usuario::where('email', $credentials['user'])->first();
        
        if ($user && Hash::check($credentials['senha'], $user->senha)) {
            Auth::login($user);
            
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
            'user' => 'Credenciais inválidas.',
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
