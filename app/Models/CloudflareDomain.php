<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExternalApi;

/**
 * 
 *
 * @property int $id
 * @property int $external_api_id
 * @property string $zone_id
 * @property string $name
 * @property string $status
 * @property bool $is_ghost
 * @property array<array-key, mixed>|null $name_servers
 * @property int|null $records_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DnsRecord> $dnsRecords
 * @property-read int|null $dns_records_count
 * @property-read ExternalApi $externalApi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Usuario> $usuarios
 * @property-read int|null $usuarios_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereExternalApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereIsGhost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereNameServers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereRecordsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereZoneId($value)
 * @mixin \Eloquent
 */
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
