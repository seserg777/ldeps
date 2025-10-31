@extends('share.layouts.base')

@php
    $componentType = 'homepage';
@endphp

@section('title', 'Главная страница')
@section('description', 'Интернет-магазин с широким ассортиментом качественных товаров')

@section('content')
  <div class="page home">
    <section class="top">
        <div class="container">
            {{-- Top menu SSR --}}
            <nav class="site-menu menu-top menu-main-menu-add">
                {!! $menuTopHtml ?? '' !!}
            </nav>
        </div>
    </section>
    
    <header>
        <div class="container">
            <div class="row">
                <div class="col-2">
                    <a href="/" class="logo">
                        <img src="{{ asset('images/logo.svg') }}" alt="Logo">
                    </a>
                </div>
            
                <div class="col-7">
                    {{-- Main menu SSR --}}
                    <nav class="site-menu menu-main menu-mainmenu-rus">
                        {!! $menuMainHtml ?? '' !!}
                    </nav>
                </div>

                <div class="col-3">
                    @include('share.layouts.partials.search')
                </div>
            </div>
        </div>
    </header>

    {{-- Homepage content --}}
    <main>
      {!! $homepageHtml ?? '' !!}
      @include('share.layouts.partials.products_module', ['type' => 'random', 'limit' => 3])
    </main>

    @isset($footerHtml)
      <footer>{!! $footerHtml !!}</footer>
    @endisset
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
