@extends('share.layouts.app')

@php
    $componentType = 'product';
@endphp

@section('title', $product->name . ' - ' . config('app.name', 'Laravel'))
@section('description', Str::limit(strip_tags($product->short_description), 160))

@push('styles')
<style>
    .product-image {
        max-height: 500px;
        object-fit: contain;
    }
    .price {
        font-size: 2em;
        font-weight: bold;
        color: #28a745;
    }
    .old-price {
        text-decoration: line-through;
        color: #6c757d;
        font-size: 1.2em;
    }
    .discount-badge {
        background: #dc3545;
        color: white;
        padding: 10px 20px;
        border-radius: 20px;
        font-size: 1.1em;
        font-weight: bold;
    }
    .characteristics {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
    }
    .breadcrumb {
        background: none;
        padding: 0;
    }

    /* Breadcrumbs Styles */
    .breadcrumbs-container {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 10px 0;
    }

    .breadcrumb {
        background: none;
        padding: 0;
        margin: 0;
        font-size: 14px;
    }

    .breadcrumb-item {
        display: inline-flex;
        align-items: center;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        color: #6c757d;
        margin: 0 8px;
    }

    .breadcrumb-item a {
        color: #007bff;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .breadcrumb-item a:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }

    .breadcrumb-item i {
        font-size: 16px;
    }
</style>
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs-container">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Каталог товарів</a>
                </li>
                @if($product->categories->count() > 0)
                    @php
                        $category = $product->categories->first();
                        $breadcrumbs = [];
                        $current = $category;
                        while ($current) {
                            $breadcrumbs[] = $current;
                            $current = $current->parent;
                        }
                        $breadcrumbs = array_reverse($breadcrumbs);
                    @endphp
                    @foreach($breadcrumbs as $breadcrumb)
                        <li class="breadcrumb-item">
                            <a href="{{ route('category.show', $breadcrumb->full_path) }}">{{ $breadcrumb->name }}</a>
                        </li>
                    @endforeach
                @endif
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

    <div class="container mt-4">

        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="{{ $product->image_url }}" 
                             class="img-fluid product-image lazy" 
                             alt="{{ $product->name }}"
                             loading="lazy"
                             width="500"
                             height="400"
                             decoding="async">
                        
                        @if($product->hasDiscount())
                            <div class="mt-3">
                                <span class="discount-badge">
                                    Скидка {{ $product->getDiscountPercentage() }}%
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title">{{ $product->name }}</h1>
                        
                        <!-- Price -->
                        <div class="price mb-3">
                            {{ $product->formatted_price }}
                            @if($product->formatted_old_price)
                                <div class="old-price">{{ $product->formatted_old_price }}</div>
                            @endif
                        </div>

                        <!-- Rating and Reviews -->
                        <div class="mb-3">
                            @if($product->average_rating > 0)
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $product->average_rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @elseif($i - 0.5 <= $product->average_rating)
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            @else
                                                <i class="far fa-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="me-3">{{ number_format($product->average_rating, 1) }}</span>
                                    @if($product->reviews_count > 0)
                                        <small class="text-muted">
                                            ({{ $product->reviews_count }} {{ Str::plural('отзыв', $product->reviews_count) }})
                                        </small>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Short Description -->
                        <div class="mb-4">
                            <h5>Описание</h5>
                            <p>{{ $product->short_description }}</p>
                        </div>

                        <!-- Product Details -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <strong>Артикул:</strong><br>
                                <small class="text-muted">{{ $product->product_ean }}</small>
                            </div>
                            <div class="col-6">
                                <strong>Производитель:</strong><br>
                                @if($product->manufacturer && $product->categories()->first())
                                    <small class="text-muted">
                                        <a href="{{ route('category.show', $product->categories()->first()->full_path) }}?f={{ $product->product_manufacturer_id }}" class="text-decoration-none">
                                            {{ $product->manufacturer->name }}
                                        </a>
                                    </small>
                                @else
                                    <small class="text-muted">{{ $product->manufacturer_name ?? 'Не указан' }}</small>
                                @endif
                            </div>
                        </div>

                        <!-- Additional Product Attributes -->
                        @php
                            $extraFields = $product->product_extra_fields ?? [];
                            $displayedFields = 0;
                            $maxFieldsPerRow = 2;
                            
                        @endphp
                        
                        
                        @if(is_array($extraFields) && count($extraFields) > 0)
                            <div class="mb-4">
                                <h6 class="mb-3">Дополнительные характеристики:</h6>
                                @foreach($extraFields as $index => $field)
                                    @if(isset($field['field_name']) && isset($field['field_value']) && !empty($field['field_value']))
                                        @if($displayedFields % $maxFieldsPerRow == 0)
                                            <div class="row">
                                        @endif
                                        
                                        <div class="col-6">
                                            <strong>{{ $field['field_name'] }}:</strong><br>
                                            <small class="text-muted">{{ $field['field_value'] }}</small>
                                        </div>
                                        
                                        @php $displayedFields++; @endphp
                                        
                                        @if($displayedFields % $maxFieldsPerRow == 0 || $loop->last)
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="mb-4">
                                <small class="text-muted">Дополнительные характеристики не найдены</small>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-lg add-to-cart" type="button" data-product-id="{{ $product->product_id }}">
                                <i class="fas fa-shopping-cart me-2"></i>Добавить в корзину
                            </button>
                            <button class="btn btn-outline-secondary add-to-wishlist" type="button" data-product-id="{{ $product->product_id }}">
                                <i class="fas fa-heart me-2"></i>В избранное
                            </button>
                        </div>

                        <!-- Product Stats -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-muted">Просмотры</small><br>
                                    <strong>{{ $product->hits }}</strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">В наличии</small><br>
                                    <strong class="text-success">
                                        {{ $product->unlimited ? 'Неограничено' : $product->product_quantity }}
                                    </strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Вес</small><br>
                                    <strong>{{ $product->product_weight }} кг</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Modifications Table --}}
        @include('front.products.partials.modifications', ['product' => $product])

        <!-- Product Details Tabs -->
        <div class="row mt-4">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab" 
                                data-bs-target="#description" type="button" role="tab">
                            <i class="fas fa-info-circle me-2"></i>Описание
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="characteristics-tab" data-bs-toggle="tab" 
                                data-bs-target="#characteristics" type="button" role="tab">
                            <i class="fas fa-list me-2"></i>Характеристики
                        </button>
                    </li>
                    @if($product->{'delivery_kit_ru-UA'})
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" 
                                data-bs-target="#delivery" type="button" role="tab">
                            <i class="fas fa-truck me-2"></i>Комплектация
                        </button>
                    </li>
                    @endif
                </ul>
                
                <div class="tab-content" id="productTabsContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                {!! $product->description !!}
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="characteristics" role="tabpanel">
                        <div class="card">
                            <div class="card-body characteristics">
                                {!! $product->characteristics !!}
                            </div>
                        </div>
                    </div>
                    
                    @if($product->{'delivery_kit_ru-UA'})
                    <div class="tab-pane fade" id="delivery" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                {!! nl2br(e($product->{'delivery_kit_ru-UA'})) !!}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
