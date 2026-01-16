<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LinkPrestador extends Model
{
    protected $table = 'links_prestador';

    protected $fillable = [
        'demanda_id',
        'prestador_id',
        'token',
        'expira_em',
        'usado',
        'usado_em',
        'acessos',
    ];

    protected $casts = [
        'expira_em' => 'datetime',
        'usado' => 'boolean',
        'usado_em' => 'datetime',
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
     * Gera um token único para o link
     */
    public static function gerarToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('token', $token)->exists());

        return $token;
    }

    /**
     * Verifica se o link está válido
     */
    public function isValido(): bool
    {
        if ($this->usado) {
            return false;
        }

        if ($this->expira_em && $this->expira_em->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Marca o link como usado
     */
    public function marcarComoUsado(): void
    {
        $this->update([
            'usado' => true,
            'usado_em' => now(),
            'acessos' => $this->acessos + 1,
        ]);
    }

    /**
     * Incrementa o contador de acessos
     */
    public function incrementarAcesso(): void
    {
        $this->increment('acessos');
    }

    /**
     * Scope para links válidos
     */
    public function scopeValidos($query)
    {
        return $query->where('usado', false)
            ->where(function ($q) {
                $q->whereNull('expira_em')
                  ->orWhere('expira_em', '>', now());
            });
    }
}
