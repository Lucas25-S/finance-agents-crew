<?php

namespace App\Http\Controllers;

use App\Models\AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Mostrar página de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Mostrar página de registro
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Processa o login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = AuthUser::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Email ou senha incorretos.',
            ])->onlyInput('email');
        }

        session(['auth_user' => $user]);
        return redirect()->route('index')->with('success', 'Login realizado com sucesso!');
    }

    /**
     * Processa o registro
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:auth_users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = AuthUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        session(['auth_user' => $user]);
        return redirect()->route('index')->with('success', 'Conta criada com sucesso! Bem-vindo!');
    }

    /**
     * Faz logout
     */
    public function logout()
    {
        session()->forget('auth_user');
        return redirect()->route('auth.showLogin')->with('success', 'Você foi desconectado.');
    }
}
