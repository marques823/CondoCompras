<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Traits\BelongsToAdministradora;

class LinkDemandaPublico extends Model
{
    use BelongsToAdministradora;
    
    protected $table = 'links_demanda_publico';

    protected $fillable = [
        'demanda_id',
        'administradora_id',
        'token',
        'token_acesso',
        'cpf_cnpj_autorizado',
        'nome_prestador',
        'token_gerado_em',
        'autenticado_em',
        'sessao_id',
        'ativo',
        'acessos',
        'expira_em',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'expira_em' => 'datetime',
        'token_gerado_em' => 'datetime',
        'autenticado_em' => 'datetime',
    ];

    /**
     * Relacionamento com Demanda
     */
    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class);
    }

    /**
     * Relacionamento com Administradora
     */
    public function administradora(): BelongsTo
    {
        return $this->belongsTo(Administradora::class, 'administradora_id');
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
     * Gera um token de acesso de 5 dígitos alfanuméricos (letras maiúsculas e números)
     */
    public static function gerarTokenAcesso(): string
    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $token = '';
        
        for ($i = 0; $i < 5; $i++) {
            $token .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }
        
        return $token;
    }

    /**
     * Verifica se o link está autenticado na sessão atual
     */
    public function isAutenticado(): bool
    {
        if (!$this->token_acesso || !$this->cpf_cnpj_autorizado) {
            return false;
        }

        // Verifica se há uma sessão autenticada válida
        if ($this->sessao_id && session('link_autenticado_' . $this->id) === $this->sessao_id) {
            return true;
        }

        return false;
    }

    /**
     * Autentica o link com CPF/CNPJ e token
     */
    public function autenticar(string $cpfCnpj, string $tokenAcesso): bool
    {
        // Remove formatação do CPF/CNPJ
        $cpfCnpjLimpo = preg_replace('/\D/', '', $cpfCnpj);
        $cpfCnpjAutorizadoLimpo = preg_replace('/\D/', '', $this->cpf_cnpj_autorizado ?? '');

        // Verifica se o CPF/CNPJ corresponde
        if ($cpfCnpjLimpo !== $cpfCnpjAutorizadoLimpo) {
            return false;
        }

        // Verifica se o token corresponde
        if (strtoupper($tokenAcesso) !== strtoupper($this->token_acesso)) {
            return false;
        }

        // Verifica se o token não expirou (válido por 24 horas após geração)
        if ($this->token_gerado_em && $this->token_gerado_em->copy()->addHours(24)->isPast()) {
            return false;
        }

        // Cria sessão de autenticação
        $sessaoId = Str::random(64);
        $this->update([
            'autenticado_em' => now(),
            'sessao_id' => $sessaoId,
        ]);

        session(['link_autenticado_' . $this->id => $sessaoId]);

        return true;
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
        return $query->where('ativo', true)
            ->where(function($q) {
                $q->whereNull('expira_em')
                  ->orWhere('expira_em', '>', now());
            });
    }
}
