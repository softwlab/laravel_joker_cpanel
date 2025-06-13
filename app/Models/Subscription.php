<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * 
 *
 * @property int $id
 * @property string|null $uuid
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property numeric $value
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DnsRecord> $dnsRecords
 * @property-read int|null $dns_records_count
 * @property-read \App\Models\Usuario|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereValue($value)
 * @mixin \Eloquent
 */
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
