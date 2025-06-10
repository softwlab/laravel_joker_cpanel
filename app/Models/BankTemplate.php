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
 * @property string|null $template_url
 * @property string|null $logo
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BankField> $fields
 * @property-read int|null $fields_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereTemplateUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereUpdatedAt($value)
 * @property bool $is_multipage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DnsRecord> $dnsRecords
 * @property-read int|null $dns_records_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereIsMultipage($value)
 * @mixin \Eloquent
 */
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
        'active',
        'is_multipage'
    ];
    
    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'is_multipage' => 'boolean',
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
    
    /**
     * Registros DNS que usam este template como principal ou secundário.
     */
    public function dnsRecords()
    {
        return $this->belongsToMany(DnsRecord::class, 'dns_record_templates')
            ->withPivot('path_segment', 'is_primary')
            ->withTimestamps();
    }
}
