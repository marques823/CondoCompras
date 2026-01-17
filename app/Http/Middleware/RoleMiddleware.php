<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Se for admin, sempre passa (opcional, dependendo da política)
        if ($user->isAdmin()) {
            return $next($request);
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Redireciona para página de erro 403 ao invés de abortar diretamente
        // Isso permite que a view customizada seja exibida com botão de logout
        return response()->view('errors.403', [], 403);
    }
}
