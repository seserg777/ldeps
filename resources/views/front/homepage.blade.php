@extends('share.layouts.base')

@php
    $componentType = 'homepage';
@endphp

@section('title', 'Главная страница')
@section('description', 'Интернет-магазин с широким ассортиментом качественных товаров')

@section('content')
<div id="vue-homepage">
    <homepage-component
        :menu-items='@json($menuItems)'
        site-name="{{ $siteName }}"
        site-description="{{ $siteDescription }}"
    ></homepage-component>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    window.addToCart = function(productId) {
        const button = document.querySelector(`[data-product-id="${productId}"]`);
        if (!button) return;

        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.remove('btn-primary');
                button.classList.add('btn-success');
                
                // Update cart count
                updateCartCount();
                
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                    button.disabled = false;
                }, 2000);
            } else {
                button.innerHTML = originalHtml;
                button.disabled = false;
                console.error('Error adding to cart:', data.message);
            }
        })
        .catch(error => {
            button.innerHTML = originalHtml;
            button.disabled = false;
            console.error('Error:', error);
        });
    }

    // Add to wishlist functionality
    window.addToWishlist = function(productId) {
        const button = document.querySelector(`[data-product-id="${productId}"]`);
        if (!button) return;

        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch('/wishlist/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.remove('btn-outline-danger');
                button.classList.add('btn-success');
                
                // Update wishlist count
                updateWishlistCount();
                
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-danger');
                    button.disabled = false;
                }, 2000);
            } else {
                button.innerHTML = originalHtml;
                button.disabled = false;
                console.error('Error adding to wishlist:', data.message);
            }
        })
        .catch(error => {
            button.innerHTML = originalHtml;
            button.disabled = false;
            console.error('Error:', error);
        });
    }

    // Update cart count
    function updateCartCount() {
        fetch('/cart/count')
            .then(response => response.json())
            .then(data => {
                const cartCountElement = document.getElementById('cart-count');
                if (cartCountElement && data.count !== undefined) {
                    cartCountElement.textContent = data.count;
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }

    // Update wishlist count
    function updateWishlistCount() {
        fetch('/wishlist/count')
            .then(response => response.json())
            .then(data => {
                const wishlistCountElement = document.getElementById('wishlist-count');
                if (wishlistCountElement && data.count !== undefined) {
                    wishlistCountElement.textContent = data.count;
                }
            })
            .catch(error => console.error('Error updating wishlist count:', error));
    }

    // Initialize counts on page load
    updateCartCount();
    updateWishlistCount();
});
</script>
@endpush
