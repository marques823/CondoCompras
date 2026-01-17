<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToAdministradora;

class Condominio extends Model
{
    use SoftDeletes, BelongsToAdministradora;

    protected $fillable = [
        'administradora_id',
        'gerente_id',
        'nome',
        'cnpj',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'sindico_nome',
        'sindico_telefone',
        'sindico_email',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com Administradora
     */
    public function administradora(): BelongsTo
    {
        return $this->belongsTo(Administradora::class, 'administradora_id');
    }

    /**
     * Relacionamento com Gerente
     */
    public function gerente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gerente_id');
    }

    /**
     * Relacionamento com Zeladores
     */
    public function zeladores(): HasMany
    {
        return $this->hasMany(Zelador::class);
    }

    /**
     * Relacionamento com Demandas
     */
    public function demandas(): HasMany
    {
        return $this->hasMany(Demanda::class);
    }

    /**
     * Relacionamento com Documentos
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    /**
     * Relacionamento com Tags
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'condominio_tag');
    }

    /**
     * Scope para condomínios ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para filtrar por administradora
     */
    public function scopeDaAdministradora($query, $id)
    {
        return $query->where('administradora_id', $id);
    }

    /**
     * Relacionamento com Links de Condomínio
     */
    public function links(): HasMany
    {
        return $this->hasMany(LinkCondominio::class);
    }
}
