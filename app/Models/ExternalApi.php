<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
