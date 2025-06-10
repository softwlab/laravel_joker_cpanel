<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $api_key_id
 * @property int|null $admin_id
 * @property string $action
 * @property string|null $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\User|null $admin
 * @property-read \App\Models\PublicApiKey $apiKey
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereApiKeyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereId($value)
 * @mixin \Eloquent
 */
class PublicApiKeyLog extends Model
{
    use HasFactory;

    protected $table = 'api_key_logs';
    public $timestamps = false;

    protected $fillable = [
        'api_key_id', 'admin_id', 'action', 'details', 'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relacionamento com a chave API
     */
    public function apiKey()
    {
        return $this->belongsTo(PublicApiKey::class, 'api_key_id');
    }

    /**
     * Relacionamento com o administrador que executou a ação
     */
    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }
}
