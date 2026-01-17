<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class AdministradoraScope implements Scope
{
    /**
     * Aplica o escopo para filtrar apenas dados da administradora do usuário logado.
     */
    public function apply(Builder $builder, Model $model)
    {
        // IMPORTANTE: Se o modelo sendo consultado for User, saímos imediatamente.
        // Isso evita recursão infinita quando o Auth::user() tenta carregar o usuário do banco.
        if ($model instanceof \App\Models\User) {
            return;
        }

        if (Auth::check()) {
            $user = Auth::user();

            // Se for Super Admin, ele pode ver tudo (não aplica o filtro)
            if ($user->isAdmin()) {
                return;
            }

            // Se o usuário tiver um administradora_id, filtra por ele
            if ($user->administradora_id) {
                $builder->where($model->getTable() . '.administradora_id', $user->administradora_id);
            }
        }
    }
}
