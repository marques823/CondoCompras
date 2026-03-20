<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'administradora_id',
        'key',
        'value',
        'type',
        'group',
        'description',
    ];
}
