{{-- Product Detail Content (used in page.blade.php) --}}

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
</style>
@endpush

{{-- Breadcrumbs --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">
                <i class="fas fa-home"></i> Головна
            </a>
        </li>
        @if($product->categories->count() > 0)
            @php
                $category = $product->categories->first();
                $breadcrumbs = [];
                $current = $category;
                while ($current) {
                    $breadcrumbs[] = $current;
                    $current = $current->parent ?? null;
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

<div class="row">
    {{-- Product Images --}}
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
                            Знижка {{ $product->getDiscountPercentage() }}%
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Product Info --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                {{-- Price --}}
                <div class="price mb-3">
                    {{ $product->formatted_price }}
                    @if($product->formatted_old_price)
                        <div class="old-price">{{ $product->formatted_old_price }}</div>
                    @endif
                </div>

                {{-- Rating --}}
                @if($product->average_rating > 0)
                    <div class="mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $product->average_rating)
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-muted"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="me-3">{{ number_format($product->average_rating, 1) }}</span>
                            @if($product->reviews_count > 0)
                                <small class="text-muted">({{ $product->reviews_count }} відгуків)</small>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Short Description --}}
                @if($product->short_description)
                    <div class="mb-4">
                        <h5>Опис</h5>
                        <p>{{ $product->short_description }}</p>
                    </div>
                @endif

                {{-- Product Details --}}
                <div class="row mb-4">
                    <div class="col-6">
                        <strong>Артикул:</strong><br>
                        <small class="text-muted">{{ $product->product_ean ?: '-' }}</small>
                    </div>
                    <div class="col-6">
                        <strong>Виробник:</strong><br>
                        @if($product->manufacturer)
                            <small class="text-muted">{{ $product->manufacturer->name }}</small>
                        @else
                            <small class="text-muted">-</small>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg add-to-cart" type="button" data-product-id="{{ $product->product_id }}">
                        <i class="fas fa-shopping-cart me-2"></i>Додати в кошик
                    </button>
                    <button class="btn btn-outline-secondary add-to-wishlist" type="button" data-product-id="{{ $product->product_id }}">
                        <i class="fas fa-heart me-2"></i>В обране
                    </button>
                </div>

                {{-- Product Stats --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted">Переглядів</small><br>
                            <strong>{{ $product->hits }}</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">В наявності</small><br>
                            <strong class="text-success">
                                {{ $product->unlimited ? 'Необмежено' : $product->product_quantity }}
                            </strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Вага</small><br>
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

{{-- Product Details Tabs --}}
<div class="row mt-4">
    <div class="col-12">
        <ul class="nav nav-tabs" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" 
                        data-bs-target="#description" type="button" role="tab">
                    <i class="fas fa-info-circle me-2"></i>Опис
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="characteristics-tab" data-bs-toggle="tab" 
                        data-bs-target="#characteristics" type="button" role="tab">
                    <i class="fas fa-list me-2"></i>Характеристики
                </button>
            </li>
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
                        {{-- Display product characteristics --}}
                        @if($product->productCharacteristics->count() > 0)
                            <table class="table table-bordered">
                                @foreach($product->productCharacteristics as $char)
                                    <tr>
                                        <th class="w-50">{{ $char->extraField->name ?? '-' }}</th>
                                        <td>{{ $char->extraFieldValue->name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            <p class="text-muted">Немає характеристик</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @include('front.products.partials.also_bought_products', ['product' => $product])
    </div>
</div>