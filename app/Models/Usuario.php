<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nome', 'email', 'senha', 'ativo', 'nivel', 'api_token'
    ];

    protected $hidden = [
        'senha', 'remember_token',
    ];

    public $timestamps = true;

    public function getAuthPassword()
    {
        return $this->senha;
    }

    public function acessos()
    {
        return $this->hasMany(Acesso::class, 'usuario_id');
    }

    public function banks()
    {
        return $this->hasMany(Bank::class, 'usuario_id');
    }

    public function linkGroups()
    {
        return $this->hasMany(LinkGroup::class, 'usuario_id');
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class, 'usuario_id');
    }

    public function userConfig()
    {
        return $this->hasOne(UserConfig::class, 'usuario_id');
    }
}
