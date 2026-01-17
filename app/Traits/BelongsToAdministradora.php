<?php

namespace App\Traits;

use App\Scopes\AdministradoraScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToAdministradora
{
    /**
     * Boot do trait para registrar o escopo global.
     */
    protected static function bootBelongsToAdministradora()
    {
        static::addGlobalScope(new AdministradoraScope);

        // Ao criar um novo registro, associa automaticamente à administradora do usuário logado
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->administradora_id && !$model->administradora_id) {
                $model->administradora_id = Auth::user()->administradora_id;
            }
        });
    }

    /**
     * Relacionamento com Administradora
     */
    public function administradora()
    {
        return $this->belongsTo(\App\Models\Administradora::class, 'administradora_id');
    }
}
