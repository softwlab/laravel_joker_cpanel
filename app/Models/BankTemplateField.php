<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTemplateField extends Model
{
    /**
     * Atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'template_id',
        'field_key',
        'label',
        'placeholder',
        'order',
        'is_required',
        'input_type',
        'validation_rules'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Relcionamento com o template
     */
    public function template()
    {
        return $this->belongsTo(BankTemplate::class);
    }
}
