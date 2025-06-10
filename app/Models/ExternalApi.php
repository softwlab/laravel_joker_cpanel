<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $external_link_api
 * @property string $key_external_api
 * @property string $status
 * @property array<array-key, mixed>|null $json
 * @property string $type
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array<array-key, mixed>|null $config
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DnsRecord> $dnsRecords
 * @property-read int|null $dns_records_count
 * @property-read mixed $links_count
 * @property-read mixed $records_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereExternalLinkApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereKeyExternalApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExternalApi extends Model
{
    use HasFactory;
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_link_api',
        'key_external_api',
        'status',
        'json',
        'type',
        'name',
        'description',
        'config'
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'json' => 'array',
        'config' => 'array',
    ];
    
    /**
     * Define o relacionamento com os registros DNS criados por esta API.
     */
    public function dnsRecords()
    {
        return $this->hasMany(DnsRecord::class);
    }
    
    /**
     * Retorna a quantidade de registros DNS criados por esta API.
     */
    public function getRecordsCountAttribute()
    {
        return $this->dnsRecords()->count();
    }
    
    /**
     * Retorna a quantidade de Links Bancários utilizando esta API.
     */
    public function getLinksCountAttribute()
    {
        return $this->dnsRecords()->distinct('bank_id')->count('bank_id');
    }
}
