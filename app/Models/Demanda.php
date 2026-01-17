<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demanda extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'condominio_id',
        'categoria_servico_id',
        'usuario_id',
        'titulo',
        'descricao',
        'status',
        'urgencia',
        'observacoes',
    ];

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relacionamento com Condomínio
     */
    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    /**
     * Relacionamento com Categoria de Serviço
     */
    public function categoriaServico(): BelongsTo
    {
        return $this->belongsTo(CategoriaServico::class);
    }

    /**
     * Relacionamento com Usuário (criador)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com Prestadores (pivot)
     */
    public function prestadores(): BelongsToMany
    {
        return $this->belongsToMany(Prestador::class, 'demanda_prestador')
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
     * Relacionamento com Negociações
     */
    public function negociacoes(): HasMany
    {
        return $this->hasMany(Negociacao::class);
    }

    /**
     * Relacionamento com Anexos
     */
    public function anexos(): HasMany
    {
        return $this->hasMany(DemandaAnexo::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
