<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
