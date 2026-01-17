<?php

namespace App\Policies;

use App\Models\Demanda;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DemandaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Filtragem é feita pelo Global Scope
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Demanda $demanda): bool
    {
        if ($user->isAdmin()) return true;

        // Mesma administradora?
        if ($user->administradora_id !== $demanda->administradora_id) return false;

        // Se for zelador, deve ser do mesmo condomínio
        if ($user->isZelador()) {
            return $user->condominio_id === $demanda->condominio_id;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Todos (exceto talvez Admin) podem criar demandas
        return $user->isAdministradora() || $user->isGerente() || $user->isZelador();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Demanda $demanda): bool
    {
        if ($user->isAdmin()) return true;

        if ($user->administradora_id !== $demanda->administradora_id) return false;

        // Zelador só edita suas próprias demandas ou do seu condomínio se tiver permissão
        if ($user->isZelador()) {
            return $user->id === $demanda->usuario_id && $user->condominio_id === $demanda->condominio_id;
        }

        return $user->isAdministradora() || $user->isGerente();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Demanda $demanda): bool
    {
        if ($user->isAdmin()) return true;

        // Apenas Admin ou Administradora
        return $user->administradora_id === $demanda->administradora_id && $user->isAdministradora();
    }
}
