<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $fillable = [
        'empresa_id',
        'usuario_id',
        'modelo',
        'modelo_id',
        'acao',
        'dados_anteriores',
        'dados_novos',
        'ip_address',
        'user_agent',
        'observacoes',
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
    ];

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relacionamento com Usuário
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por empresa
     */
    public function scopeDaEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para filtrar por modelo
     */
    public function scopeDoModelo($query, $modelo, $modeloId = null)
    {
        $query->where('modelo', $modelo);
        
        if ($modeloId) {
            $query->where('modelo_id', $modeloId);
        }
        
        return $query;
    }

    /**
     * Scope para filtrar por ação
     */
    public function scopePorAcao($query, $acao)
    {
        return $query->where('acao', $acao);
    }
}
