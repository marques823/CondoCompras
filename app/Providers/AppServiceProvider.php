<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super Admin tem acesso a tudo
        Gate::before(function (User $user, string $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        // Define Gate para verificar se usuário é admin
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        // Gate para Administradora
        Gate::define('administradora', function (User $user) {
            return $user->isAdministradora();
        });

        // Gate para Gerente
        Gate::define('gerente', function (User $user) {
            return $user->isGerente();
        });

        // Gate para Zelador
        Gate::define('zelador', function (User $user) {
            return $user->isZelador();
        });
    }
}
