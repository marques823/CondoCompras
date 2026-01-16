<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CategoriaServico extends Model
{
    protected $table = 'categorias_servicos';

    protected $fillable = [
        'nome',
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
        return $this->belongsToMany(Prestador::class, 'prestador_categoria');
    }

    /**
     * Relacionamento com Demandas
     */
    public function demandas()
    {
        return $this->hasMany(Demanda::class);
    }

    /**
     * Scope para categorias ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
}
