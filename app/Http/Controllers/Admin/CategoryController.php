<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Models\Content\Category;
use App\Services\Admin\CategoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of categories.
     */
    public function index(Request $request): View
    {
        $query = Category::with(['parent', 'content']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('alias', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
            });
        }

        // Filter by extension
        if ($request->filled('extension')) {
            $query->where('extension', $request->extension);
        }

        // Filter by published status
        if ($request->filled('published')) {
            $query->where('published', $request->published);
        }

        // Filter by language
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        // Ordering
        $sortBy = $request->get('sort', 'lft');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $categories = $query->paginate(10);

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
                'key' => 'extension',
                'label' => 'Extension',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'published',
                'label' => 'Status',
                'sortable' => true,
                'format' => 'status'
            ],
            [
                'key' => 'language',
                'label' => 'Language',
                'sortable' => true
            ],
            [
                'key' => 'hits',
                'label' => 'Views',
                'sortable' => true
            ]
        ];

        $sortableColumns = ['id', 'title', 'alias', 'extension', 'published', 'language', 'hits'];

        $actions = [
            [
                'label' => 'Content',
                'icon' => 'file-alt',
                'class' => 'info',
                'url' => function ($item) {
                    return route('admin.content.index', ['category' => $item->id]);
                }
            ],
            [
                'label' => 'Edit',
                'icon' => 'edit',
                'class' => 'warning',
                'url' => function ($item) {
                    return route('admin.categories.edit', $item);
                }
            ],
            [
                'label' => 'Publish',
                'icon' => 'eye',
                'class' => 'success',
                'url' => function ($item) {
                    return route('admin.categories.toggle-published', $item);
                },
                'condition' => function ($item) {
                    return $item->published == 0;
                }
            ],
            [
                'label' => 'Unpublish',
                'icon' => 'eye-slash',
                'class' => 'secondary',
                'url' => function ($item) {
                    return route('admin.categories.toggle-published', $item);
                },
                'condition' => function ($item) {
                    return $item->published == 1;
                }
            ],
            [
                'label' => 'Delete',
                'icon' => 'trash',
                'class' => 'danger',
                'url' => function ($item) {
                    return route('admin.categories.destroy', $item);
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
                'key' => 'delete',
                'label' => 'Delete Selected'
            ]
        ];

        $filters = [
            [
                'key' => 'extension',
                'label' => 'Extension',
                'type' => 'select',
                'options' => [
                    '' => 'All Extensions',
                    'com_content' => 'Content',
                    'com_news' => 'News',
                    'com_blog' => 'Blog'
                ]
            ],
            [
                'key' => 'published',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    '' => 'All Statuses',
                    '1' => 'Published',
                    '0' => 'Unpublished'
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

        return view('admin.content.categories.index', compact(
            'categories',
            'columns',
            'sortableColumns',
            'actions',
            'bulkActions',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        $parentCategories = Category::where('level', '<', 3)->orderBy('lft')->get();

        return view('admin.content.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(CategoryStoreRequest $request): RedirectResponse
    {

        $data = $this->categoryService->prepareCategoryData($request->all());

        // Update rgt values for parent and siblings
        $this->categoryService->updateRgtValues($data['lft']);

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category successfully created.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): View
    {
        $category->load(['parent', 'children', 'content']);

        return view('admin.content.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the category.
     */
    public function edit(Category $category): View
    {
        $parentCategories = Category::where('id', '!=', $category->id)->orderBy('lft')->get();

        return view('admin.content.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'alias' => 'required|string|max:400',
            'note' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'published' => 'boolean',
            'parent_id' => 'required|integer',
            'access' => 'required|integer|min:0',
            'extension' => 'required|string|max:50',
            'language' => 'nullable|string|max:7',
            'metadesc' => 'nullable|string|max:1024',
            'metakey' => 'nullable|string|max:1024'
        ]);

        $data = $request->all();
        $data['path'] = $this->generatePath($data['alias'], $data['parent_id']);
        $data['level'] = $this->calculateLevel($data['parent_id']);
        $data['modified_time'] = now();

        // If parent changed, update lft/rgt values
        if ($category->parent_id != $data['parent_id']) {
            $this->updateLftRgtValues($category, $data['parent_id']);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category successfully updated.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Check if category has children
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Нельзя удалить категорию, которая содержит дочерние категории.');
        }

        // Check if category has content
        if ($category->content()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Нельзя удалить категорию, которая содержит контент.');
        }

        // Update lft/rgt values for remaining categories
        $this->updateRgtValuesAfterDelete($category->lft, $category->rgt);

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category successfully deleted.');
    }

    /**
     * Toggle published status of category.
     */
    public function togglePublished(Category $category): RedirectResponse
    {
        $category->update(['published' => !$category->published]);

        $status = $category->published ? 'published' : 'unpublished';
        return redirect()->route('admin.categories.index')
            ->with('success', "Category {$status}.");
    }

}
