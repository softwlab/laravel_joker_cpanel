<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $group_id
 * @property string $title
 * @property string $url
 * @property string|null $icon
 * @property int $order
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LinkGroup $group
 * @method static \Database\Factories\LinkGroupItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LinkGroupItem whereUrl($value)
 * @mixin \Eloquent
 */
class LinkGroupItem extends Model
{
    use HasFactory;
    protected $table = 'link_group_items';

    protected $fillable = [
        'group_id', 'title', 'url', 'icon', 'order', 'active'
    ];

    public function group()
    {
        return $this->belongsTo(LinkGroup::class, 'group_id');
    }
}
