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
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->empresa_id) {
            abort(403, 'Usuário não possui empresa associada.');
        }

        // Adiciona empresa_id ao request para uso nos controllers
        $request->merge(['empresa_id' => $user->empresa_id]);

        return $next($request);
    }
}
