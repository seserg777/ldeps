<?php

namespace App\Helpers;

use App\Models\Module;
use App\Models\Menu\Menu;
use Illuminate\Support\Facades\Cache;

class MenuRenderer
{
    /**
     * Get menus and modules for current page.
     *
     * @param int|null $activeMenuId
     * @param array $defaultMenuTypes Default menu types to load if no modules found
     * @return array
     */
    public static function getMenusForPage($activeMenuId = null, $defaultMenuTypes = [])
    {
        $language = app()->getLocale();
        $result = [
            'menuTopHtml' => '',
            'menuMainHtml' => '',
            'modules' => collect(),
            'activeMenuId' => $activeMenuId,
        ];

        // Get modules for this menu item ID
        $modules = collect();
        
        if ($activeMenuId) {
            $modules = Module::published()
                ->whereHas('menuItems', function($query) use ($activeMenuId) {
                    $query->where('id', $activeMenuId);
                })
                ->ordered()
                ->get();
        }
        
        // If no modules found or no activeMenuId, use default menus
        if ($modules->isEmpty()) {
            $menus = static::buildMenus($defaultMenuTypes);
            
            $result['menuTopHtml'] = static::renderMenu(
                $menus[$defaultMenuTypes[0]] ?? [],
                $language
            );
            
            $result['menuMainHtml'] = static::renderMenu(
                $menus[$defaultMenuTypes[1]] ?? [],
                $language
            );
            
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

        // If no menu modules, use defaults
        if (empty($topMenu) && empty($mainMenu)) {
            $menus = static::buildMenus($defaultMenuTypes);
            $topMenu = $menus[$defaultMenuTypes[0]] ?? [];
            $mainMenu = $menus[$defaultMenuTypes[1]] ?? [];
        }

        // Render menus
        $result['menuTopHtml'] = static::renderMenu($topMenu, $language);
        $result['menuMainHtml'] = static::renderMenu($mainMenu, $language);

        return $result;
    }

    /**
     * Build menu items from database.
     *
     * @param array $menuTypes
     * @return array
     */
    protected static function buildMenus(array $menuTypes): array
    {
        $result = [];
        
        foreach ($menuTypes as $type) {
            $result[$type] = static::getMenuItems($type);
        }
        
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

