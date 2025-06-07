<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acesso extends Model
{
    protected $table = 'acessos';

    protected $fillable = [
        'usuario_id', 'ip', 'user_agent', 'data_acesso', 'ultimo_acesso'
    ];

    protected $casts = [
        'data_acesso' => 'datetime',
        'ultimo_acesso' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
