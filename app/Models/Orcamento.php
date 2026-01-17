<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orcamento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'demanda_id',
        'prestador_id',
        'link_prestador_id',
        'valor',
        'descricao',
        'validade',
        'status',
        'observacoes',
        'motivo_rejeicao',
        'aprovado_por',
        'aprovado_em',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'validade' => 'date',
        'aprovado_em' => 'datetime',
    ];

    /**
     * Relacionamento com Demanda
     */
    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class);
    }

    /**
     * Relacionamento com Prestador
     */
    public function prestador(): BelongsTo
    {
        return $this->belongsTo(Prestador::class);
    }

    /**
     * Relacionamento com Link Prestador
     */
    public function linkPrestador(): BelongsTo
    {
        return $this->belongsTo(LinkPrestador::class);
    }

    /**
     * Relacionamento com Usuário que aprovou
     */
    public function aprovadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovado_por');
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
     * Aprova o orçamento
     */
    public function aprovar(int $usuarioId): void
    {
        $this->update([
            'status' => 'aprovado',
            'aprovado_por' => $usuarioId,
            'aprovado_em' => now(),
        ]);
    }

    /**
     * Rejeita o orçamento
     */
    public function rejeitar(string $motivo): void
    {
        $this->update([
            'status' => 'rejeitado',
            'motivo_rejeicao' => $motivo,
        ]);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para orçamentos aprovados
     */
    public function scopeAprovados($query)
    {
        return $query->where('status', 'aprovado');
    }
}
