<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bank extends Model
{
    use HasFactory;
    
    protected $table = 'banks';

    protected $fillable = [
        'bank_template_id', 
        'name', 
        'slug', 
        'description', 
        'url', 
        'links',
        'field_values',
        'field_active',
        'active', 
        'usuario_id'
    ];

    protected $casts = [
        'links' => 'array',
        'field_values' => 'array',
        'field_active' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
    
    /**
     * Get the bank template that this bank is based on.
     */
    public function template()
    {
        return $this->belongsTo(BankTemplate::class, 'bank_template_id');
    }

    /**
     * Get the link groups this bank appears in
     */
    public function linkGroups()
    {
        return $this->belongsToMany(LinkGroup::class, 'link_group_banks', 'bank_id', 'link_group_id');
    }

    public function getLinksAttribute($value)
    {
        $links = is_string($value) ? json_decode($value, true) : $value;
        $links = $links ?: [];
        
        return [
            'atual' => $links['atual'] ?? '',
            'redir' => $links['redir'] ?? []
        ];
    }

    public function setLinksAttribute($value)
    {
        $this->attributes['links'] = is_array($value) ? json_encode($value) : $value;
    }
    
    public function getFieldValuesAttribute($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public function setFieldValuesAttribute($value)
    {
        $this->attributes['field_values'] = is_array($value) ? json_encode($value) : $value;
    }
    
    public function getFieldActiveAttribute($value)
    {
        return is_string($value) ? json_decode($value, true) : $value;
    }

    public function setFieldActiveAttribute($value)
    {
        $this->attributes['field_active'] = is_array($value) ? json_encode($value) : $value;
    }
}
