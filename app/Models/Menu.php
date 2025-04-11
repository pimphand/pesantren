<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /** @use HasFactory<\Database\Factories\MenuFactory> */
    use HasFactory;

    protected $fillable = [
        'permission_id',
        'name',
        'url',
        'menu_id',
        'order_menu',
        'icon',
        'status',
        'created_at',
        'updated_at',
    ];

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Menu::class, 'menu_id')->orderBy('order_menu');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
