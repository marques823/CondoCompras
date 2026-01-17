<?php

namespace App\Traits;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;

trait Auditavel
{
    /**
     * Boot do trait - registra eventos do modelo
     */
    public static function bootAuditavel()
    {
        static::created(function ($model) {
            $model->registrarAuditoria('created', null, $model->toArray());
        });

        static::updated(function ($model) {
            $model->registrarAuditoria('updated', $model->getOriginal(), $model->toArray());
        });

        static::deleted(function ($model) {
            $model->registrarAuditoria('deleted', $model->toArray(), null);
        });
    }

    /**
     * Registra uma ação na auditoria
     */
    public function registrarAuditoria(string $acao, $dadosAnteriores = null, $dadosNovos = null): void
    {
        Auditoria::create([
            'administradora_id' => $this->administradora_id ?? Auth::user()?->administradora_id,
            'usuario_id' => Auth::id(),
            'modelo' => class_basename($this),
            'modelo_id' => $this->id,
            'acao' => $acao,
            'dados_anteriores' => $dadosAnteriores ? json_encode($dadosAnteriores) : null,
            'dados_novos' => $dadosNovos ? json_encode($dadosNovos) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
