@push('styles')
<style>
    .product-extra-fields {
        border-top: 1px solid #f0f0f0;
        padding-top: 8px;
        margin-top: 8px;
    }
    
    .extra-field-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 2px 0;
    }
    
    .extra-field-item span:first-child {
        flex: 0 0 auto;
        margin-right: 8px;
    }
    
    .extra-field-item span:last-child {
        flex: 1;
        text-align: right;
        font-size: 0.8rem;
    }
    
    @media (max-width: 576px) {
        .extra-field-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .extra-field-item span:last-child {
            text-align: left;
            margin-top: 2px;
        }
    }
</style>
@endpush

@if($products->count() > 0)
    @foreach($products as $product)
        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card product-card h-100 shadow-sm border-0">
                <!-- Product Image Container -->
                <div class="position-relative overflow-hidden">
                    <img src="{{ $product->thumbnail_url }}" 
                         class="card-img-top product-image lazy" 
                         alt="{{ $product->name }}"
                         loading="lazy"
                         width="300"
                         height="200"
                         decoding="async">
                    
                    <!-- Product Badges -->
                    <div class="product-badges">
                        @if($product->hits > 100)
                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">
                                <i class="fas fa-fire me-1"></i>Хіт
                            </span>
                        @endif
                        @if($product->product_price < 1000)
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                <i class="fas fa-percent me-1"></i>Акція
                            </span>
                        @endif
                    </div>
                    
                    <!-- Quick Actions Overlay -->
                    <div class="product-overlay">
                        <div class="overlay-actions">
                            <a href="{{ route('products.show-by-path', $product->full_path) }}" 
                               class="btn btn-light btn-sm rounded-circle me-2" 
                               title="Швидкий перегляд">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-danger btn-sm rounded-circle me-2 add-to-wishlist" 
                                    data-product-id="{{ $product->product_id }}"
                                    title="Додати до бажань">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="btn btn-primary btn-sm rounded-circle add-to-cart" 
                                    data-product-id="{{ $product->product_id }}"
                                    title="Додати до кошика">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="card-body d-flex flex-column p-3">
                    <!-- Product Title -->
                    <h6 class="card-title mb-2 fw-bold text-dark">
                            <a href="{{ route('products.show-by-path', $product->full_path) }}" 
                               class="text-decoration-none text-dark stretched-link">
                                {{ Str::limit($product->name, 60) }}
                            </a>
                    </h6>
                    
                    <!-- Product Description -->
                    <p class="card-text text-muted small mb-2 flex-grow-1">
                        {{ Str::limit('Оптичний кабель високої якості для професійного використання', 80) }}
                    </p>
                    
                    <!-- Product Extra Fields -->
                    @php
                        try {
                            $extraFieldsData = $product->product_extra_fields ?? [];
                            // Ensure it's always an array and not a string
                            if (!is_array($extraFieldsData) || is_string($extraFieldsData)) {
                                $extraFieldsData = [];
                            }
                            // Additional safety check
                            if (is_object($extraFieldsData) && method_exists($extraFieldsData, 'toArray')) {
                                $extraFieldsData = $extraFieldsData->toArray();
                            }
                        } catch (\Exception $e) {
                            $extraFieldsData = [];
                            \Log::error("Error getting product extra fields for product {$product->product_id}: " . $e->getMessage());
                        }
                    @endphp
                    
                    
                    @if(is_array($extraFieldsData) && !empty($extraFieldsData) && count($extraFieldsData) > 0)
                        <div class="product-extra-fields mb-2">
                            @foreach($extraFieldsData as $extraField)
                                @if(is_array($extraField) && isset($extraField['field_name']) && isset($extraField['field_value']))
                                    <div class="extra-field-item mb-1">
                                        <span class="fw-bold text-dark small">{{ $extraField['field_name'] }}:</span>
                                        <span class="text-muted small">{{ $extraField['field_value'] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Rating -->
                    <div class="product-rating mb-2">
                        <div class="stars d-inline-block">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= 4)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                        </div>
                        <small class="text-muted ms-1">({{ rand(10, 50) }})</small>
                    </div>
                    
                    <!-- Price -->
                    <div class="product-price mb-3">
                        <div class="d-flex align-items-center">
                            <span class="h5 mb-0 text-primary fw-bold">{{ $product->formatted_price }}</span>
                            @if($product->product_price > 2000)
                                <small class="text-muted text-decoration-line-through ms-2">
                                    {{ number_format($product->product_price * 1.2, 2) }} ₴
                                </small>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Product Actions -->
                    <div class="product-actions">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-eye me-1"></i>{{ $product->hits ?? 0 }} переглядів
                            </small>
                            <div class="btn-group" role="group">
                                <a href="{{ route('products.show-by-path', $product->full_path) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-info-circle me-1"></i>Деталі
                                </a>
                                <button class="btn btn-outline-danger btn-sm add-to-wishlist" 
                                        data-product-id="{{ $product->product_id }}">
                                    <i class="fas fa-heart me-1"></i>Бажання
                                </button>
                                <button class="btn btn-primary btn-sm add-to-cart" 
                                        data-product-id="{{ $product->product_id }}">
                                    <i class="fas fa-cart-plus me-1"></i>В кошик
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="col-12">
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-search fa-4x text-muted mb-4"></i>
                <h3 class="text-muted mb-3">Товари не знайдені</h3>
                <p class="text-muted mb-4">Спробуйте змінити параметри пошуку або фільтри</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-refresh me-2"></i>Скинути фільтри
                </a>
            </div>
        </div>
    </div>
@endif
