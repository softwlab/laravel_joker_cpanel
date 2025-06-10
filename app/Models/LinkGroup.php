<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int $active
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $usuario_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LinkGroupItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Usuario|null $usuario
 * @method static \Database\Factories\LinkGroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroup whereUsuarioId($value)
 * @mixin \Eloquent
 */
class LinkGroup extends Model
{
    use HasFactory;
    protected $table = 'link_groups';

    protected $fillable = [
        'title', 'description', 'active', 'usuario_id'
    ];

    /**
     * Get the link group items in this group
     */
    public function items()
    {
        return $this->hasMany(LinkGroupItem::class, 'group_id');
    }
    
    /**
     * Get the banks in this group
     */
    public function banks()
    {
        return $this->belongsToMany(Bank::class, 'link_group_banks', 'link_group_id', 'bank_id')
                    ->withPivot('order')
                    ->orderBy('link_group_banks.order', 'asc');
    }

    /**
     * Get the user that owns this group
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
