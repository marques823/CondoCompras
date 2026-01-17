<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToAdministradora;

class Documento extends Model
{
    use SoftDeletes, BelongsToAdministradora;

    protected $fillable = [
        'administradora_id',
        'condominio_id',
        'demanda_id',
        'orcamento_id',
        'prestador_id',
        'tipo',
        'nome_original',
        'nome_arquivo',
        'caminho',
        'mime_type',
        'tamanho',
        'data_documento',
        'observacoes',
    ];

    protected $casts = [
        'tamanho' => 'integer',
        'data_documento' => 'date',
    ];

    /**
     * Relacionamento com Administradora
     */
    public function administradora(): BelongsTo
    {
        return $this->belongsTo(Administradora::class, 'administradora_id');
    }

    /**
     * Relacionamento com Condomínio
     */
    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    /**
     * Relacionamento com Demanda
     */
    public function demanda(): BelongsTo
    {
        return $this->belongsTo(Demanda::class);
    }

    /**
     * Relacionamento com Orçamento
     */
    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(Orcamento::class);
    }

    /**
     * Relacionamento com Prestador
     */
    public function prestador(): BelongsTo
    {
        return $this->belongsTo(Prestador::class);
    }

    /**
     * Retorna o tamanho formatado
     */
    public function getTamanhoFormatadoAttribute(): string
    {
        $bytes = $this->tamanho;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por administradora
     */
    public function scopeDaAdministradora($query, $id)
    {
        return $query->where('administradora_id', $id);
    }
}
