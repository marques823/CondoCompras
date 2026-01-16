<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class EnsureApiToken
{
    /**
     * Handle an incoming request.
     * Verifica se o token da API é válido
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->input('token');

        if (!$token) {
            return response()->json([
                'message' => 'Token de autenticação não fornecido.'
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'message' => 'Token inválido.'
            ], 401);
        }

        // Adiciona o usuário autenticado ao request
        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->tokenable;
        });

        return $next($request);
    }
}
