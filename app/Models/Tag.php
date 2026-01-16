<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'nome',
        'cor',
        'descricao',
        'tipo',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relacionamento com Prestadores
     */
    public function prestadores(): BelongsToMany
    {
        return $this->belongsToMany(Prestador::class, 'prestador_tag');
    }

    /**
     * Relacionamento com Condomínios
     */
    public function condominios(): BelongsToMany
    {
        return $this->belongsToMany(Condominio::class, 'condominio_tag');
    }

    /**
     * Scope para tags ativas
     */
    public function scopeAtivas($query)
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

    /**
     * Scope para filtrar por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where(function($q) use ($tipo) {
            $q->where('tipo', $tipo)
              ->orWhere('tipo', 'ambos');
        });
    }

    /**
     * Retorna a cor em formato RGB para uso em CSS
     */
    public function getCorRgbAttribute(): string
    {
        $hex = str_replace('#', '', $this->cor);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgb($r, $g, $b)";
    }
}
