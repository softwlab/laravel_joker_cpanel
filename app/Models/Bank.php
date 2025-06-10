<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $url
 * @property array<array-key, mixed>|null $links
 * @property int $active
 * @property int $usuario_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $bank_template_id
 * @property array<array-key, mixed>|null $field_values
 * @property array<array-key, mixed>|null $field_active
 * @property-read \App\Models\BankTemplate|null $template
 * @property-read \App\Models\Usuario $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereBankTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereFieldActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereFieldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereUsuarioId($value)
 * @mixin \Eloquent
 */
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
