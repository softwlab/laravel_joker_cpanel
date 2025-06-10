<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ramsey\Uuid\Uuid;
use App\Models\InformacaoBancaria;

/**
 * 
 *
 * @property int $id
 * @property string $uuid
 * @property int $usuario_id
 * @property int|null $link_id
 * @property string|null $ip
 * @property string|null $user_agent
 * @property string|null $referrer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, InformacaoBancaria> $informacoes
 * @property-read int|null $informacoes_count
 * @property-read \App\Models\LinkGroupItem|null $linkItem
 * @property-read \App\Models\Usuario $usuario
 * @method static \Database\Factories\VisitanteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereReferrer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereUsuarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereUuid($value)
 * @mixin \Eloquent
 */
class Visitante extends Model
{
    use HasFactory;
    
    protected $table = 'visitantes';
    
    protected $fillable = [
        'uuid',
        'usuario_id',
        'link_id',
        'dns_record_id',
        'ip',
        'user_agent',
        'referrer',
        'created_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = Uuid::uuid4()->toString();
            }
        });
    }
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
    
    public function linkItem()
    {
        return $this->belongsTo(LinkGroupItem::class, 'link_id');
    }
    
    public function informacoes()
    {
        return $this->hasMany(InformacaoBancaria::class, 'visitante_uuid', 'uuid');
    }
    
    public function dnsRecord()
    {
        return $this->belongsTo(DnsRecord::class, 'dns_record_id');
    }
}
