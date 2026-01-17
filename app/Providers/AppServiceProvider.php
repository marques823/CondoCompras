<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Condominio;
use App\Models\Demanda;
use App\Models\Prestador;
use App\Models\Tag;
use App\Policies\CondominioPolicy;
use App\Policies\DemandaPolicy;
use App\Policies\PrestadorPolicy;
use App\Policies\TagPolicy;

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
        // Mapeamento explícito de Policies para garantir funcionamento
        Gate::policy(Condominio::class, CondominioPolicy::class);
        Gate::policy(Demanda::class, DemandaPolicy::class);
        Gate::policy(Prestador::class, PrestadorPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);

        // Super Admin tem acesso a tudo via Gate::before
        Gate::before(function (User $user, string $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        // Gates auxiliares
        Gate::define('admin', fn (User $user) => $user->isAdmin());
        Gate::define('administradora', fn (User $user) => $user->isAdministradora());
        Gate::define('gerente', fn (User $user) => $user->isGerente());
        Gate::define('zelador', fn (User $user) => $user->isZelador());
    }
}
