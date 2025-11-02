@php
    // Get also bought products using the relationship
    $alsoBoughtProducts = $product->alsoBoughtProducts()
        ->select([
            'vjprf_jshopping_products.product_id',
            'vjprf_jshopping_products.name_' . app()->getLocale() . '-' . (app()->getLocale() === 'en' ? 'GB' : 'UA') . ' as name',
            'vjprf_jshopping_products.product_thumb_image as image',
            'vjprf_jshopping_products.product_price',
            'vjprf_jshopping_products.alias_' . app()->getLocale() . '-' . (app()->getLocale() === 'en' ? 'GB' : 'UA') . ' as alias',
        ])
        ->limit(6)
        ->get();
@endphp

@if($alsoBoughtProducts->count() > 0)
<section class="also-bought-products mt-5">
    <div class="container">
        <h3 class="mb-4">
            <i class="fas fa-shopping-basket me-2"></i>
            {{ __('З цим товаром купують') }}
        </h3>
        
        <div class="row g-4">
            @foreach($alsoBoughtProducts as $alsoProduct)
                <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                    <div class="product-card h-100">
                        <a href="{{ route('products.show', $alsoProduct->product_id) }}" class="text-decoration-none">
                            {{-- Product Image --}}
                            <div class="product-image-wrapper mb-3">
                                @if($alsoProduct->image)
                                    <img 
                                        src="{{ asset('images/product/thumb/' . $alsoProduct->image) }}" 
                                        alt="{{ $alsoProduct->name }}"
                                        class="img-fluid rounded"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="placeholder-image bg-light rounded d-flex align-items-center justify-center" style="min-height: 150px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Product Name --}}
                            <h6 class="product-title text-dark mb-2" style="font-size: 0.9rem; line-height: 1.3;">
                                {{ Str::limit($alsoProduct->name, 60) }}
                            </h6>

                            {{-- Product Price --}}
                            <div class="product-price">
                                <span class="text-success fw-bold">
                                    {{ number_format($alsoProduct->product_price, 2) }} ₴
                                </span>
                            </div>

                            {{-- Add to Cart Button --}}
                            <button 
                                type="button"
                                class="btn btn-sm btn-outline-success mt-2 w-100"
                                @click.prevent="addToCart({{ $alsoProduct->product_id }})"
                                title="{{ __('Додати в кошик') }}"
                            >
                                <i class="fas fa-cart-plus me-1"></i>
                                {{ __('В кошик') }}
                            </button>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
.also-bought-products .product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    padding: 1rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: white;
}

.also-bought-products .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.also-bought-products .product-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
}

.also-bought-products .product-image-wrapper img {
    transition: transform 0.3s ease;
}

.also-bought-products .product-card:hover .product-image-wrapper img {
    transform: scale(1.05);
}

.also-bought-products .product-title {
    min-height: 2.6rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.also-bought-products .placeholder-image {
    background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
}
</style>
@endif

