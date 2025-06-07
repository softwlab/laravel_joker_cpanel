<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
