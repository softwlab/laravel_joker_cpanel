<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Usuario;

/**
 * 
 *
 * @property int $id
 * @property int|null $external_api_id
 * @property int|null $bank_id
 * @property int|null $bank_template_id
 * @property int|null $user_id
 * @property string $record_type
 * @property string $name
 * @property string $content
 * @property int $ttl
 * @property int $priority
 * @property string $status
 * @property array<array-key, mixed>|null $extra_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\BankTemplate|null $bankTemplate
 * @property-read \App\Models\ExternalApi|null $externalApi
 * @property-read mixed $icon
 * @property-read Usuario|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereBankTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereExternalApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereExtraData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereRecordType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereTtl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereUserId($value)
 * @mixin \Eloquent
 */
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
