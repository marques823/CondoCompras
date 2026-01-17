<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Administradora extends Model
{
    use SoftDeletes;

    protected $table = 'administradoras';

    protected $fillable = [
        'nome',
        'razao_social',
        'cnpj',
        'email',
        'telefone',
        'endereco',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'token_cadastro',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com Usuários (Administradores da empresa e Gerentes)
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'administradora_id');
    }

    /**
     * Relacionamento com Gerentes (Usuários com role gerente)
     */
    public function gerentes(): HasMany
    {
        return $this->hasMany(User::class, 'administradora_id')->whereHas('roles', function($q) {
            $q->where('name', 'gerente');
        });
    }

    /**
     * Relacionamento com Condomínios
     */
    public function condominios(): HasMany
    {
        return $this->hasMany(Condominio::class, 'administradora_id');
    }

    /**
     * Relacionamento com Zeladores
     */
    public function zeladores(): HasMany
    {
        return $this->hasMany(Zelador::class, 'administradora_id');
    }

    /**
     * Relacionamento com Prestadores
     */
    public function prestadores(): HasMany
    {
        return $this->hasMany(Prestador::class, 'administradora_id');
    }

    /**
     * Relacionamento com Demandas
     */
    public function demandas(): HasMany
    {
        return $this->hasMany(Demanda::class, 'administradora_id');
    }

    /**
     * Relacionamento com Documentos
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'administradora_id');
    }

    /**
     * Scope para administradoras ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Gera um token único para cadastro
     */
    public static function gerarTokenCadastro(): string
    {
        do {
            $token = Str::random(32);
        } while (self::where('token_cadastro', $token)->exists());

        return $token;
    }
}
