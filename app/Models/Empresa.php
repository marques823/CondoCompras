<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        'token_cadastro',
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

    /**
     * Gera um token único para cadastro de prestadores
     */
    public static function gerarTokenCadastro(): string
    {
        do {
            $token = Str::random(32);
        } while (self::where('token_cadastro', $token)->exists());

        return $token;
    }
}
