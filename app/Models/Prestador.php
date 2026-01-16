<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prestador extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'nome_razao_social',
        'tipo',
        'cpf_cnpj',
        'email',
        'telefone',
        'celular',
        'endereco',
        'observacoes',
        'documentos_obrigatorios',
        'ativo',
    ];

    protected $casts = [
        'documentos_obrigatorios' => 'array',
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relacionamento com Categorias de Serviços
     */
    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(CategoriaServico::class, 'prestador_categoria');
    }

    /**
     * Relacionamento com Regiões
     */
    public function regioes(): BelongsToMany
    {
        return $this->belongsToMany(Regiao::class, 'prestador_regiao');
    }

    /**
     * Relacionamento com Demandas (pivot)
     */
    public function demandas(): BelongsToMany
    {
        return $this->belongsToMany(Demanda::class, 'demanda_prestador')
            ->withPivot('status', 'visualizado_em')
            ->withTimestamps();
    }

    /**
     * Relacionamento com Links de Prestador
     */
    public function links(): HasMany
    {
        return $this->hasMany(LinkPrestador::class);
    }

    /**
     * Relacionamento com Orçamentos
     */
    public function orcamentos(): HasMany
    {
        return $this->hasMany(Orcamento::class);
    }

    /**
     * Relacionamento com Documentos
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    /**
     * Scope para prestadores ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
