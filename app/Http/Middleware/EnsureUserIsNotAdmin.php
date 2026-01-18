<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsNotAdmin
{
    /**
     * Handle an incoming request.
     * Bloqueia acesso de admins às rotas de gestão de condomínios/prestadores/demandas
     * Admin só gerencia administradoras e usuários
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            abort(403, 'Acesso negado. Administradores do sistema só gerenciam administradoras e usuários.');
        }

        return $next($request);
    }
}
