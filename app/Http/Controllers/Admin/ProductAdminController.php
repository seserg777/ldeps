<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        $product = Product::with(['productCharacteristics', 'productAttributes'])->findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, $id)
    {
        Log::info('Product update started', [
            'product_id' => $id,
            'request_data' => $request->except(['_token', '_method'])
        ]);

        try {
            $product = Product::findOrFail($id);
            
            DB::beginTransaction();
            
            // Update basic fields
            $updateData = [];
            
            // Multilang fields
            if ($request->has('name_uk-UA')) $updateData['name_uk-UA'] = $request->input('name_uk-UA');
            if ($request->has('name_ru-UA')) $updateData['name_ru-UA'] = $request->input('name_ru-UA');
            if ($request->has('name_en-GB')) $updateData['name_en-GB'] = $request->input('name_en-GB');
            
            if ($request->has('short_description_uk-UA')) $updateData['short_description_uk-UA'] = $request->input('short_description_uk-UA');
            if ($request->has('short_description_ru-UA')) $updateData['short_description_ru-UA'] = $request->input('short_description_ru-UA');
            if ($request->has('short_description_en-GB')) $updateData['short_description_en-GB'] = $request->input('short_description_en-GB');
            
            if ($request->has('description_uk-UA')) $updateData['description_uk-UA'] = $request->input('description_uk-UA');
            if ($request->has('description_ru-UA')) $updateData['description_ru-UA'] = $request->input('description_ru-UA');
            if ($request->has('description_en-GB')) $updateData['description_en-GB'] = $request->input('description_en-GB');
            
            // Other fields
            if ($request->has('product_code')) $updateData['product_code'] = $request->input('product_code');
            if ($request->has('product_ean')) $updateData['product_ean'] = $request->input('product_ean');
            if ($request->has('product_price')) $updateData['product_price'] = $request->input('product_price');
            if ($request->has('product_quantity')) $updateData['product_quantity'] = $request->input('product_quantity');
            if ($request->has('product_manufacturer_id')) $updateData['product_manufacturer_id'] = $request->input('product_manufacturer_id');
            
            $updateData['product_publish'] = $request->boolean('product_publish') ? 1 : 0;
            
            Log::info('Updating product with data', ['update_data' => $updateData]);
            
            $product->update($updateData);
            
            // Update characteristics
            if ($request->has('characteristics')) {
                foreach ($request->input('characteristics') as $fieldId => $valueId) {
                    if (empty($valueId)) {
                        // Remove characteristic if value is empty
                        \App\Models\ProductCharacteristic::where('product_id', $product->product_id)
                            ->where('extra_field', $fieldId)
                            ->delete();
                    } else {
                        // Update or create characteristic
                        \App\Models\ProductCharacteristic::updateOrCreate(
                            [
                                'product_id' => $product->product_id,
                                'extra_field' => $fieldId
                            ],
                            [
                                'extra_field_value' => $valueId
                            ]
                        );
                    }
                }
                
                Log::info('Product characteristics updated', [
                    'product_id' => $product->product_id,
                    'characteristics' => $request->input('characteristics')
                ]);
            }
            
            // Update attributes (variations)
            if ($request->has('attributes')) {
                // Delete old attributes that are not in request
                $keepIds = [];
                
                foreach ($request->input('attributes') as $attrData) {
                    if (!empty($attrData['product_attr_id'])) {
                        $keepIds[] = $attrData['product_attr_id'];
                    }
                }
                
                \App\Models\ProductAttribute::where('product_id', $product->product_id)
                    ->whereNotIn('product_attr_id', $keepIds)
                    ->delete();
                
                // Create or update attributes
                foreach ($request->input('attributes') as $attrData) {
                    if (!empty($attrData['product_attr_id'])) {
                        // Update existing
                        \App\Models\ProductAttribute::where('product_attr_id', $attrData['product_attr_id'])
                            ->update(array_merge($attrData, ['product_id' => $product->product_id]));
                    } else {
                        // Create new
                        \App\Models\ProductAttribute::create(array_merge($attrData, ['product_id' => $product->product_id]));
                    }
                }
                
                Log::info('Product attributes updated', [
                    'product_id' => $product->product_id,
                    'attributes_count' => count($request->input('attributes'))
                ]);
            }
            
            DB::commit();
            
            Log::info('Product updated successfully', ['product_id' => $product->product_id]);
            
            return redirect()->back()->with('success', 'Product updated successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Product update failed', [
                'product_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating product: ' . $e->getMessage());
        }
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
