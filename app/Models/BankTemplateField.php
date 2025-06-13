<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $template_id
 * @property string $field_key
 * @property string|null $label
 * @property string|null $placeholder
 * @property int $order
 * @property bool $is_required
 * @property string $input_type
 * @property string|null $validation_rules
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankTemplate $template
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereFieldKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereInputType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereValidationRules($value)
 * @mixin \Eloquent
 */
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
