<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Regiao extends Model
{
    protected $fillable = [
        'nome',
        'cidade',
        'estado',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com Prestadores
     */
    public function prestadores(): BelongsToMany
    {
        return $this->belongsToMany(Prestador::class, 'prestador_regiao');
    }

    /**
     * Scope para regiões ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
}
