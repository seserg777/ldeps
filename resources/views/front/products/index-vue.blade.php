@extends('share.layouts.app')

@section('title', 'Каталог товарів')

@section('content')
<div id="vue-products-app">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="filter-sidebar">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-filter me-2"></i>Фільтри
                </h5>
                
                <!-- Price Filter -->
                <div class="mb-4">
                    <h6 class="fw-bold">Ціна</h6>
                    <div class="row">
                        <div class="col-6">
                            <input 
                                type="number" 
                                class="form-control form-control-sm" 
                                placeholder="Від" 
                                v-model="filters.priceMin"
                                @input="applyFilters"
                            >
                        </div>
                        <div class="col-6">
                            <input 
                                type="number" 
                                class="form-control form-control-sm" 
                                placeholder="До" 
                                v-model="filters.priceMax"
                                @input="applyFilters"
                            >
                        </div>
                    </div>
                </div>

                <!-- Manufacturers Filter -->
                <div class="mb-4" v-if="manufacturers.length > 0">
                    <h6 class="fw-bold">Виробники</h6>
                    <div class="list-group list-group-flush">
                        <label 
                            v-for="manufacturer in manufacturers" 
                            :key="manufacturer.manufacturer_id"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        >
                            <input 
                                type="checkbox" 
                                :value="manufacturer.manufacturer_id"
                                v-model="filters.manufacturers"
                                @change="applyFilters"
                                class="form-check-input me-2"
                            >
                            {{ manufacturer.name }}
                            <span class="badge bg-primary rounded-pill">{{ manufacturer.products_count }}</span>
                        </label>
                    </div>
                </div>

                <!-- Special Price Filter -->
                <div class="mb-4">
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            v-model="filters.specialPrice"
                            @change="applyFilters"
                            id="specialPrice"
                        >
                        <label class="form-check-label" for="specialPrice">
                            Тільки акційні товари
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Каталог товарів</h1>
                    <p class="text-muted mb-0">Знайдено {{ totalProducts }} товарів</p>
                </div>
                
                <!-- Sort Dropdown -->
                <div class="dropdown">
                    <button 
                        class="btn btn-outline-secondary dropdown-toggle" 
                        type="button" 
                        data-bs-toggle="dropdown"
                    >
                        <i class="fas fa-sort me-1"></i>Сортування
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#" @click.prevent="setSort('date_added', 'desc')">
                                Нові спочатку
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" @click.prevent="setSort('price', 'asc')">
                                Ціна: від низької до високої
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" @click.prevent="setSort('price', 'desc')">
                                Ціна: від високої до низької
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" @click.prevent="setSort('popularity', 'desc')">
                                Популярність
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" @click.prevent="setSort('name', 'asc')">
                                Назва: А-Я
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Search -->
            <div class="mb-4">
                <div class="input-group">
                    <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Пошук товарів..." 
                        v-model="searchQuery"
                        @input="debouncedSearch"
                    >
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Vue Components -->
            <Categories 
                :categories="categories"
                :loading="loading"
                :error="error"
                @add-to-cart="handleAddToCart"
                @add-to-wishlist="handleAddToWishlist"
            />

            <!-- Pagination -->
            <div v-if="pagination && pagination.last_page > 1" class="d-flex justify-content-center mt-4">
                <nav>
                    <ul class="pagination">
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <li 
                            v-for="page in visiblePages" 
                            :key="page"
                            class="page-item" 
                            :class="{ active: page === pagination.current_page }"
                        >
                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const { createApp } = Vue;
    
    const app = createApp({
        data() {
            return {
                categories: [],
                manufacturers: [],
                loading: false,
                error: null,
                totalProducts: 0,
                pagination: null,
                searchQuery: '',
                searchTimeout: null,
                filters: {
                    priceMin: null,
                    priceMax: null,
                    manufacturers: [],
                    specialPrice: false
                },
                sort: {
                    field: 'date_added',
                    direction: 'desc'
                }
            }
        },
        
        computed: {
            visiblePages() {
                if (!this.pagination) return [];
                const current = this.pagination.current_page;
                const last = this.pagination.last_page;
                const delta = 2;
                const range = [];
                const rangeWithDots = [];

                for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
                    range.push(i);
                }

                if (current - delta > 2) {
                    rangeWithDots.push(1, '...');
                } else {
                    rangeWithDots.push(1);
                }

                rangeWithDots.push(...range);

                if (current + delta < last - 1) {
                    rangeWithDots.push('...', last);
                } else {
                    rangeWithDots.push(last);
                }

                return rangeWithDots;
            }
        },
        
        methods: {
            async loadProducts() {
                this.loading = true;
                this.error = null;
                
                try {
                    const params = new URLSearchParams();
                    
                    // Add filters
                    if (this.filters.priceMin) params.append('price_min', this.filters.priceMin);
                    if (this.filters.priceMax) params.append('price_max', this.filters.priceMax);
                    if (this.filters.manufacturers.length > 0) {
                        params.append('manufacturers', this.filters.manufacturers.join(','));
                    }
                    if (this.filters.specialPrice) params.append('special_price', '1');
                    
                    // Add search
                    if (this.searchQuery) params.append('search', this.searchQuery);
                    
                    // Add sort
                    params.append('sort', this.sort.field);
                    params.append('direction', this.sort.direction);
                    
                    const response = await fetch(`/api/products?${params}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.categories = data.categories || [];
                        this.manufacturers = data.manufacturers || [];
                        this.totalProducts = data.total || 0;
                        this.pagination = data.pagination || null;
                    } else {
                        this.error = data.message || 'Помилка завантаження товарів';
                    }
                } catch (error) {
                    this.error = 'Помилка з\'єднання з сервером';
                    console.error('Error loading products:', error);
                } finally {
                    this.loading = false;
                }
            },
            
            debouncedSearch() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.loadProducts();
                }, 500);
            },
            
            applyFilters() {
                this.loadProducts();
            },
            
            setSort(field, direction) {
                this.sort.field = field;
                this.sort.direction = direction;
                this.loadProducts();
            },
            
            changePage(page) {
                if (page >= 1 && page <= this.pagination.last_page) {
                    this.loadProducts();
                }
            },
            
            handleAddToCart(productId) {
                window.addToCart(productId);
            },
            
            handleAddToWishlist(productId) {
                window.addToWishlist(productId);
            }
        },
        
        mounted() {
            this.loadProducts();
        }
    });
    
    // Register components
    app.component('Categories', window.Categories);
    app.component('Category', window.Category);
    app.component('ProductList', window.ProductList);
    app.component('Product', window.Product);
    
    app.mount('#vue-products-app');
});
</script>
@endpush
