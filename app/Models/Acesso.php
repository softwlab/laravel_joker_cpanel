<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $ip
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $data_acesso
 * @property \Illuminate\Support\Carbon|null $ultimo_acesso
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Usuario $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereDataAcesso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereUltimoAcesso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereUsuarioId($value)
 * @mixin \Eloquent
 */
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
