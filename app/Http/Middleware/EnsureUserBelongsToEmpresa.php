<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserBelongsToEmpresa
{
    /**
     * Handle an incoming request.
     * Garante que o usuário só acesse dados da sua empresa
     * Admins (super admin) não precisam de administradora_id obrigatoriamente
     * Administradoras e outros usuários precisam ter administradora_id
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Super admin não precisa de administradora_id
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // Outros usuários (administradora, usuario, zelador) precisam de administradora_id
        if (!$user || !$user->administradora_id) {
            abort(403, 'Usuário não possui empresa associada.');
        }

        // Adiciona administradora_id ao request para uso nos controllers
        $request->merge(['administradora_id' => $user->administradora_id]);

        return $next($request);
    }
}
