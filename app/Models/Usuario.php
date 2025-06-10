<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * 
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha
 * @property int $ativo
 * @property string $nivel
 * @property string|null $api_token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Acesso> $acessos
 * @property-read int|null $acessos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApiKey> $apiKeys
 * @property-read int|null $api_keys_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CloudflareDomain> $cloudflareDomains
 * @property-read int|null $cloudflare_domains_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LinkGroup> $linkGroups
 * @property-read int|null $link_groups_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\UserConfig|null $userConfig
 * @method static \Database\Factories\UsuarioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereNivel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereSenha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
    
    /**
     * Relacionamento muitos-para-muitos com domínios Cloudflare
     */
    public function cloudflareDomains()
    {
        return $this->belongsToMany(CloudflareDomain::class, 'cloudflare_domain_usuario')
            ->withPivot(['status', 'config', 'notes'])
            ->withTimestamps();
    }
}
