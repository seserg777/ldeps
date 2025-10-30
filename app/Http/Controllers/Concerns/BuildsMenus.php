<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Menu\Menu;

trait BuildsMenus
{
    /**
     * Build menus for given types. Supports multiple menus on a page.
     *
     * @param array $menuTypes
     * @return array<string, array>
     */
    protected function buildMenus(array $menuTypes): array
    {
        $extractParams = function($item) {
            // Params may already be an array or a JSON string
            if (is_array($item->params)) {
                return $item->params;
            }
            if (is_string($item->params) && $item->params !== '') {
                $decoded = json_decode($item->params, true);
                return is_array($decoded) ? $decoded : [];
            }
            return [];
        };

        $shouldShow = function($item) use ($extractParams) {
            $params = $extractParams($item);
            // default: show if not specified
            return (int)($params['menu_show'] ?? 1) !== 0;
        };

        $menus = [];

        foreach ($menuTypes as $type) {
            $buildTree = function ($parentId) use (&$buildTree, $type, $shouldShow, $extractParams) {
                $children = Menu::ofType($type)
                    ->published()
                    ->where('parent_id', $parentId)
                            ->orderBy('ordering')
                            ->orderBy('lft')
                    ->get()
                    ->filter($shouldShow);

                return $children->map(function ($item) use (&$buildTree, $shouldShow, $extractParams) {
                    $params = $extractParams($item);

                    // Resolve alias type: use target menu item's routing params
                    $effectiveAlias = $item->alias;
                    $effectivePath = $item->alias;
                    $effectiveLink = $item->link;
                    $effectiveType = $item->type;

                    if ($item->type === 'alias') {
                        $targetId = (int) ($params['aliasoptions'] ?? 0);
                        if ($targetId > 0) {
                            $target = Menu::published()->where('id', $targetId)->first();
                            if ($target) {
                                $effectiveAlias = $target->alias ?: $effectiveAlias;
                                $effectivePath = $target->alias ?: $effectivePath;
                                $effectiveLink = $target->link ?: $effectiveLink;
                                $effectiveType = $target->type ?: $effectiveType;
                            }
                        }
                    }

                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'alias' => $effectiveAlias,
                        'path' => $effectivePath, // Use alias as path for SEO URLs
                        'link' => $effectiveLink,
                        'type' => $effectiveType,
                        'level' => $item->level,
                        'img' => $item->img,
                        'language' => $item->language,
                        'menu_show' => (int)($params['menu_show'] ?? 1),
                        'children' => $buildTree($item->id),
                    ];
                })->values()->all();
            };

            $menus[$type] = Menu::ofType($type)
                ->published()
                ->root()
                        ->orderBy('ordering')
                        ->orderBy('lft')
                ->get()
                ->filter($shouldShow)
                ->map(function ($item) use (&$buildTree, $shouldShow, $extractParams) {
                    $params = $extractParams($item);

                    $effectiveAlias = $item->alias;
                    $effectivePath = $item->alias;
                    $effectiveLink = $item->link;
                    $effectiveType = $item->type;

                    if ($item->type === 'alias') {
                        $targetId = (int) ($params['aliasoptions'] ?? 0);
                        if ($targetId > 0) {
                            $target = Menu::published()->where('id', $targetId)->first();
                            if ($target) {
                                $effectiveAlias = $target->alias ?: $effectiveAlias;
                                $effectivePath = $target->alias ?: $effectivePath;
                                $effectiveLink = $target->link ?: $effectiveLink;
                                $effectiveType = $target->type ?: $effectiveType;
                            }
                        }
                    }

                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'alias' => $effectiveAlias,
                        'path' => $effectivePath, // Use alias as path for SEO URLs
                        'link' => $effectiveLink,
                        'type' => $effectiveType,
                        'level' => $item->level,
                        'img' => $item->img,
                        'language' => $item->language,
                        'menu_show' => (int)($params['menu_show'] ?? 1),
                        'children' => $buildTree($item->id),
                    ];
                })->values()->all();
        }

        return $menus;
    }
}


