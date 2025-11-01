<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;
use App\Models\MenuItem;

class ModuleHelper
{
    /**
     * Get active menu item ID based on current URL.
     *
     * @return int|null
     */
    public static function getActiveMenuId()
    {
        $currentPath = Request::path();
        
        // Try to find menu item by link/alias
        $menuItem = MenuItem::where('published', 1)
            ->where(function ($query) use ($currentPath) {
                $query->where('link', 'like', "%{$currentPath}%")
                      ->orWhere('alias', $currentPath);
            })
            ->first();
        
        if ($menuItem) {
            return $menuItem->id;
        }
        
        // Check route parameters for menu item
        $route = Request::route();
        if ($route && $route->hasParameter('menuItemId')) {
            return (int) $route->parameter('menuItemId');
        }
        
        // Check session for active menu
        if (session()->has('active_menu_id')) {
            return (int) session('active_menu_id');
        }
        
        return null;
    }

    /**
     * Set active menu ID in session.
     *
     * @param int|null $menuId
     * @return void
     */
    public static function setActiveMenuId($menuId)
    {
        if ($menuId) {
            session(['active_menu_id' => (int) $menuId]);
        } else {
            session()->forget('active_menu_id');
        }
    }
}

