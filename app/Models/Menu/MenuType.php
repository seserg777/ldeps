<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Model;

class MenuType extends Model
{
    protected $table = 'vjprf_menu_types';
    
    protected $fillable = [
        'asset_id',
        'menutype',
        'title',
        'description',
        'client_id',
        'ordering'
    ];

    protected $casts = [
        'asset_id' => 'integer',
        'client_id' => 'integer',
        'ordering' => 'integer'
    ];

    /**
     * Get the menu items for this menu type.
     */
    public function menuItems()
    {
        return $this->hasMany(Menu::class, 'menutype', 'menutype');
    }

    /**
     * Get published menu items for this menu type.
     */
    public function publishedMenuItems()
    {
        return $this->menuItems()->published();
    }

    /**
     * Get root level menu items for this menu type.
     */
    public function rootMenuItems()
    {
        return $this->menuItems()->root()->orderBy('ordering');
    }

    /**
     * Scope a query to only include frontend menu types.
     */
    public function scopeFrontend($query)
    {
        return $query->where('client_id', 0);
    }

    /**
     * Scope a query to only include admin menu types.
     */
    public function scopeAdmin($query)
    {
        return $query->where('client_id', 1);
    }

    /**
     * Get the count of published menu items.
     */
    public function getPublishedCountAttribute()
    {
        return $this->menuItems()->published()->count();
    }

    /**
     * Get the count of unpublished menu items.
     */
    public function getUnpublishedCountAttribute()
    {
        return $this->menuItems()->where('published', 0)->count();
    }

    /**
     * Get the count of menu items in trash.
     */
    public function getTrashCountAttribute()
    {
        return $this->menuItems()->where('published', -2)->count();
    }
}
