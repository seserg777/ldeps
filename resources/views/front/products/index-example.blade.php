@extends('share.layouts.shop')

@section('title', 'Каталог товарів')
@section('description', 'Широкий асортимент якісних товарів за доступними цінами')

@section('breadcrumbs')
<x-breadcrumbs :items="[
    ['title' => 'Каталог товарів', 'url' => route('products.index'), 'icon' => 'fas fa-th-large']
]" />
@endsection

@section('sidebar')
<x-sidebar title="Фільтри">
    <!-- Price Filter -->
    <div class="mb-4">
        <h6 class="fw-bold">Ціна</h6>
        <div class="row">
            <div class="col-6">
                <input type="number" class="form-control form-control-sm" placeholder="Від" name="price_from">
            </div>
            <div class="col-6">
                <input type="number" class="form-control form-control-sm" placeholder="До" name="price_to">
            </div>
        </div>
    </div>

    <!-- Categories Filter -->
    <div class="mb-4">
        <h6 class="fw-bold">Категорії</h6>
        <div class="list-group list-group-flush">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->category_id) }}" 
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    {{ $category->name }}
                    <span class="badge bg-primary rounded-pill">{{ $category->products_count ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Manufacturers Filter -->
    <div class="mb-4">
        <h6 class="fw-bold">Виробники</h6>
        <div class="form-check">
            @foreach($manufacturers as $manufacturer)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           name="manufacturer[]" value="{{ $manufacturer->manufacturer_id }}"
                           id="manufacturer_{{ $manufacturer->manufacturer_id }}">
                    <label class="form-check-label" for="manufacturer_{{ $manufacturer->manufacturer_id }}">
                        {{ $manufacturer->name }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Clear Filters -->
    <button type="button" class="btn btn-outline-secondary btn-sm w-100">
        <i class="fas fa-times me-1"></i>Очистити фільтри
    </button>
</x-sidebar>
@endsection

@section('page-content')
<!-- Page Header -->
<x-page-header 
    title="Каталог товарів" 
    subtitle="Знайдіть ідеальний товар для себе"
    icon="fas fa-th-large"
>
    <x-slot name="actions">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-light">
                <i class="fas fa-th me-1"></i>Сітка
            </button>
            <button type="button" class="btn btn-outline-light">
                <i class="fas fa-list me-1"></i>Список
            </button>
        </div>
    </x-slot>
</x-page-header>

<!-- Products Grid -->
<div class="row">
    @forelse($products as $product)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="position-relative">
                    <img src="{{ $product->thumbnail_url }}" 
                         class="card-img-top" 
                         alt="{{ $product->product_name }}"
                         style="height: 200px; object-fit: cover;">
                    
                    <!-- Badges -->
                    @if($product->product_publish)
                        <span class="badge bg-success position-absolute top-0 start-0 m-2">В наявності</span>
                    @endif
                    
                    <!-- Quick Actions -->
                    <div class="position-absolute top-0 end-0 m-2">
                        <button class="btn btn-light btn-sm rounded-circle" title="Додати до бажань">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $product->product_name }}</h5>
                    <p class="card-text text-muted small flex-grow-1">
                        {{ Str::limit($product->product_short_description, 100) }}
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="h5 text-primary mb-0">
                            {{ number_format($product->product_price, 0, ',', ' ') }} ₴
                        </div>
                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-cart-plus me-1"></i>В кошик
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Товари не знайдено</h4>
                <p class="text-muted">Спробуйте змінити параметри пошуку</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($products->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $products->links() }}
    </div>
@endif
@endsection
