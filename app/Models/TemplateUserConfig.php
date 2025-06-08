<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateUserConfig extends Model
{
    protected $table = 'template_user_configs';

    protected $fillable = [
        'user_id',
        'template_id',
        'record_id',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    /**
     * Relacionamento com o usuário
     */
    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    /**
     * Relacionamento com o template
     */
    public function template()
    {
        return $this->belongsTo(BankTemplate::class, 'template_id');
    }

    /**
     * Relacionamento com o registro DNS
     */
    public function record()
    {
        return $this->belongsTo(\App\Models\DnsRecord::class, 'record_id');
    }

    /**
     * Obtém a configuração de um campo específico
     */
    public function getFieldConfig($fieldName)
    {
        if (isset($this->config[$fieldName])) {
            return $this->config[$fieldName];
        }
        
        return [
            'active' => true,
            'order' => 0
        ];
    }

    /**
     * Verifica se um campo está ativo
     */
    public function isFieldActive($fieldName)
    {
        return isset($this->config[$fieldName]) && $this->config[$fieldName]['active'];
    }

    /**
     * Obtém a ordem de um campo
     */
    public function getFieldOrder($fieldName)
    {
        return isset($this->config[$fieldName]) ? $this->config[$fieldName]['order'] : 0;
    }
}
