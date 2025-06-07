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
     * Relacionamento com a API externa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function externalApi()
    {
        return $this->belongsTo(ExternalApi::class, 'external_api_id');
    }
}
