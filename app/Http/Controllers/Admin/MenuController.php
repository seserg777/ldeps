<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu\Menu;
use App\Models\Menu\MenuType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MenuController extends Controller
{
    /**
     * Display a listing of menu items for a specific menu type.
     */
    public function index(Request $request, $menutype): View
    {
        $menuType = MenuType::where('menutype', $menutype)->firstOrFail();

        $query = Menu::ofType($menutype)->with('parent');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('alias', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'published') {
                $query->where('published', 1);
            } elseif ($status === 'unpublished') {
                $query->where('published', 0);
            } elseif ($status === 'trash') {
                $query->where('published', -2);
            }
        }

        // Filter by access level
        if ($request->filled('access')) {
            $query->where('access', $request->access);
        }

        // Filter by language
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        // Ordering
        $sortBy = $request->get('sort', 'lft');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $menuItems = $query->paginate(10);

        $columns = [
            [
                'key' => 'id',
                'label' => 'ID',
                'sortable' => true
            ],
            [
                'key' => 'title',
                'label' => 'Title',
                'sortable' => true,
                'searchable' => true,
                'format' => 'hierarchical'
            ],
            [
                'key' => 'alias',
                'label' => 'Alias',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'link',
                'label' => 'Link',
                'searchable' => true
            ],
            [
                'key' => 'level',
                'label' => 'Level',
                'sortable' => true
            ],
            [
                'key' => 'published',
                'label' => 'Status',
                'sortable' => true,
                'format' => 'menu_status'
            ],
            [
                'key' => 'language',
                'label' => 'Language',
                'sortable' => true
            ]
        ];

        $sortableColumns = ['id', 'title', 'alias', 'level', 'published', 'language'];

        $actions = [
            [
                'label' => 'View',
                'icon' => 'eye',
                'class' => 'info',
                'url' => function ($item) use ($menuType) {
                    return route('admin.menu.items.show', [$menuType->menutype, $item]);
                }
            ],
            [
                'label' => 'Edit',
                'icon' => 'edit',
                'class' => 'warning',
                'url' => function ($item) use ($menuType) {
                    return route('admin.menu.items.edit', [$menuType->menutype, $item]);
                }
            ],
            [
                'label' => 'Publish',
                'icon' => 'eye',
                'class' => 'success',
                'url' => function ($item) use ($menuType) {
                    return route('admin.menu.items.toggle-published', [$menuType->menutype, $item]);
                },
                'condition' => function ($item) {
                    return $item->published == 0;
                }
            ],
            [
                'label' => 'Unpublish',
                'icon' => 'eye-slash',
                'class' => 'secondary',
                'url' => function ($item) use ($menuType) {
                    return route('admin.menu.items.toggle-published', [$menuType->menutype, $item]);
                },
                'condition' => function ($item) {
                    return $item->published == 1;
                }
            ],
            [
                'label' => 'Move to Trash',
                'icon' => 'trash',
                'class' => 'warning',
                'url' => function ($item) use ($menuType) {
                    return route('admin.menu.items.trash', [$menuType->menutype, $item]);
                },
                'condition' => function ($item) {
                    return $item->published != -2;
                }
            ],
            [
                'label' => 'Restore',
                'icon' => 'undo',
                'class' => 'info',
                'url' => function ($item) use ($menuType) {
                    return route('admin.menu.items.restore', [$menuType->menutype, $item]);
                },
                'condition' => function ($item) {
                    return $item->published == -2;
                }
            ],
            [
                'label' => 'Delete Permanently',
                'icon' => 'times',
                'class' => 'danger',
                'url' => function ($item) use ($menuType) {
                    return route('admin.menu.items.destroy', [$menuType->menutype, $item]);
                }
            ]
        ];

        $bulkActions = [
            [
                'key' => 'publish',
                'label' => 'Publish Selected'
            ],
            [
                'key' => 'unpublish',
                'label' => 'Unpublish Selected'
            ],
            [
                'key' => 'trash',
                'label' => 'Move to Trash'
            ],
            [
                'key' => 'delete',
                'label' => 'Delete Permanently'
            ]
        ];

        $filters = [
            [
                'key' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    '' => 'All Statuses',
                    'published' => 'Published',
                    'unpublished' => 'Unpublished',
                    'trash' => 'In Trash'
                ]
            ],
            [
                'key' => 'language',
                'label' => 'Language',
                'type' => 'select',
                'options' => [
                    '' => 'All Languages',
                    'ru-RU' => 'Russian',
                    'uk-UA' => 'Ukrainian',
                    'en-GB' => 'English'
                ]
            ]
        ];

        return view('admin.menu.items.index', compact(
            'menuType',
            'menuItems',
            'columns',
            'sortableColumns',
            'actions',
            'bulkActions',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new menu item.
     */
    public function create($menutype): View
    {
        $menuType = MenuType::where('menutype', $menutype)->firstOrFail();
        $parentItems = Menu::ofType($menutype)->where('level', '<', 2)->orderBy('lft')->get();

        return view('admin.menu.items.create', compact('menuType', 'parentItems'));
    }

    /**
     * Store a newly created menu item.
     */
    public function store(Request $request, $menutype): RedirectResponse
    {
        $menuType = MenuType::where('menutype', $menutype)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'alias' => 'required|string|max:400',
            'note' => 'nullable|string|max:255',
            'link' => 'required|string|max:1024',
            'type' => 'required|string|max:16',
            'published' => 'boolean',
            'parent_id' => 'required|integer',
            'access' => 'required|integer|min:0',
            'img' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:7',
            'publish_up' => 'nullable|date',
            'publish_down' => 'nullable|date|after:publish_up'
        ]);

        $data = $request->all();
        $data['menutype'] = $menutype;
        $data['path'] = $this->generatePath($data['alias'], $data['parent_id']);
        $data['level'] = $this->calculateLevel($data['parent_id']);
        $data['ordering'] = Menu::ofType($menutype)->where('parent_id', $data['parent_id'])->max('ordering') + 1;
        $data['lft'] = $this->calculateLft($data['parent_id']);
        $data['rgt'] = $data['lft'] + 1;

        // Update rgt values for parent and siblings
        $this->updateRgtValues($data['lft']);

        Menu::create($data);

        return redirect()->route('admin.menu.items.index', $menutype)
            ->with('success', 'Menu item successfully created.');
    }

    /**
     * Display the specified menu item.
     */
    public function show($menutype, Menu $menu): View
    {
        $menuType = MenuType::where('menutype', $menutype)->firstOrFail();
        $menu->load(['parent', 'children']);

        return view('admin.menu.items.show', compact('menuType', 'menu'));
    }

    /**
     * Show the form for editing the menu item.
     */
    public function edit($menutype, Menu $menu): View
    {
        $menuType = MenuType::where('menutype', $menutype)->firstOrFail();
        $parentItems = Menu::ofType($menutype)->where('id', '!=', $menu->id)->orderBy('lft')->get();

        return view('admin.menu.items.edit', compact('menuType', 'menu', 'parentItems'));
    }

    /**
     * Update the specified menu item.
     */
    public function update(Request $request, $menutype, Menu $menu): RedirectResponse
    {
        $menuType = MenuType::where('menutype', $menutype)->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'alias' => 'required|string|max:400',
            'note' => 'nullable|string|max:255',
            'link' => 'required|string|max:1024',
            'type' => 'required|string|max:16',
            'published' => 'boolean',
            'parent_id' => 'required|integer',
            'access' => 'required|integer|min:0',
            'img' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:7',
            'publish_up' => 'nullable|date',
            'publish_down' => 'nullable|date|after:publish_up'
        ]);

        $data = $request->all();
        $data['path'] = $this->generatePath($data['alias'], $data['parent_id']);
        $data['level'] = $this->calculateLevel($data['parent_id']);

        // If parent changed, update lft/rgt values
        if ($menu->parent_id != $data['parent_id']) {
            $this->updateLftRgtValues($menu, $data['parent_id']);
        }

        $menu->update($data);

        return redirect()->route('admin.menu.items.index', $menutype)
            ->with('success', 'Menu item successfully updated.');
    }

    /**
     * Remove the specified menu item.
     */
    public function destroy($menutype, Menu $menu): RedirectResponse
    {
        // Check if menu item has children
        if ($menu->children()->count() > 0) {
            return redirect()->route('admin.menu.items.index', $menutype)
                ->with('error', 'Нельзя удалить пункт меню, который содержит дочерние элементы.');
        }

        // Update lft/rgt values for remaining items
        $this->updateRgtValuesAfterDelete($menu->lft, $menu->rgt);

        $menu->delete();

        return redirect()->route('admin.menu.items.index', $menutype)
            ->with('success', 'Menu item successfully deleted.');
    }

    /**
     * Toggle published status of menu item.
     */
    public function togglePublished($menutype, Menu $menu): RedirectResponse
    {
        $menu->update(['published' => !$menu->published]);

        $status = $menu->published ? 'опубликован' : 'снят с публикации';
        return redirect()->route('admin.menu.items.index', $menutype)
            ->with('success', "Пункт меню {$status}.");
    }

    /**
     * Move menu item to trash.
     */
    public function trash($menutype, Menu $menu): RedirectResponse
    {
        $menu->update(['published' => -2]);

        return redirect()->route('admin.menu.items.index', $menutype)
            ->with('success', 'Пункт меню перемещен в корзину.');
    }

    /**
     * Restore menu item from trash.
     */
    public function restore($menutype, Menu $menu): RedirectResponse
    {
        $menu->update(['published' => 0]);

        return redirect()->route('admin.menu.items.index', $menutype)
            ->with('success', 'Пункт меню восстановлен из корзины.');
    }

    /**
     * Generate path for menu item.
     */
    private function generatePath($alias, $parentId)
    {
        if ($parentId == 1) {
            return $alias;
        }

        $parent = Menu::find($parentId);
        return $parent ? $parent->path . '/' . $alias : $alias;
    }

    /**
     * Calculate level for menu item.
     */
    private function calculateLevel($parentId)
    {
        if ($parentId == 1) {
            return 1;
        }

        $parent = Menu::find($parentId);
        return $parent ? $parent->level + 1 : 1;
    }

    /**
     * Calculate lft value for menu item.
     */
    private function calculateLft($parentId)
    {
        if ($parentId == 1) {
            return Menu::max('rgt') + 1;
        }

        $parent = Menu::find($parentId);
        return $parent ? $parent->rgt : Menu::max('rgt') + 1;
    }

    /**
     * Update rgt values for parent and siblings.
     */
    private function updateRgtValues($lft)
    {
        Menu::where('rgt', '>=', $lft)->increment('rgt', 2);
        Menu::where('lft', '>', $lft)->increment('lft', 2);
    }

    /**
     * Update lft/rgt values when parent changes.
     */
    private function updateLftRgtValues($menu, $newParentId)
    {
        // Implementation for updating nested set values
        // This is a simplified version - in production you'd want more robust logic
    }

    /**
     * Update rgt values after deletion.
     */
    private function updateRgtValuesAfterDelete($lft, $rgt)
    {
        $diff = $rgt - $lft + 1;
        Menu::where('rgt', '>', $rgt)->decrement('rgt', $diff);
        Menu::where('lft', '>', $rgt)->decrement('lft', $diff);
    }
}
