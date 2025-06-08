<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Usuario;

class DnsRecord extends Model
{
    use HasFactory;
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_api_id',
        'bank_id',
        'bank_template_id',
        'link_group_id',
        'user_id',
        'record_type',
        'name',
        'content',
        'ttl',
        'priority',
        'status',
        'extra_data'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'extra_data' => 'array',
    ];
    
    /**
     * Relacionamento com a API externa que criou este registro DNS.
     */
    public function externalApi()
    {
        return $this->belongsTo(ExternalApi::class);
    }
    
    /**
     * Relacionamento com o Link Bancário associado.
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    
    /**
     * Relacionamento com a Instituição Bancária associada.
     */
    public function bankTemplate()
    {
        return $this->belongsTo(BankTemplate::class);
    }
    
    /**
     * Relacionamento com o Grupo Organizado associado.
     */
    public function linkGroup()
    {
        return $this->belongsTo(LinkGroup::class);
    }
    
    /**
     * Relacionamento com o usuário associado.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Usuario::class);
    }
    
    /**
     * Escopo para filtrar registros ativos.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Retorna um ícone apropriado baseado no tipo de registro.
     */
    public function getIconAttribute()
    {
        return match($this->record_type) {
            'A' => 'fas fa-server',
            'CNAME' => 'fas fa-exchange-alt',
            'MX' => 'fas fa-envelope',
            'TXT' => 'fas fa-file-alt',
            'SPF' => 'fas fa-shield-alt',
            'DKIM' => 'fas fa-key',
            'DMARC' => 'fas fa-check-circle',
            default => 'fas fa-globe'
        };
    }
}
