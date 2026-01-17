<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zelador extends Model
{
    use SoftDeletes;

    protected $table = 'zeladores';

    protected $fillable = [
        'user_id',
        'condominio_id',
        'administradora_id',
        'ativo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condominio()
    {
        return $this->belongsTo(Condominio::class);
    }

    public function administradora()
    {
        return $this->belongsTo(Administradora::class);
    }
}
