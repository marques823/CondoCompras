<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Super Admin tem acesso livre
            if ($user->isAdmin()) {
                return $next($request);
            }

            // Usuários de Administradora, Gerente e Zelador DEVEM ter administradora_id
            if (!$user->administradora_id) {
                Auth::logout();
                return redirect()->route('login')->withErrors(['context' => 'Usuário sem administradora vinculada.']);
            }
            
            // Verifica se a administradora está ativa
            $administradora = $user->administradora;
            if ($administradora && !$administradora->ativo) {
                Auth::logout();
                return redirect()->route('login')->withErrors(['context' => 'A administradora vinculada ao seu usuário está desativada. Entre em contato com o suporte.']);
            }
            
            // Se for Zelador, ele deve ter um condominio_id vinculado (regra de negócio)
            if ($user->isZelador() && !$user->condominio_id) {
                // Podemos deixar passar mas restringir via Policy
            }
        }

        return $next($request);
    }
}
