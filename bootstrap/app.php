<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckContext;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Confiar em proxies para detectar HTTPS corretamente (necessário para Zero Trust)
        $middleware->trustProxies(at: '*');

        // Aliases de middlewares customizados
        $middleware->alias([
            'context' => CheckContext::class,
            'role' => RoleMiddleware::class,
        ]);

        // Middlewares globais para rotas web autenticadas (opcional, pode ser aplicado no grupo de rotas)
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
