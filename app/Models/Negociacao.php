<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Negociacao extends Model
{
    protected $table = 'negociacoes';

    protected $fillable = [
        'orcamento_id',
        'demanda_id',
        'prestador_id',
        'tipo',
        'valor_original',
        'valor_solicitado',
        'parcelas',
        'status',
        'mensagem_solicitacao',
        'mensagem_resposta',
        'respondido_em',
        'criado_por',
    ];

    protected $casts = [
        'valor_original' => 'decimal:2',
        'valor_solicitado' => 'decimal:2',
        'respondido_em' => 'datetime',
    ];

    /**
     * Relacionamento com Orçamento
     */
    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(Orcamento::class);
    }

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
     * Relacionamento com Usuário que criou
     */
    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    /**
     * Scope para negociações pendentes
     */
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    /**
     * Scope para negociações aceitas
     */
    public function scopeAceitas($query)
    {
        return $query->where('status', 'aceita');
    }

    /**
     * Aceita a negociação
     */
    public function aceitar(string $mensagemResposta = null): void
    {
        $this->update([
            'status' => 'aceita',
            'mensagem_resposta' => $mensagemResposta,
            'respondido_em' => now(),
        ]);

        // Atualiza o valor do orçamento conforme o tipo de negociação
        if ($this->tipo === 'contraproposta') {
            // Contraproposta: atualiza para o valor proposto
            $this->orcamento->update([
                'valor' => $this->valor_solicitado,
            ]);
        } elseif ($this->tipo === 'desconto') {
            // Desconto: atualiza para o valor com desconto aplicado
            $this->orcamento->update([
                'valor' => $this->valor_solicitado, // valor_solicitado já é o valor final com desconto
            ]);
        } elseif ($this->tipo === 'parcelamento') {
            // Parcelamento: mantém o valor original (o parcelamento é apenas uma forma de pagamento)
            // Não altera o valor do orçamento, apenas registra as parcelas
        }
    }

    /**
     * Recusa a negociação
     */
    public function recusar(string $mensagemResposta = null): void
    {
        $this->update([
            'status' => 'recusada',
            'mensagem_resposta' => $mensagemResposta,
            'respondido_em' => now(),
        ]);
    }
}
