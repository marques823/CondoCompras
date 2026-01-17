<?php

namespace App\Policies;

use App\Models\Prestador;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PrestadorPolicy
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
    public function view(User $user, Prestador $prestador): bool
    {
        if ($user->isAdmin()) return true;
        
        // Pertence à mesma administradora?
        return $user->administradora_id === $prestador->administradora_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Apenas Gerente pode criar prestadores
        return $user->isGerente();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Prestador $prestador): bool
    {
        if ($user->isAdmin()) return true;
        
        // Apenas Gerente pode atualizar
        return $user->administradora_id === $prestador->administradora_id && $user->isGerente();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Prestador $prestador): bool
    {
        if ($user->isAdmin()) return true;
        
        // Apenas Gerente pode excluir
        return $user->administradora_id === $prestador->administradora_id && $user->isGerente();
    }
}
