<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContentStoreRequest;
use App\Models\Content\Content;
use App\Models\Content\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ContentController extends Controller
{
    /**
     * Display a listing of content.
     */
    public function index(Request $request): View
    {
        $query = Content::with(['category']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('alias', 'like', "%{$search}%")
                  ->orWhere('introtext', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('catid', $request->category);
        }

        // Filter by state
        if ($request->filled('state')) {
            $state = $request->state;
            if ($state === 'published') {
                $query->where('state', 1);
            } elseif ($state === 'unpublished') {
                $query->where('state', 0);
            } elseif ($state === 'archived') {
                $query->where('state', 2);
            } elseif ($state === 'trashed') {
                $query->where('state', -2);
            }
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('featured', $request->featured);
        }

        // Filter by language
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        // Ordering
        $sortBy = $request->get('sort', 'created');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $content = $query->paginate(10);

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
                'key' => 'alias',
                'label' => 'Alias',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'category.title',
                'label' => 'Category',
                'sortable' => false
            ],
            [
                'key' => 'state',
                'label' => 'Status',
                'sortable' => true,
                'format' => 'content_status'
            ],
            [
                'key' => 'featured',
                'label' => 'Featured',
                'sortable' => true,
                'format' => 'boolean'
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
            ],
            [
                'key' => 'created',
                'label' => 'Created',
                'sortable' => true,
                'format' => 'datetime'
            ]
        ];

        $sortableColumns = ['id', 'title', 'alias', 'state', 'featured', 'language', 'hits', 'created'];

        $actions = [
            [
                'label' => 'View',
                'icon' => 'eye',
                'class' => 'info',
                'url' => function($item) {
                    return route('admin.content.show', $item);
                }
            ],
            [
                'label' => 'Edit',
                'icon' => 'edit',
                'class' => 'warning',
                'url' => function($item) {
                    return route('admin.content.edit', $item);
                }
            ],
            [
                'label' => 'Publish',
                'icon' => 'eye',
                'class' => 'success',
                'url' => function($item) {
                    return route('admin.content.toggle-published', $item);
                },
                'condition' => function($item) {
                    return $item->state == 0;
                }
            ],
            [
                'label' => 'Unpublish',
                'icon' => 'eye-slash',
                'class' => 'secondary',
                'url' => function($item) {
                    return route('admin.content.toggle-published', $item);
                },
                'condition' => function($item) {
                    return $item->state == 1;
                }
            ],
            [
                'label' => 'Archive',
                'icon' => 'archive',
                'class' => 'warning',
                'url' => function($item) {
                    return route('admin.content.archive', $item);
                },
                'condition' => function($item) {
                    return $item->state != 2;
                }
            ],
            [
                'label' => 'Move to Trash',
                'icon' => 'trash',
                'class' => 'danger',
                'url' => function($item) {
                    return route('admin.content.trash', $item);
                },
                'condition' => function($item) {
                    return $item->state != -2;
                }
            ],
            [
                'label' => 'Delete Permanently',
                'icon' => 'times',
                'class' => 'danger',
                'url' => function($item) {
                    return route('admin.content.destroy', $item);
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
                'key' => 'archive',
                'label' => 'Move to Archive'
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
                'key' => 'category',
                'label' => 'Category',
                'type' => 'select',
                'options' => [
                    '' => 'All Categories'
                ] + Category::pluck('title', 'id')->toArray()
            ],
            [
                'key' => 'state',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    '' => 'All Statuses',
                    'published' => 'Published',
                    'unpublished' => 'Unpublished',
                    'archived' => 'Archived',
                    'trashed' => 'In Trash'
                ]
            ],
            [
                'key' => 'featured',
                'label' => 'Featured',
                'type' => 'select',
                'options' => [
                    '' => 'All',
                    '1' => 'Featured',
                    '0' => 'Regular'
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

        return view('admin.content.index', compact(
            'content',
            'columns',
            'sortableColumns',
            'actions',
            'bulkActions',
            'filters'
        ));
    }

    /**
     * Show the form for creating new content.
     */
    public function create(): View
    {
        $categories = Category::orderBy('lft')->get();
        
        return view('admin.content.create', compact('categories'));
    }

    /**
     * Store a newly created content.
     */
    public function store(ContentStoreRequest $request): RedirectResponse
    {

        $data = $request->all();
        $data['created'] = now();
        $data['modified'] = now();
        $data['created_by'] = auth('custom')->id() ?? 1;
        $data['modified_by'] = auth('custom')->id() ?? 1;

        Content::create($data);

        return redirect()->route('admin.content.index')
            ->with('success', 'Content successfully created.');
    }

    /**
     * Display the specified content.
     */
    public function show(Content $content): View
    {
        $content->load(['category']);
        
        return view('admin.content.show', compact('content'));
    }

    /**
     * Show the form for editing the content.
     */
    public function edit(Content $content): View
    {
        $categories = Category::orderBy('lft')->get();
        
        return view('admin.content.edit', compact('content', 'categories'));
    }

    /**
     * Update the specified content.
     */
    public function update(Request $request, Content $content): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'alias' => 'required|string|max:400',
            'introtext' => 'nullable|string',
            'fulltext' => 'nullable|string',
            'state' => 'required|integer|in:0,1,2,-2',
            'catid' => 'required|integer|exists:vjprf_categories,id',
            'created_by_alias' => 'nullable|string|max:255',
            'publish_up' => 'nullable|date',
            'publish_down' => 'nullable|date|after:publish_up',
            'metakey' => 'nullable|string',
            'metadesc' => 'nullable|string',
            'access' => 'required|integer|min:0',
            'featured' => 'boolean',
            'language' => 'nullable|string|max:7',
            'note' => 'nullable|string|max:255'
        ]);

        $data = $request->all();
        $data['modified'] = now();
        $data['modified_by'] = auth('custom')->id() ?? 1;

        $content->update($data);

        return redirect()->route('admin.content.index')
            ->with('success', 'Content successfully updated.');
    }

    /**
     * Remove the specified content.
     */
    public function destroy(Content $content): RedirectResponse
    {
        $content->delete();

        return redirect()->route('admin.content.index')
            ->with('success', 'Content successfully deleted.');
    }

    /**
     * Toggle published status of content.
     */
    public function togglePublished(Content $content): RedirectResponse
    {
        $newState = $content->state == 1 ? 0 : 1;
        $content->update(['state' => $newState]);

        $status = $newState == 1 ? 'опубликован' : 'снят с публикации';
        return redirect()->route('admin.content.index')
            ->with('success', "Контент {$status}.");
    }

    /**
     * Archive content.
     */
    public function archive(Content $content): RedirectResponse
    {
        $content->update(['state' => 2]);

        return redirect()->route('admin.content.index')
            ->with('success', 'Контент перемещен в архив.');
    }

    /**
     * Move content to trash.
     */
    public function trash(Content $content): RedirectResponse
    {
        $content->update(['state' => -2]);

        return redirect()->route('admin.content.index')
            ->with('success', 'Контент перемещен в корзину.');
    }

    /**
     * Restore content from trash.
     */
    public function restore(Content $content): RedirectResponse
    {
        $content->update(['state' => 0]);

        return redirect()->route('admin.content.index')
            ->with('success', 'Контент восстановлен из корзины.');
    }

    /**
     * Toggle featured status of content.
     */
    public function toggleFeatured(Content $content): RedirectResponse
    {
        $content->update(['featured' => !$content->featured]);

        $status = $content->featured ? 'added to featured' : 'removed from featured';
        return redirect()->route('admin.content.index')
            ->with('success', "Контент {$status}.");
    }
}
