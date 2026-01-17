<?php

namespace App\Policies;

use App\Models\Condominio;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CondominioPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Administradora e Gerente podem visualizar
        return $user->isAdmin() || $user->isAdministradora() || $user->isGerente();
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Condominio $condominio): bool
    {
        // Se for Super Admin, acesso livre
        if ($user->isAdmin()) return true;

        // Pertence à mesma administradora?
        return $user->administradora_id === $condominio->administradora_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Apenas Gerente pode criar condomínios (Administradora não cria diretamente)
        return $user->isGerente();
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Condominio $condominio): bool
    {
        if ($user->isAdmin()) return true;

        // Apenas se for da mesma administradora
        // Administradora pode atualizar, mas Gerente é quem cria/gerencia operacionalmente
        return $user->administradora_id === $condominio->administradora_id 
            && ($user->isAdministradora() || $user->isGerente());
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Condominio $condominio): bool
    {
        if ($user->isAdmin()) return true;

        // Apenas Administradora pode excluir condomínios de sua empresa
        return $user->administradora_id === $condominio->administradora_id && $user->isAdministradora();
    }
}
