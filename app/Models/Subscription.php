<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    use HasFactory;
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'description',
        'value',
        'start_date',
        'end_date',
        'status'
    ];
    
    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'value' => 'decimal:2',
    ];
    
    /**
     * Obtém o usuário associado à assinatura.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
    
    /**
     * Obtém os registros DNS associados à assinatura.
     *
     * @return BelongsToMany
     */
    public function dnsRecords(): BelongsToMany
    {
        return $this->belongsToMany(DnsRecord::class, 'dns_record_subscription')
                    ->withTimestamps();
    }
    
    /**
     * Verifica se a assinatura está ativa.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->status === 'active' && 
               $now->greaterThanOrEqualTo($this->start_date) && 
               $now->lessThanOrEqualTo($this->end_date);
    }
    
    /**
     * Verifica se a assinatura expirou.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return Carbon::now()->greaterThan($this->end_date);
    }
    
    /**
     * Retorna o tempo restante da assinatura em dias.
     *
     * @return int
     */
    public function getRemainingDays(): int
    {
        $now = Carbon::now();
        if ($now->greaterThan($this->end_date)) {
            return 0;
        }
        
        return $now->diffInDays($this->end_date);
    }
    
    /**
     * Escopo para assinaturas ativas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('status', 'active')
                     ->where('start_date', '<=', $now)
                     ->where('end_date', '>=', $now);
    }
}
