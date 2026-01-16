<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandaPrestador extends Model
{
    protected $table = 'demanda_prestador';

    protected $fillable = [
        'demanda_id',
        'prestador_id',
        'status',
        'visualizado_em',
    ];

    protected $casts = [
        'visualizado_em' => 'datetime',
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
     * Marca como visualizado
     */
    public function marcarComoVisualizado(): void
    {
        if (!$this->visualizado_em) {
            $this->update([
                'status' => 'visualizou',
                'visualizado_em' => now(),
            ]);
        }
    }
}
