<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
