<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ramsey\Uuid\Uuid;
use App\Models\InformacaoBancaria;

class Visitante extends Model
{
    use HasFactory;
    
    protected $table = 'visitantes';
    
    protected $fillable = [
        'uuid',
        'usuario_id',
        'link_id',
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
}
