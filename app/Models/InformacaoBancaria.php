<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $visitante_uuid
 * @property \Illuminate\Support\Carbon|null $data
 * @property string|null $agencia
 * @property string|null $conta
 * @property string|null $cpf
 * @property string|null $nome_completo
 * @property string|null $telefone
 * @property array<array-key, mixed>|null $informacoes_adicionais
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Visitante $visitante
 * @method static \Database\Factories\InformacaoBancariaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereAgencia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereConta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereCpf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereInformacoesAdicionais($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereNomeCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereTelefone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereVisitanteUuid($value)
 * @mixin \Eloquent
 */
class InformacaoBancaria extends Model
{
    use HasFactory;
    
    protected $table = 'informacoes_bancarias';
    
    protected $fillable = [
        'visitante_uuid',
        'data',
        'agencia',
        'conta',
        'cpf',
        'nome_completo',
        'telefone',
        'informacoes_adicionais'
    ];
    
    protected $casts = [
        'data' => 'datetime',
        'informacoes_adicionais' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function visitante()
    {
        return $this->belongsTo(Visitante::class, 'visitante_uuid', 'uuid');
    }
}
