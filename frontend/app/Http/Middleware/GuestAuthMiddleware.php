<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestAuthMiddleware
{
    /**
     * Bloqueia usuários já autenticados de acessar login/registro
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('auth_user')) {
            return redirect()->route('index');
        }

        return $next($request);
    }
}
