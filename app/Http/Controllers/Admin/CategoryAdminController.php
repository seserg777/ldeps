<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;
use Illuminate\Http\Request;

class CategoryAdminController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with(['parent', 'children'])
            ->orderBy('category_id', 'desc')
            ->get();

        $columns = [
            [
                'key' => 'category_id',
                'label' => 'ID',
                'sortable' => true
            ],
            [
                'key' => 'name',
                'label' => 'Название',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'parent.name',
                'label' => 'Родительская категория',
                'sortable' => true,
                'render' => function ($item) {
                    return $item->parent ? $item->parent->name : '<span class="text-muted">Корневая</span>';
                }
            ],
            [
                'key' => 'category_publish',
                'label' => 'Статус',
                'sortable' => true,
                'format' => 'status'
            ],
            [
                'key' => 'ordering',
                'label' => 'Порядок',
                'sortable' => true
            ],
        ];

        $sortableColumns = ['category_id', 'name', 'category_publish', 'ordering'];

        $actions = [
            [
                'label' => 'Редактировать',
                'icon' => 'edit',
                'class' => 'primary',
                'url' => function ($item) {
                    return route('admin.categories.edit', $item->category_id);
                }
            ],
            [
                'label' => 'Просмотр',
                'icon' => 'eye',
                'class' => 'info',
                'url' => function ($item) {
                    return route('category.show', $item->category_id);
                }
            ],
            [
                'label' => 'Удалить',
                'icon' => 'trash',
                'class' => 'danger',
                'url' => function ($item) {
                    return route('admin.categories.destroy', $item->category_id);
                },
                'condition' => function ($item) {
                    return $item->children->count() === 0; // Only delete if no children
                }
            ]
        ];

        $bulkActions = [
            [
                'key' => 'delete',
                'label' => 'Удалить выбранные'
            ],
            [
                'key' => 'activate',
                'label' => 'Активировать'
            ],
            [
                'key' => 'deactivate',
                'label' => 'Деактивировать'
            ]
        ];

        return view('admin.categories.index', compact(
            'categories',
            'columns',
            'sortableColumns',
            'actions',
            'bulkActions'
        ));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_publish' => 'boolean',
            'ordering' => 'integer|min:0'
        ]);

        Category::create($request->all());

        return redirect()->route('admin.categories')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_publish' => 'boolean',
            'ordering' => 'integer|min:0'
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());

        return redirect()->route('admin.categories')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories')
            ->with('success', 'Category deleted successfully.');
    }
}
