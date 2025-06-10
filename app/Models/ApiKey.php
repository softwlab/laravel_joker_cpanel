<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string|null $description
 * @property int $active
 * @property int|null $usuario_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $last_used_at
 * @property-read \App\Models\Usuario|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereUsuarioId($value)
 * @mixin \Eloquent
 */
class ApiKey extends Model
{
    protected $table = 'api_keys';

    protected $fillable = [
        'usuario_id', 'key', 'description', 'active'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
