<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use Illuminate\Http\Request;

class ProductAdminController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['categories', 'manufacturer'])
            ->orderBy('product_id', 'desc')
            ->get();

        $columns = [
            [
                'key' => 'product_id',
                'label' => 'ID',
                'sortable' => true
            ],
            [
                'key' => 'product_name',
                'label' => 'Название',
                'sortable' => true,
                'searchable' => true
            ],
            [
                'key' => 'manufacturer.name',
                'label' => 'Производитель',
                'sortable' => true
            ],
            [
                'key' => 'product_price',
                'label' => 'Цена',
                'sortable' => true,
                'format' => 'currency'
            ],
            [
                'key' => 'product_quantity',
                'label' => 'Количество',
                'sortable' => true
            ],
            [
                'key' => 'product_publish',
                'label' => 'Статус',
                'sortable' => true,
                'format' => 'status'
            ],
        ];

        $sortableColumns = ['product_id', 'product_name', 'product_price', 'product_quantity', 'product_publish'];

        $actions = [
            [
                'label' => 'Редактировать',
                'icon' => 'edit',
                'class' => 'primary',
                'url' => function ($item) {
                    return route('admin.products.edit', $item->product_id);
                }
            ],
            [
                'label' => 'Просмотр',
                'icon' => 'eye',
                'class' => 'info',
                'url' => function ($item) {
                    return route('products.show', $item->product_id);
                }
            ],
            [
                'label' => 'Удалить',
                'icon' => 'trash',
                'class' => 'danger',
                'url' => function ($item) {
                    return route('admin.products.destroy', $item->product_id);
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

        return view('admin.products.index', compact(
            'products',
            'columns',
            'sortableColumns',
            'actions',
            'bulkActions'
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        // TODO: Implement product creation
        return redirect()->route('admin.products')->with('success', 'Product created successfully');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, $id)
    {
        // TODO: Implement product update
        return redirect()->route('admin.products')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified product.
     */
    public function destroy($id)
    {
        // TODO: Implement product deletion
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully');
    }
}
