<?php

namespace App\Helpers;

use App\Models\Module;
use App\Models\Menu\Menu;
use App\Models\Category\Category;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class MenuRenderer
{
    /**
     * Detect active menu ID from current request context.
     *
     * @param mixed $entity Optional entity (Product, Category, Content, etc.)
     * @return int|null
     */
    public static function detectActiveMenuId($entity = null): ?int
    {
        // If entity is provided, try to find menu by entity
        if ($entity) {
            // Product
            if ($entity instanceof Product && $entity->categories->count() > 0) {
                $category = $entity->categories->first();
                return static::findMenuByCategory($category);
            }
            
            // Category
            if ($entity instanceof Category) {
                return static::findMenuByCategory($entity);
            }
        }
        
        // Try to detect from URL
        $currentPath = Request::path();
        
        // Try to find menu item by current path or alias
        $menuItem = Menu::where('published', 1)
            ->where(function ($query) use ($currentPath) {
                $query->where('link', 'like', "%{$currentPath}%")
                      ->orWhere('alias', $currentPath)
                      ->orWhere('path', $currentPath);
            })
            ->first();
        
        if ($menuItem) {
            return $menuItem->id;
        }
        
        // Check session for stored active menu
        if (session()->has('active_menu_id')) {
            return (int) session('active_menu_id');
        }
        
        return null;
    }

    /**
     * Find menu item by category.
     *
     * @param Category $category
     * @return int|null
     */
    protected static function findMenuByCategory(Category $category): ?int
    {
        $menuItem = Menu::where('published', 1)
            ->where(function ($query) use ($category) {
                $query->where('link', 'like', '%category_id=' . $category->category_id . '%')
                      ->orWhere('alias', $category->alias)
                      ->orWhere('alias', $category->{'alias_uk-UA'})
                      ->orWhere('alias', $category->{'alias_ru-UA'})
                      ->orWhere('alias', $category->{'alias_en-GB'});
            })
            ->first();
        
        return $menuItem ? $menuItem->id : null;
    }

    /**
     * Get menus and modules for current page.
     *
     * @param int|null $activeMenuId
     * @return array
     */
    public static function getMenusForPage(?int $activeMenuId = null): array
    {
        $language = app()->getLocale();
        $result = [
            'menuTopHtml' => '',
            'menuMainHtml' => '',
            'modules' => collect(),
            'activeMenuId' => $activeMenuId,
        ];

        // Get all published menu modules (without menu restriction or for specific menu)
        $query = Module::published()
            ->where('module', 'mod_menu')
            ->ordered();
        
        if ($activeMenuId) {
            // Get modules for this specific menu item OR modules without menu restrictions
            $modules = Module::published()
                ->where('module', 'mod_menu')
                ->where(function($q) use ($activeMenuId) {
                    $q->whereHas('menuItems', function($query) use ($activeMenuId) {
                        $query->where('id', $activeMenuId);
                    })
                    ->orWhereDoesntHave('menuItems'); // Modules without menu restrictions (show everywhere)
                })
                ->ordered()
                ->get();
        } else {
            // Get modules without menu restrictions (show on all pages)
            $modules = Module::published()
                ->where('module', 'mod_menu')
                ->whereDoesntHave('menuItems')
                ->ordered()
                ->get();
        }
        
        // If no modules found, return empty menus
        if ($modules->isEmpty()) {
            return $result;
        }

        // Process modules
        $result['modules'] = $modules;
        
        // Find menu modules and render them
        $menuModules = $modules->filter(function($module) {
            return $module->module === 'mod_menu';
        });

        // Default menus array
        $topMenu = [];
        $mainMenu = [];

        // Get menus from modules
        foreach ($menuModules as $module) {
            $params = $module->params_array;
            $menuType = $params['menutype'] ?? null;
            $position = $module->position;
            
            if (!$menuType) continue;
            
            // Load menu items for this type
            $menuItems = static::getMenuItems($menuType);
            
            // Assign to position
            if ($position === 'top' || strpos($position, 'header') !== false) {
                $topMenu = $menuItems;
            } elseif ($position === 'main' || strpos($position, 'content') !== false) {
                $mainMenu = $menuItems;
            }
        }

        // Render menus
        $result['menuTopHtml'] = static::renderMenu($topMenu, $language);
        $result['menuMainHtml'] = static::renderMenu($mainMenu, $language);

        return $result;
    }

    /**
     * Get menu items for a specific menu type.
     *
     * @param string $menuType
     * @return array
     */
    protected static function getMenuItems(string $menuType): array
    {
        $cacheKey = "menu_items_{$menuType}";
        
        return Cache::remember($cacheKey, 3600, function() use ($menuType) {
            $items = Menu::where('menutype', $menuType)
                ->where('published', 1)
                ->where('parent_id', 0)
                ->orderBy('lft')
                ->get();

            return static::buildMenuTree($items);
        });
    }

    /**
     * Build hierarchical menu tree.
     *
     * @param \Illuminate\Support\Collection $items
     * @return array
     */
    protected static function buildMenuTree($items): array
    {
        $tree = [];
        
        foreach ($items as $item) {
            $node = [
                'id' => $item->id,
                'title' => $item->title,
                'alias' => $item->alias,
                'path' => $item->path,
                'link' => $item->link,
                'type' => $item->type,
                'language' => $item->language,
                'menu_show' => 1,
                'children' => static::getMenuChildren($item->id),
            ];
            
            $tree[] = $node;
        }
        
        return $tree;
    }

    /**
     * Get children for menu item.
     *
     * @param int $parentId
     * @return array
     */
    protected static function getMenuChildren(int $parentId): array
    {
        $children = Menu::where('parent_id', $parentId)
            ->where('published', 1)
            ->orderBy('lft')
            ->get();

        $result = [];
        
        foreach ($children as $child) {
            $result[] = [
                'id' => $child->id,
                'title' => $child->title,
                'alias' => $child->alias,
                'path' => $child->path,
                'link' => $child->link,
                'type' => $child->type,
                'language' => $child->language,
                'menu_show' => 1,
                'children' => static::getMenuChildren($child->id),
            ];
        }
        
        return $result;
    }

    /**
     * Render menu HTML.
     *
     * @param array $items
     * @param string $language
     * @param int $maxLevels
     * @return string
     */
    protected static function renderMenu(array $items, string $language, int $maxLevels = 4): string
    {
        return view('share.menu.html', [
            'items' => $items,
            'language' => $language,
            'maxLevels' => $maxLevels,
        ])->render();
    }
}

