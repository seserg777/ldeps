@php
    // Get also bought products using the relationship
    $locale = app()->getLocale();
    $localeSuffix = $locale === 'en' ? 'en-GB' : ($locale === 'uk' ? 'uk-UA' : 'ru-UA');
    
    $alsoBoughtProducts = $product->alsoBoughtProducts()
        ->select([
            'vjprf_jshopping_products.product_id',
            'vjprf_jshopping_products.name_' . $localeSuffix,
            'vjprf_jshopping_products.product_thumb_image',
            'vjprf_jshopping_products.product_price',
            'vjprf_jshopping_products.alias_' . $localeSuffix,
        ])
        ->limit(12)
        ->get();
@endphp

@if($alsoBoughtProducts->count() > 0)
<section class="also-bought-products mt-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">
                <i class="fas fa-shopping-basket me-2"></i>
                {{ __('З цим товаром купують') }}
            </h3>
            <div class="swiper-navigation d-flex gap-2">
                <button class="swiper-button-prev-custom btn btn-outline-secondary btn-sm">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="swiper-button-next-custom btn btn-outline-secondary btn-sm">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        {{-- Swiper Container --}}
        <div class="swiper also-bought-swiper">
            <div class="swiper-wrapper">
                @foreach($alsoBoughtProducts as $alsoProduct)
                    <div class="swiper-slide">
                        <div class="product-card h-100">
                            <a href="{{ route('products.show', $alsoProduct->product_id) }}" class="text-decoration-none">
                                {{-- Product Image --}}
                                <div class="product-image-wrapper mb-3">
                                    @if($alsoProduct->product_thumb_image)
                                        <img 
                                            src="{{ asset('images/product/thumb/' . $alsoProduct->product_thumb_image) }}" 
                                            alt="{{ $alsoProduct->{'name_' . $localeSuffix} ?? 'Product' }}"
                                            class="img-fluid rounded"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="placeholder-image bg-light rounded d-flex align-items-center justify-content-center" style="min-height: 150px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Name --}}
                                <h6 class="product-title text-dark mb-2">
                                    {{ Str::limit($alsoProduct->{'name_' . $localeSuffix} ?? 'Без назви', 60) }}
                                </h6>

                                {{-- Product Price --}}
                                <div class="product-price mb-2">
                                    <span class="text-success fw-bold">
                                        {{ number_format($alsoProduct->product_price, 2) }} ₴
                                    </span>
                                </div>

                                {{-- Add to Cart Button --}}
                                <button 
                                    type="button"
                                    class="btn btn-sm btn-outline-success w-100"
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
            
            {{-- Pagination --}}
            <div class="swiper-pagination mt-3"></div>
        </div>
    </div>
</section>

{{-- Swiper CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

{{-- Swiper JS --}}
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.also-bought-products .product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    padding: 1rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.also-bought-products .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.also-bought-products .product-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    background: #f8f9fa;
}

.also-bought-products .product-image-wrapper img {
    transition: transform 0.3s ease;
    width: 100%;
    height: auto;
    object-fit: cover;
}

.also-bought-products .product-card:hover .product-image-wrapper img {
    transform: scale(1.05);
}

.also-bought-products .product-title {
    font-size: 0.9rem;
    line-height: 1.3;
    min-height: 2.6rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.also-bought-products .placeholder-image {
    background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
}

.also-bought-products .swiper-navigation button {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.also-bought-products .swiper-navigation button:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.also-bought-products .swiper-pagination {
    position: static;
    margin-top: 1rem;
}

.also-bought-products .swiper-pagination-bullet {
    background: #6c757d;
}

.also-bought-products .swiper-pagination-bullet-active {
    background: #28a745;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const alsoBoughtSwiper = new Swiper('.also-bought-swiper', {
        // Responsive breakpoints
        slidesPerView: 2,
        spaceBetween: 15,
        breakpoints: {
            // Mobile (>= 576px)
            576: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            // Tablet (>= 768px)
            768: {
                slidesPerView: 3,
                spaceBetween: 20
            },
            // Desktop (>= 992px)
            992: {
                slidesPerView: 4,
                spaceBetween: 20
            },
            // Large Desktop (>= 1200px)
            1200: {
                slidesPerView: 6,
                spaceBetween: 20
            }
        },
        // Navigation
        navigation: {
            nextEl: '.swiper-button-next-custom',
            prevEl: '.swiper-button-prev-custom',
        },
        // Pagination
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        // Loop mode
        loop: false,
        // Lazy loading
        lazy: true,
        // Auto height
        autoHeight: false,
        // Grab cursor
        grabCursor: true,
    });
});
</script>
@endif

