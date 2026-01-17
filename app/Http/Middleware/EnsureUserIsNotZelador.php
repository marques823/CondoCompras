<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsNotZelador
{
    /**
     * Handle an incoming request.
     * Bloqueia acesso de zeladores às rotas administrativas
     * Permite acesso para admin e administradora
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isZelador()) {
            abort(403, 'Acesso negado. Zeladores não têm permissão para acessar esta área.');
        }

        return $next($request);
    }
}
