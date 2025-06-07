<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankTemplate extends Model
{
    use HasFactory;
    
    protected $table = 'bank_templates';

    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'template_url', 
        'logo',
        'active'
    ];

    /**
     * Get the fields associated with this bank template.
     */
    public function fields()
    {
        return $this->hasMany(BankField::class, 'bank_template_id');
    }
    
    /**
     * Get the bank links (Banks) that use this template.
     */
    public function banks()
    {
        return $this->hasMany(Bank::class, 'bank_template_id');
    }
}
