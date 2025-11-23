<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthUserMiddleware
{
    /**
     * Verifica se o usuário está autenticado
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('auth_user')) {
            return redirect()->route('auth.showLogin');
        }

        return $next($request);
    }
}
