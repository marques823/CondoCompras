<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome',
        'razao_social',
        'cnpj',
        'email',
        'telefone',
        'endereco',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com Usuários
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relacionamento com Condomínios
     */
    public function condominios(): HasMany
    {
        return $this->hasMany(Condominio::class);
    }

    /**
     * Relacionamento com Prestadores
     */
    public function prestadores(): HasMany
    {
        return $this->hasMany(Prestador::class);
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
     * Relacionamento com Auditoria
     */
    public function auditorias(): HasMany
    {
        return $this->hasMany(Auditoria::class);
    }

    /**
     * Scope para empresas ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
}
