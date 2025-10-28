@extends('layouts.app')

@section('title', 'Кошик')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-cart me-2"></i>Кошик</h2>
                <span class="badge bg-primary fs-6" id="cart-total-count">{{ $products->sum('quantity') }}</span>
            </div>

            @if($products->count() > 0)
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div id="cart-items">
                                    @foreach($products as $product)
                                        <div class="row align-items-center mb-3 pb-3 border-bottom" id="cart-item-{{ $product->product_id }}">
                                            <div class="col-md-2">
                                                <img src="{{ $product->thumbnail_url }}" class="img-fluid rounded" alt="{{ $product->name }}">
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('products.show-by-path', $product->full_path) }}" class="text-decoration-none">
                                                        {{ $product->name }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">{{ $product->manufacturer_name }}</small>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="input-group input-group-sm">
                                                    <button class="btn btn-outline-secondary quantity-decrease" data-product-id="{{ $product->product_id }}">-</button>
                                                    <input type="number" class="form-control text-center quantity-input" 
                                                           value="{{ $product->quantity }}" min="1" max="99" 
                                                           data-product-id="{{ $product->product_id }}">
                                                    <button class="btn btn-outline-secondary quantity-increase" data-product-id="{{ $product->product_id }}">+</button>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <span class="h6 text-primary product-price" data-price="{{ $product->product_price }}">
                                                    {{ number_format($product->product_price * $product->quantity, 2) }} ₴
                                                </span>
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-outline-danger btn-sm remove-from-cart" data-product-id="{{ $product->product_id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Підсумок замовлення</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Товари:</span>
                                    <span id="subtotal">{{ number_format($subtotal, 2) }} ₴</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Доставка:</span>
                                    <span>Безкоштовно</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between h5">
                                    <span>Всього:</span>
                                    <span id="total">{{ number_format($subtotal, 2) }} ₴</span>
                                </div>
                                
                                <div class="d-grid gap-2 mt-3">
                                    <button class="btn btn-primary btn-lg" id="checkout-btn">
                                        <i class="fas fa-credit-card me-2"></i>Оформити замовлення
                                    </button>
                                    <button class="btn btn-outline-danger" id="clear-cart-btn">
                                        <i class="fas fa-trash me-2"></i>Очистити кошик
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Кошик порожній</h4>
                    <p class="text-muted">Додайте товари до кошика, щоб вони з'явилися тут</p>
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
    // Update quantity
    $('.quantity-input').on('change', function() {
        const productId = $(this).data('product-id');
        const quantity = parseInt($(this).val());
        updateQuantity(productId, quantity);
    });

    $('.quantity-increase').on('click', function() {
        const productId = $(this).data('product-id');
        const input = $(`input[data-product-id="${productId}"]`);
        const newQuantity = parseInt(input.val()) + 1;
        input.val(newQuantity);
        updateQuantity(productId, newQuantity);
    });

    $('.quantity-decrease').on('click', function() {
        const productId = $(this).data('product-id');
        const input = $(`input[data-product-id="${productId}"]`);
        const newQuantity = Math.max(1, parseInt(input.val()) - 1);
        input.val(newQuantity);
        updateQuantity(productId, newQuantity);
    });

    // Remove from cart
    $('.remove-from-cart').on('click', function() {
        const productId = $(this).data('product-id');
        
        $.ajax({
            url: '{{ route("cart.remove") }}',
            method: 'POST',
            data: {
                product_id: productId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#cart-item-' + productId).fadeOut(300, function() {
                        $(this).remove();
                        updateCartCount();
                        updateTotals();
                        
                        // Check if cart is empty
                        if ($('#cart-items .row').length === 0) {
                            location.reload();
                        }
                    });
                }
            }
        });
    });

    // Clear cart
    $('#clear-cart-btn').on('click', function() {
        if (confirm('Ви впевнені, що хочете очистити кошик?')) {
            $.ajax({
                url: '{{ route("cart.clear") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }
    });

    function updateQuantity(productId, quantity) {
        $.ajax({
            url: '{{ route("cart.update") }}',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    updateCartCount();
                    updateTotals();
                }
            }
        });
    }

    function updateTotals() {
        let subtotal = 0;
        $('.product-price').each(function() {
            const price = parseFloat($(this).data('price'));
            const quantity = parseInt($(this).closest('.row').find('.quantity-input').val());
            const total = price * quantity;
            $(this).text(total.toFixed(2) + ' ₴');
            subtotal += total;
        });
        
        $('#subtotal').text(subtotal.toFixed(2) + ' ₴');
        $('#total').text(subtotal.toFixed(2) + ' ₴');
    }

    function updateCartCount() {
        $.get('{{ route("cart.count") }}', function(response) {
            $('#cart-count').text(response.count);
            $('#cart-total-count').text(response.count);
        });
    }
});
</script>
@endpush
