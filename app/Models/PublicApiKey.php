<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicApiKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'api_keys';

    protected $fillable = [
        'name', 'key', 'description', 'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_used_at' => 'datetime',
    ];
    
    // Definir scope para diferenciar as chaves públicas das outras chaves
    protected static function booted()
    {
        static::addGlobalScope('public_api', function ($query) {
            $query->whereNull('usuario_id');
        });
    }

    /**
     * Verifica se a chave de API está ativa
     */
    public function isActive(): bool
    {
        return (bool) $this->active;
    }

    /**
     * Registra o uso atual da chave
     */
    public function markAsUsed(): self
    {
        $this->last_used_at = now();
        $this->save();
        
        return $this;
    }

    /**
     * Relacionamento com os logs dessa chave
     */
    public function logs()
    {
        return $this->hasMany(PublicApiKeyLog::class, 'api_key_id');
    }

    /**
     * Gera uma nova chave API aleatória
     */
    public static function generateKey(): string
    {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Registra log de uma ação na chave
     */
    public function logAction(string $action, array $details = null, $adminId = null): void
    {
        $this->logs()->create([
            'admin_id' => $adminId,
            'action' => $action,
            'details' => $details ? json_encode($details) : null,
            'created_at' => now()
        ]);
    }
}
