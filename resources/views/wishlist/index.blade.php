@extends('layouts.app')

@section('title', 'Список бажань')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-heart me-2"></i>Список бажань</h2>
                <span class="badge bg-primary fs-6" id="wishlist-total-count">{{ $products->count() }}</span>
            </div>

            @if($products->count() > 0)
                <div class="row" id="wishlist-products">
                    @foreach($products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4" id="wishlist-product-{{ $product->product_id }}">
                            <div class="card h-100 product-card">
                                <div class="position-relative">
                                    <img src="{{ $product->thumbnail_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                                    <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 remove-from-wishlist" 
                                            data-product-id="{{ $product->product_id }}" title="Видалити з бажань">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">{{ $product->name }}</h6>
                                    <p class="card-text text-muted small">{{ $product->short_description }}</p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="h5 text-primary mb-0">{{ $product->formatted_price }}</span>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary add-to-cart" data-product-id="{{ $product->product_id }}">
                                                <i class="fas fa-shopping-cart me-1"></i>Додати в кошик
                                            </button>
                                            <a href="{{ route('products.show-by-path', $product->full_path) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Переглянути
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-heart-broken fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Список бажань порожній</h4>
                    <p class="text-muted">Додайте товари до списку бажань, щоб вони з'явилися тут</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Перейти до каталогу
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Remove from wishlist
    $('.remove-from-wishlist').on('click', function() {
        const productId = $(this).data('product-id');
        const button = $(this);
        
        $.ajax({
            url: '{{ route("wishlist.remove") }}',
            method: 'POST',
            data: {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#wishlist-product-' + productId).fadeOut(300, function() {
                        $(this).remove();
                        updateWishlistCount();
                        
                        // Check if wishlist is empty
                        if ($('#wishlist-products .col-lg-3').length === 0) {
                            location.reload();
                        }
                    });
                }
            }
        });
    });

    // Add to cart
    $('.add-to-cart').on('click', function() {
        const productId = $(this).data('product-id');
        const button = $(this);
        const originalText = button.html();
        
        button.html('<i class="fas fa-spinner fa-spin me-1"></i>Додаємо...');
        button.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: 1,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    button.html('<i class="fas fa-check me-1"></i>Додано!');
                    button.removeClass('btn-primary').addClass('btn-success');
                    updateCartCount();
                    
                    setTimeout(() => {
                        button.html(originalText);
                        button.removeClass('btn-success').addClass('btn-primary');
                        button.prop('disabled', false);
                    }, 2000);
                }
            },
            error: function() {
                button.html(originalText);
                button.prop('disabled', false);
            }
        });
    });

    function updateWishlistCount() {
        $.get('{{ route("wishlist.count") }}', function(response) {
            $('#wishlist-count').text(response.count);
            $('#wishlist-total-count').text(response.count);
        });
    }

    function updateCartCount() {
        $.get('{{ route("cart.count") }}', function(response) {
            $('#cart-count').text(response.count);
        });
    }
});
</script>
@endpush
