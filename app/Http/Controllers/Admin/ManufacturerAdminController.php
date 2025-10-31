<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerAdminController extends Controller
{
    public function index(Request $request)
    {
        $manufacturers = Manufacturer::orderBy('manufacturer_id', 'desc')
            ->get();

        $columns = [
            [
                'key' => 'manufacturer_id',
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
                'key' => 'description',
                'label' => 'Описание',
                'searchable' => true,
                'render' => function ($item) {
                    return $item->description ? \Str::limit($item->description, 50) : '-';
                }
            ],
            [
                'key' => 'website',
                'label' => 'Сайт',
                'render' => function ($item) {
                    return $item->website ? '<a href="' . $item->website . '" target="_blank" class="text-decoration-none">' . \Str::limit($item->website, 30) . '</a>' : '-';
                }
            ],
            [
                'key' => 'ordering',
                'label' => 'Порядок',
                'sortable' => true
            ],
        ];

        $sortableColumns = ['manufacturer_id', 'name', 'ordering'];

        $actions = [
            [
                'label' => 'Редактировать',
                'icon' => 'edit',
                'class' => 'primary',
                'url' => function ($item) {
                    return route('admin.manufacturers.edit', $item->manufacturer_id);
                }
            ],
            [
                'label' => 'Просмотр товаров',
                'icon' => 'box',
                'class' => 'info',
                'url' => function ($item) {
                    return route('admin.products', ['manufacturer' => $item->manufacturer_id]);
                }
            ],
            [
                'label' => 'Удалить',
                'icon' => 'trash',
                'class' => 'danger',
                'url' => function ($item) {
                    return route('admin.manufacturers.destroy', $item->manufacturer_id);
                },
                'condition' => function ($item) {
                    return true; // Add your delete condition here
                }
            ]
        ];

        $bulkActions = [
            [
                'key' => 'delete',
                'label' => 'Удалить выбранные'
            ]
        ];

        return view('admin.manufacturers.index', compact(
            'manufacturers',
            'columns',
            'sortableColumns',
            'actions',
            'bulkActions'
        ));
    }

    /**
     * Show the form for creating a new manufacturer.
     */
    public function create()
    {
        return view('admin.manufacturers.create');
    }

    /**
     * Store a newly created manufacturer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordering' => 'integer|min:0'
        ]);

        Manufacturer::create($request->all());

        return redirect()->route('admin.manufacturers')
            ->with('success', 'Manufacturer created successfully.');
    }

    /**
     * Show the form for editing the specified manufacturer.
     */
    public function edit($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        return view('admin.manufacturers.edit', compact('manufacturer'));
    }

    /**
     * Update the specified manufacturer in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordering' => 'integer|min:0'
        ]);

        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->update($request->all());

        return redirect()->route('admin.manufacturers')
            ->with('success', 'Manufacturer updated successfully.');
    }

    /**
     * Remove the specified manufacturer from storage.
     */
    public function destroy($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->delete();

        return redirect()->route('admin.manufacturers')
            ->with('success', 'Manufacturer deleted successfully.');
    }
}
