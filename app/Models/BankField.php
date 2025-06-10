<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $bank_template_id
 * @property string $name
 * @property string $field_key
 * @property string $field_type
 * @property string|null $placeholder
 * @property bool $is_required
 * @property int $order
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $options
 * @property-read \App\Models\BankTemplate $bankTemplate
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereBankTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereFieldKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BankField extends Model
{
    use HasFactory;
    
    protected $table = 'bank_fields';

    protected $fillable = [
        'bank_template_id',
        'name',
        'field_key',
        'field_type',
        'options',
        'is_required',
        'order',
        'active'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'active' => 'boolean'
    ];

    /**
     * Get the bank template that this field belongs to.
     */
    public function bankTemplate()
    {
        return $this->belongsTo(BankTemplate::class, 'bank_template_id');
    }
}
