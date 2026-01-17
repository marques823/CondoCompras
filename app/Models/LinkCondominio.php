<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LinkCondominio extends Model
{
    protected $table = 'links_condominio';

    protected $fillable = [
        'condominio_id',
        'empresa_id',
        'token',
        'titulo',
        'ativo',
        'usos',
        'expira_em',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'expira_em' => 'datetime',
    ];

    /**
     * Relacionamento com Condomínio
     */
    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
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
        if (!$this->ativo) {
            return false;
        }

        if ($this->expira_em && $this->expira_em->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Incrementa o contador de usos
     */
    public function incrementarUso(): void
    {
        $this->increment('usos');
    }

    /**
     * Scope para links válidos
     */
    public function scopeValidos($query)
    {
        return $query->where('ativo', true)
            ->where(function($q) {
                $q->whereNull('expira_em')
                  ->orWhere('expira_em', '>', now());
            });
    }
}
