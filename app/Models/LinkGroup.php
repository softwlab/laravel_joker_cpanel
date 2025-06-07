<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
