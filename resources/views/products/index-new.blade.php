@extends('layouts.app')

@section('title', 'Каталог товарів')

@section('products-count', $products->total())

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">Каталог товарів</a>
    </li>
    @if(isset($category))
        @php
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
                @if($loop->last)
                    {{ $breadcrumb->name }}
                @else
                    <a href="{{ route('category.show', $breadcrumb->full_path) }}">{{ $breadcrumb->name }}</a>
                @endif
            </li>
        @endforeach
    @endif
@endsection

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
                                       {{ (isset($filterParam) && !str_starts_with($filterParam, 'l') && in_array($manufacturer->manufacturer_id, array_map('intval', explode(',', $filterParam)))) ? 'checked' : '' }}>
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
            
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                <i class="fas fa-times me-2"></i>Скинути
            </a>
        </form>
    </div>
@endsection

@section('content')
    <!-- Child Categories Section -->
    @include('products.partials.child-categories')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Товари</h4>
            @if(isset($category))
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Показано товари з категорії "{{ $category->name }}" та всіх підкатегорій
                    @if(isset($filteredExtraFieldValue) && $filteredExtraFieldValue)
                        <br>
                        <span class="badge bg-success mt-1">
                            <i class="fas fa-filter me-1"></i>
                            {{ $filteredExtraFieldValue->extraField->name }}: {{ $filteredExtraFieldValue->name }}
                        </span>
                    @endif
                    @if(isset($filteredManufacturer) && $filteredManufacturer)
                        <br>
                        <span class="badge bg-primary mt-1">
                            <i class="fas fa-industry me-1"></i>
                            Виробник: {{ $filteredManufacturer->name }}
                        </span>
                    @endif
                </p>
            @endif
        </div>
        <div class="loading">
            <i class="fas fa-spinner fa-spin me-2"></i>Завантаження...
        </div>
    </div>

    <!-- Products Grid -->
    <div id="products-grid" class="row">
        @include('products.partials.product-grid', ['products' => $products])
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        @include('products.partials.pagination', ['products' => $products])
    </div>
@endsection

