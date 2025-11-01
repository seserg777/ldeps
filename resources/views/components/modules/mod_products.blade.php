{{-- Products Module Type --}}
@php
    $params = $module->params_array;
    $limit = (int)($params['limit'] ?? 6);
    $type = $params['type'] ?? 'latest';
    $categoryId = $params['category_id'] ?? null;
    
    // Build products query
    $query = \App\Models\Product\Product::published();
    
    if ($categoryId) {
        $query->whereHas('categories', function($q) use ($categoryId) {
            $q->where('vjprf_jshopping_products_to_categories.category_id', $categoryId);
        });
    }
    
    switch ($type) {
        case 'random':
            $query->inRandomOrder();
            break;
        case 'popular':
            $query->popular();
            break;
        case 'latest':
        default:
            $query->orderBy('product_date_added', 'desc');
            break;
    }
    
    $products = $query->limit($limit)->get();
@endphp

@if($products->count() > 0)
    <div class="module-products pg">
        <div class="pg-grid">
            @foreach($products as $product)
                <a class="pg-card" href="{{ route('products.show-by-path', $product->full_path) }}">
                    <div class="pg-img">
                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}">
                    </div>
                    <div class="pg-title">{{ $product->name }}</div>
                    <div class="pg-price">{{ $product->formatted_price }}</div>
                </a>
            @endforeach
        </div>
    </div>
@endif

