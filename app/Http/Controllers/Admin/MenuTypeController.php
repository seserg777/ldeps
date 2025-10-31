<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu\MenuType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MenuTypeController extends Controller
{
    /**
     * Display a listing of menu types.
     */
    public function index(): View
    {
        $menuTypes = MenuType::withCount(['menuItems as published_count' => function ($query) {
            $query->where('published', 1);
        }])
            ->withCount(['menuItems as unpublished_count' => function ($query) {
                $query->where('published', 0);
            }])
            ->withCount(['menuItems as trash_count' => function ($query) {
                $query->where('published', -2);
            }])
            ->orderBy('ordering')
            ->orderBy('lft')
            ->paginate(10);

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
                'searchable' => true
            ],
            [
                'key' => 'menutype',
                'label' => 'Menu Type',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'description',
                'label' => 'Description',
                'searchable' => true
            ],
            [
                'key' => 'client_id',
                'label' => 'Client',
                'sortable' => true,
                'format' => 'client_type'
            ],
            [
                'key' => 'published_count',
                'label' => 'Published',
                'sortable' => true
            ],
            [
                'key' => 'unpublished_count',
                'label' => 'Unpublished',
                'sortable' => true
            ],
            [
                'key' => 'trash_count',
                'label' => 'In Trash',
                'sortable' => true
            ]
        ];

        $sortableColumns = ['id', 'title', 'menutype', 'client_id', 'published_count', 'unpublished_count', 'trash_count'];

        $actions = [
            [
                'label' => 'Menu Items',
                'icon' => 'list',
                'class' => 'info',
                'url' => function ($item) {
                    return route('admin.menu.items.index', $item->menutype);
                }
            ],
            [
                'label' => 'View',
                'icon' => 'eye',
                'class' => 'primary',
                'url' => function ($item) {
                    return route('admin.menu.types.show', $item);
                }
            ],
            [
                'label' => 'Edit',
                'icon' => 'edit',
                'class' => 'warning',
                'url' => function ($item) {
                    return route('admin.menu.types.edit', $item);
                }
            ],
            [
                'label' => 'Delete',
                'icon' => 'trash',
                'class' => 'danger',
                'url' => function ($item) {
                    return route('admin.menu.types.destroy', $item);
                },
                'condition' => function ($item) {
                    return $item->menuItems()->count() == 0;
                }
            ]
        ];

        $bulkActions = [
            [
                'key' => 'delete',
                'label' => 'Delete Selected'
            ]
        ];

        return view('admin.menu.types.index', compact(
            'menuTypes',
            'columns',
            'sortableColumns',
            'actions',
            'bulkActions'
        ));
    }

    /**
     * Show the form for creating a new menu type.
     */
    public function create(): View
    {
        return view('admin.menu.types.create');
    }

    /**
     * Store a newly created menu type.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'menutype' => 'required|string|max:24|unique:vjprf_menu_types,menutype',
            'title' => 'required|string|max:48',
            'description' => 'nullable|string|max:255',
            'client_id' => 'required|integer|in:0,1'
        ]);

        $data = $request->all();
        $data['ordering'] = MenuType::max('ordering') + 1;

        MenuType::create($data);

        return redirect()->route('admin.menu.types.index')
            ->with('success', 'Menu type successfully created.');
    }

    /**
     * Display the specified menu type.
     */
    public function show(MenuType $menuType): View
    {
        $menuType->load(['menuItems' => function ($query) {
            $query->orderBy('lft');
        }]);

        return view('admin.menu.types.show', compact('menuType'));
    }

    /**
     * Show the form for editing the menu type.
     */
    public function edit(MenuType $menuType): View
    {
        return view('admin.menu.types.edit', compact('menuType'));
    }

    /**
     * Update the specified menu type.
     */
    public function update(Request $request, MenuType $menuType): RedirectResponse
    {
        $request->validate([
            'menutype' => 'required|string|max:24|unique:vjprf_menu_types,menutype,' . $menuType->id,
            'title' => 'required|string|max:48',
            'description' => 'nullable|string|max:255',
            'client_id' => 'required|integer|in:0,1'
        ]);

        $menuType->update($request->all());

        return redirect()->route('admin.menu.types.index')
            ->with('success', 'Menu type successfully updated.');
    }

    /**
     * Remove the specified menu type.
     */
    public function destroy(MenuType $menuType): RedirectResponse
    {
        // Check if menu type has menu items
        if ($menuType->menuItems()->count() > 0) {
            return redirect()->route('admin.menu.types.index')
                ->with('error', 'Нельзя удалить тип меню, который содержит пункты меню.');
        }

        $menuType->delete();

        return redirect()->route('admin.menu.types.index')
            ->with('success', 'Menu type successfully deleted.');
    }

    /**
     * Update ordering of menu types.
     */
    public function updateOrdering(Request $request): RedirectResponse
    {
        $request->validate([
            'ordering' => 'required|array',
            'ordering.*' => 'integer'
        ]);

        foreach ($request->ordering as $id => $order) {
            MenuType::where('id', $id)->update(['ordering' => $order]);
        }

        return redirect()->route('admin.menu.types.index')
            ->with('success', 'Menu types order updated.');
    }
}