@push('styles')
<style>
    /* Product Card Styles */
    .product-card {
        transition: all 0.3s ease;
        height: 100%;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
    
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    
    /* Product Image */
    .product-image {
        height: 220px;
        object-fit: cover;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-image {
        transform: scale(1.05);
    }
    
    /* Product Overlay */
    .product-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .product-card:hover .product-overlay {
        opacity: 1;
    }
    
    .overlay-actions {
        display: flex;
        gap: 10px;
    }
    
    /* Product Badges */
    .product-badges .badge {
        font-size: 0.75rem;
        padding: 6px 10px;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    /* Product Rating */
    .product-rating .stars {
        font-size: 0.9rem;
    }
    
    .product-rating .stars i {
        margin-right: 2px;
    }
    
    /* Product Price */
    .product-price .h5 {
        font-size: 1.4rem;
        font-weight: 700;
    }
    
    /* Product Actions */
    .product-actions .btn-group .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .product-actions .btn:hover {
        transform: translateY(-1px);
    }
    
    /* Empty State */
    .empty-state {
        padding: 60px 20px;
    }
    
    .empty-state i {
        opacity: 0.3;
    }
    
    /* Loading Animation */
    .loading {
        display: none;
    }
    
    .loading.show {
        display: block;
    }
    
    /* Responsive Grid */
    @media (max-width: 768px) {
        .product-image {
            height: 180px;
        }
        
        .product-card {
            margin-bottom: 20px;
        }
    }
    
    /* Card Hover Effects */
    .product-card .card-body {
        position: relative;
        z-index: 2;
    }
    
    .product-card:hover .card-body {
        background: #fff;
    }
    
    /* Button Styles */
    .btn-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(0,123,255,0.3);
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
        box-shadow: 0 6px 20px rgba(0,123,255,0.4);
    }
    
    .btn-outline-primary {
        border-color: #007bff;
        color: #007bff;
    }
    
    .btn-outline-primary:hover {
        background: #007bff;
        border-color: #007bff;
    }
    
    /* Animation for product cards */
    .product-card {
        animation: fadeInUp 0.6s ease forwards;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Staggered animation for multiple cards */
    .product-card:nth-child(1) { animation-delay: 0.1s; }
    .product-card:nth-child(2) { animation-delay: 0.2s; }
    .product-card:nth-child(3) { animation-delay: 0.3s; }
    .product-card:nth-child(4) { animation-delay: 0.4s; }
    .product-card:nth-child(5) { animation-delay: 0.5s; }
    .product-card:nth-child(6) { animation-delay: 0.6s; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Filter form submission
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            updateUrlFromFilters();
        });

        // Manufacturer checkbox change handler
        $('input[name="manufacturer[]"]').on('change', function() {
            updateUrlFromManufacturerFilter();
        });

        // Price range input handlers
        $('input[name="price_min"], input[name="price_max"]').on('change', function() {
            updateUrlFromFilters();
        });

        // Special price checkbox handler
        $('input[name="special_price"]').on('change', function() {
            updateUrlFromFilters();
        });

        // Sort select handler
        $('select[name="sort"]').on('change', function() {
            updateUrlFromFilters();
        });

        // Function to update URL based on manufacturer filter
        function updateUrlFromManufacturerFilter() {
            const checkedManufacturers = $('input[name="manufacturer[]"]:checked');
            const url = new URL(window.location);
            
            // Always remove manufacturer[] parameters and f parameter
            url.searchParams.delete('manufacturer[]');
            url.searchParams.delete('f');
            
            if (checkedManufacturers.length > 0) {
                // For multiple manufacturers, use comma-separated values: ?f=4,6,8
                const manufacturerIds = checkedManufacturers.map(function() {
                    return $(this).val();
                }).get();
                url.searchParams.set('f', manufacturerIds.join(','));
            }
            // If no manufacturers selected, f parameter is already deleted
            
            window.location.href = url.toString();
        }

        // Function to update URL from all filters
        function updateUrlFromFilters() {
            const formData = $('#filter-form').serialize();
            const url = new URL(window.location);
            
            // Remove existing filter parameters
            url.searchParams.delete('manufacturer[]');
            url.searchParams.delete('f');
            url.searchParams.delete('price_min');
            url.searchParams.delete('price_max');
            url.searchParams.delete('special_price');
            url.searchParams.delete('sort');
            
            // Add form data to URL
            const params = new URLSearchParams(formData);
            params.forEach((value, key) => {
                if (value) {
                    url.searchParams.set(key, value);
                }
            });
            
            window.location.href = url.toString();
        }

        // AJAX product loading
        function loadProducts() {
            $('.loading').show();
            
            $.ajax({
                url: '{{ route("products.api") }}',
                method: 'GET',
                data: $('#filter-form').serialize(),
                success: function(response) {
                    $('#products-grid').html(response.html);
                    $('#products-count').text(response.count);
                    $('.loading').hide();
                },
                error: function() {
                    $('.loading').hide();
                    alert('Помилка завантаження товарів');
                }
            });
        }

        // Auto-submit form on change
        $('#filter-form select, #filter-form input[type="checkbox"]').on('change', function() {
            loadProducts();
        });
    });
    
    // Add to cart function
    function addToCart(productId) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Додаємо...';
        button.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // Show success message
            button.innerHTML = '<i class="fas fa-check me-1"></i>Додано!';
            button.classList.remove('btn-primary');
            button.classList.add('btn-success');
            
            // Show toast notification
            showToast('Товар додано до кошика!', 'success');
            
            // Reset button after 2 seconds
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('btn-success');
                button.classList.add('btn-primary');
                button.disabled = false;
            }, 2000);
        }, 1000);
    }
    
    // Load more products function
    function loadMoreProducts() {
        const button = $('.show-more-btn');
        const originalText = button.html();
        
        // Show loading state
        button.html('<i class="fas fa-spinner fa-spin me-2"></i>Завантаження...');
        button.prop('disabled', true);
        
        // Get current page and load next page
        const currentUrl = new URL(window.location);
        const currentPage = parseInt(currentUrl.searchParams.get('page')) || 1;
        const nextPage = currentPage + 1;
        
        currentUrl.searchParams.set('page', nextPage);
        
        // Use the AJAX endpoint for products
        const ajaxUrl = '{{ route("products.api") }}';
        const ajaxParams = new URLSearchParams(currentUrl.search);
        
        $.ajax({
            url: ajaxUrl,
            method: 'GET',
            data: ajaxParams.toString(),
            success: function(response) {
                console.log('AJAX Response:', response); // Debug log
                
                if (response.html && response.html.trim() !== '') {
                    // Parse the HTML response to get products
                    const $html = $(response.html);
                    console.log('HTML content length:', response.html.length); // Debug log
                    console.log('HTML content preview:', response.html.substring(0, 500)); // Debug log
                    
                    // Try different selectors to find products
                    let newProducts = $html.find('.col-lg-3, .col-md-6, .col-sm-6');
                    console.log('Found products (col selectors):', newProducts.length); // Debug log
                    
                    // If no products found, try finding product cards and get their parent containers
                    if (newProducts.length === 0) {
                        // Find all product cards and get their parent div containers
                        const productCards = $html.find('.product-card');
                        newProducts = $();
                        productCards.each(function() {
                            const parentDiv = $(this).closest('div[class*="col-"]');
                            if (parentDiv.length > 0) {
                                newProducts = newProducts.add(parentDiv);
                            }
                        });
                        console.log('Found products (product-card):', newProducts.length); // Debug log
                    }
                    
                    // If still no products, try finding any div with col classes
                    if (newProducts.length === 0) {
                        newProducts = $html.find('div[class*="col-"]');
                        console.log('Found products (any col):', newProducts.length); // Debug log
                    }
                    
                    // If still no products, try a different approach - look for the exact structure
                    if (newProducts.length === 0) {
                        // Look for the exact structure from product-grid.blade.php
                        newProducts = $html.find('div.col-lg-3, div.col-md-6, div.col-sm-6');
                        console.log('Found products (exact structure):', newProducts.length); // Debug log
                    }
                    
                    if (newProducts.length > 0) {
                        console.log('About to append products:', newProducts.length); // Debug log
                        console.log('Current grid has products:', $('#products-grid .col-lg-3, #products-grid .col-md-6, #products-grid .col-sm-6').length); // Debug log
                        
                        // Append new products to existing grid
                        $('#products-grid .row').append(newProducts);
                        
                        console.log('Products appended successfully'); // Debug log
                        console.log('Grid now has products:', $('#products-grid .col-lg-3, #products-grid .col-md-6, #products-grid .col-sm-6').length); // Debug log
                        
                        // Additional check - count all product cards
                        console.log('Total product cards in grid:', $('#products-grid .product-card').length); // Debug log
                        console.log('Total col divs in grid:', $('#products-grid div[class*="col-"]').length); // Debug log
                        
                        // Update pagination section
                        if (response.pagination) {
                            $('.pagination-container').html(response.pagination);
                        }
                        
                        // Show success message
                        button.html('<i class="fas fa-check me-2"></i>Завантажено!');
                        button.removeClass('btn-outline-success').addClass('btn-success');
                        
                        // Reset button after 2 seconds
                        setTimeout(() => {
                            button.html(originalText);
                            button.removeClass('btn-success').addClass('btn-outline-success');
                            button.prop('disabled', false);
                        }, 2000);
                    } else {
                        console.log('No products found in response'); // Debug log
                        console.log('Response structure:', {
                            hasHtml: !!response.html,
                            htmlLength: response.html ? response.html.length : 0,
                            hasPagination: !!response.pagination,
                            count: response.count
                        });
                        
                        // Check if this is the last page
                        if (response.count && $('#products-grid .col-lg-3, #products-grid .col-md-6, #products-grid .col-sm-6').length >= response.count) {
                            button.html('<i class="fas fa-check-circle me-2"></i>Всі товари завантажено');
                            button.removeClass('btn-outline-success').addClass('btn-info');
                            button.prop('disabled', true);
                        } else {
                            button.html('<i class="fas fa-exclamation-triangle me-2"></i>Немає товарів');
                            button.removeClass('btn-outline-success').addClass('btn-warning');
                            
                            setTimeout(() => {
                                button.html(originalText);
                                button.removeClass('btn-warning').addClass('btn-outline-success');
                                button.prop('disabled', false);
                            }, 3000);
                        }
                    }
                } else {
                    console.log('Empty response received'); // Debug log
                    button.html('<i class="fas fa-exclamation-triangle me-2"></i>Порожня відповідь');
                    button.removeClass('btn-outline-success').addClass('btn-warning');
                    
                    setTimeout(() => {
                        button.html(originalText);
                        button.removeClass('btn-warning').addClass('btn-outline-success');
                        button.prop('disabled', false);
                    }, 3000);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', status, error); // Debug log
                button.html('<i class="fas fa-exclamation-triangle me-2"></i>Помилка');
                button.removeClass('btn-outline-success').addClass('btn-danger');
                
                setTimeout(() => {
                    button.html(originalText);
                    button.removeClass('btn-danger').addClass('btn-outline-success');
                    button.prop('disabled', false);
                }, 3000);
            }
        });
    }
    
    // Toast notification function
    function showToast(message, type = 'info') {
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'info'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        // Create toast container if it doesn't exist
        if (!$('#toast-container').length) {
            $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
        }
        
        $('#toast-container').append(toast);
        
        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        // Remove toast element after it's hidden
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
</script>
@endpush


