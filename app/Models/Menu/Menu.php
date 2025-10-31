<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'vjprf_menu';

    protected $fillable = [
        'menutype',
        'title',
        'alias',
        'note',
        'path',
        'link',
        'type',
        'published',
        'parent_id',
        'level',
        'component_id',
        'checked_out',
        'checked_out_time',
        'browserNav',
        'access',
        'img',
        'template_style_id',
        'params',
        'lft',
        'rgt',
        'home',
        'language',
        'client_id',
        'publish_up',
        'publish_down',
        'ordering'
    ];

    protected $casts = [
        'published' => 'boolean',
        'home' => 'boolean',
        'browserNav' => 'boolean',
        'publish_up' => 'datetime',
        'publish_down' => 'datetime',
        'checked_out_time' => 'datetime',
        'params' => 'array'
    ];

    /**
     * Get the menu type that owns the menu item.
     */
    public function menuType()
    {
        return $this->belongsTo(MenuType::class, 'menutype', 'menutype');
    }

    /**
     * Get the parent menu item.
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Get the child menu items.
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('ordering')->orderBy('lft');
    }

    /**
     * Get all descendants of the menu item.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Scope a query to only include published menu items.
     */
    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

    /**
     * Scope a query to only include menu items of a specific type.
     */
    public function scopeOfType($query, $menutype)
    {
        return $query->where('menutype', $menutype);
    }

    /**
     * Scope a query to only include root level menu items.
     */
    public function scopeRoot($query)
    {
        return $query->where('level', 1);
    }

    /**
     * Get the full path for the menu item.
     */
    public function getFullPathAttribute()
    {
        return $this->path ?: $this->alias;
    }

    /**
     * Check if menu item is published.
     */
    public function isPublished()
    {
        return $this->published == 1;
    }

    /**
     * Check if menu item is home page.
     */
    public function isHome()
    {
        return $this->home == 1;
    }
}
