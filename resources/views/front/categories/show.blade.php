@extends('share.layouts.app')

@php
    $componentType = 'category';
@endphp

@section('title', $category->name)

@section('products-count', $products->total())

@section('sidebar')
    <div class="filter-sidebar">
        <h5><i class="fas fa-filter me-2"></i>Фільтри</h5>
        
        <form id="filter-form">
            <!-- Price Range -->
            <div class="mb-3">
                <label class="form-label">Ціна</label>
                <div class="row">
                    <div class="col-6">
                        <input type="number" class="form-control" name="price_min" placeholder="Від" 
                               value="{{ request('price_min') }}">
                    </div>
                    <div class="col-6">
                        <input type="number" class="form-control" name="price_max" placeholder="До" 
                               value="{{ request('price_max') }}">
                    </div>
                </div>
                @if($priceRange)
                <small class="text-muted">
                    Діапазон: {{ number_format($priceRange->min_price, 0) }} - {{ number_format($priceRange->max_price, 0) }} ₴
                </small>
                @endif
            </div>

            <!-- Manufacturer -->
            <div class="mb-3">
                <label class="form-label">Виробник</label>
                @if(isset($manufacturers) && $manufacturers->count() > 0)
                    <div class="manufacturer-filters" style="max-height: 200px; overflow-y: auto;">
                        @foreach($manufacturers as $manufacturer)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="manufacturer[]" 
                                       value="{{ $manufacturer->manufacturer_id }}" 
                                       id="manufacturer_{{ $manufacturer->manufacturer_id }}"
                                       {{ in_array($manufacturer->manufacturer_id, (array) request('manufacturer', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="manufacturer_{{ $manufacturer->manufacturer_id }}">
                                    {{ $manufacturer->name }} ({{ $manufacturer->products_count }})
                                </label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Немає виробників</p>
                @endif
            </div>

            <!-- Special Price -->
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="special_price" value="1" 
                           {{ request('special_price') ? 'checked' : '' }}>
                    <label class="form-check-label">
                        Тільки акції
                    </label>
                </div>
            </div>

            <!-- Sort -->
            <div class="mb-3">
                <label class="form-label">Сортування</label>
                <select class="form-select" name="sort">
                    <option value="date_added" {{ request('sort') == 'date_added' ? 'selected' : '' }}>За датою додавання</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Ціна: за зростанням</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Ціна: за спаданням</option>
                    <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>За популярністю</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>За рейтингом</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>За назвою</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-2"></i>Застосувати фільтри
            </button>
            
            <a href="{{ route('category.show', $category->category_id) }}" class="btn btn-outline-secondary w-100 mt-2">
                <i class="fas fa-times me-2"></i>Скинути
            </a>
        </form>
    </div>
@endsection

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('products.index') }}">Головна</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('categories.index') }}">Категорії</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Category Info -->
    <div class="category-header mb-4">
        <h2>{{ $category->name }}</h2>
        @if($category->description)
            <p class="text-muted">{{ $category->description }}</p>
        @endif
        <div class="d-flex justify-content-between align-items-center">
            <span class="badge bg-primary fs-6">{{ $products->total() }} товарів</span>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row">
        @forelse($products as $product)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card product-card h-100">
                    @if($product->hasDiscount())
                        <div class="discount-badge">
                            -{{ $product->getDiscountPercentage() }}%
                        </div>
                    @endif
                    
                    <div class="position-relative">
                        <img src="{{ $product->thumbnail_url }}" class="card-img-top product-image" alt="{{ $product->name }}">
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ Str::limit($product->name, 60) }}</h5>
                        <p class="card-text text-muted flex-grow-1">
                            {{ Str::limit($product->short_description, 100) }}
                        </p>
                        
                        <div class="price-section mb-3">
                            <div class="d-flex align-items-center">
                                <span class="price-current">{{ $product->formatted_price }}</span>
                                @if($product->formatted_old_price)
                                    <span class="price-old ms-2">{{ $product->formatted_old_price }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="product-stats mb-3">
                            <small class="text-muted">
                                <i class="fas fa-eye me-1"></i>{{ $product->hits }}
                                @if($product->average_rating > 0)
                                    <i class="fas fa-star text-warning ms-2 me-1"></i>{{ number_format($product->average_rating, 1) }}
                                @endif
                            </small>
                        </div>
                        
                        <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Детальніше
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Товари не знайдені</h4>
                    <p class="text-muted">У цій категорії немає товарів</p>
                    <a href="{{ route('categories.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Повернутися до категорій
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endsection

@push('styles')
<style>
    .category-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 20px;
    }
    
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e9ecef;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .product-image {
        height: 200px;
        object-fit: cover;
        background: #f8f9fa;
    }
    
    .price-old {
        text-decoration: line-through;
        color: #6c757d;
    }
    
    .price-current {
        font-weight: bold;
        color: #dc3545;
        font-size: 1.1rem;
    }
    
    .discount-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        padding: 5px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: bold;
        z-index: 10;
    }
</style>
@endpush


