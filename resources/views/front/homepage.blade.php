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
            <nav class="site-menu menu-top">
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
                    <nav class="site-menu menu-main">
                        {!! $menuMainHtml ?? '' !!}
                    </nav>
                </div>

                <div class="col-3">
                    <div x-data="{
                            q: '',
                            open: false,
                            loading: false,
                            results: { products: [], categories: [], manufacturers: [] },
                            async search() {
                                const term = this.q.trim();
                                if (term.length < 2) { this.results = {products:[],categories:[],manufacturers:[]}; this.open = false; return; }
                                this.loading = true; this.open = true;
                                try {
                                    const res = await fetch(`/api/search?q=${encodeURIComponent(term)}`);
                                    if (res.ok) { this.results = await res.json(); }
                                } catch(e) { /* noop */ }
                                this.loading = false;
                            }
                        }" class="position-relative">
                        <div class="input-group">
                            <input type="search" class="form-control" placeholder="Поиск оборудования" x-model.debounce.300ms="q" @input="search()" @focus="open = q.length >= 2">
                            <button class="btn btn-outline-secondary" type="button" @click="search()"><i class="fas fa-search"></i></button>
                        </div>
                        <div class="position-absolute bg-white border rounded shadow p-3" x-show="open" @click.outside="open = false" style="top: 110%; left: 0; right: 0; z-index: 5000;">
                            <template x-if="loading">
                                <div class="text-center py-2">Идёт поиск…</div>
                            </template>
                            <template x-if="!loading">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <h6 class="mb-2">Товары</h6>
                                        <div class="list-group">
                                            <template x-for="p in (results.products || []).slice(0,5)" :key="p.id">
                                                <a class="list-group-item list-group-item-action" :href="p.url" x-text="p.name"></a>
                                            </template>
                                            <div class="text-muted small" x-show="(results.products||[]).length === 0">Ничего не найдено</div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Homepage content --}}
    <main>
      {!! $homepageHtml ?? '' !!}
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
