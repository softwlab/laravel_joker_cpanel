<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExternalApi;

class CloudflareDomain extends Model
{
    use HasFactory;
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_api_id',
        'zone_id',
        'name',
        'status',
        'paused',
        'meta',
        'is_ghost',
        'name_servers',
        'records_count'
    ];
    
    /**
     * Atributos a serem convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name_servers' => 'array',
        'is_ghost' => 'boolean',
        'paused' => 'boolean',
        'meta' => 'json',
        'records_count' => 'integer'
    ];
    
    /**
     * Relacionamento com a API externa
     */
    public function externalApi()
    {
        return $this->belongsTo(ExternalApi::class, 'external_api_id');
    }
    
    /**
     * Relacionamento muitos-para-muitos com usuários/clientes
     */
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'cloudflare_domain_usuario')
            ->withPivot(['status', 'config', 'notes'])
            ->withTimestamps();
    }
    
    /**
     * Registros DNS associados a este domínio
     * Nota: A relação é feita pelo external_api_id já que não temos uma coluna zone_id na tabela dns_records
     */
    public function dnsRecords()
    {
        return $this->hasMany(DnsRecord::class, 'external_api_id', 'external_api_id');
    }
    
    // Relacionamento com a API externa movido para a declaração acima
}
