<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Каталог товарів - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .product-card {
            transition: transform 0.3s ease;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .price {
            font-size: 1.2em;
            font-weight: bold;
            color: #28a745;
        }
        .old-price {
            text-decoration: line-through;
            color: #6c757d;
            font-size: 0.9em;
        }
        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8em;
        }
        .filter-sidebar {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .loading {
            display: none;
        }
        .search-box {
            position: relative;
        }
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        .search-result-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }
        .search-result-item:hover {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('products.index') }}">
                <i class="fas fa-shopping-bag me-2"></i>Каталог товарів
            </a>
            
            <!-- Search Box -->
            <div class="search-box flex-grow-1 mx-4">
                <input type="text" class="form-control" id="search-input" placeholder="Пошук товарів...">
                <div class="search-results" id="search-results"></div>
            </div>
            
            <!-- Language Switcher -->
            <div class="navbar-nav me-3">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-globe me-2"></i>
                        @if(app()->getLocale() == 'uk') 🇺🇦 Українська
                        @elseif(app()->getLocale() == 'ru') 🇷🇺 Русский
                        @else 🇬🇧 English
                        @endif
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ app()->getLocale() == 'uk' ? 'active' : '' }}" href="{{ route('language.switch', 'uk') }}">🇺🇦 Українська</a></li>
                        <li><a class="dropdown-item {{ app()->getLocale() == 'ru' ? 'active' : '' }}" href="{{ route('language.switch', 'ru') }}">🇷🇺 Русский</a></li>
                        <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">🇬🇧 English</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="navbar-nav ms-auto">
                <!-- Wishlist -->
                <a class="nav-link text-light me-3" href="{{ route('wishlist.index') }}" title="Список бажань">
                    <i class="fas fa-heart"></i>
                    <span class="badge bg-danger ms-1" id="wishlist-count">0</span>
                </a>
                
                <!-- Cart -->
                <a class="nav-link text-light" href="{{ route('cart.index') }}" title="Кошик">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-danger ms-1" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
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
                        
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-times me-2"></i>Скинути
                        </a>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Товари</h4>
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin me-2"></i>Завантаження...
                    </div>
                </div>

                <!-- Products Grid -->
                <div id="products-grid" class="row">
                    @include('front.products.partials.product-grid', ['products' => $products])
                </div>

                <!-- Pagination -->
                <div id="pagination-container" class="mt-4">
                    @include('front.products.partials.pagination', ['products' => $products])
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filter-form');
            const searchInput = document.getElementById('search-input');
            const searchResults = document.getElementById('search-results');
            const productsGrid = document.getElementById('products-grid');
            const paginationContainer = document.getElementById('pagination-container');
            const loading = document.querySelector('.loading');
            const productsCount = document.getElementById('products-count');
            
            let searchTimeout;

            // Filter form submission
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                loadProducts();
            });

            // Search functionality
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const query = this.value.trim();
                    if (query.length >= 2) {
                        searchProducts(query);
                    } else {
                        searchResults.style.display = 'none';
                    }
                }, 300);
            });

            // Search products function
            function searchProducts(query) {
                fetch(`{{ route('products.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(product => {
                                const item = document.createElement('div');
                                item.className = 'search-result-item';
                                item.innerHTML = `
                                    <div class="d-flex align-items-center">
                                        <img src="${product.image}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover;" class="me-2">
                                        <div>
                                            <div class="fw-bold">${product.name}</div>
                                            <div class="text-success">${product.price}</div>
                                        </div>
                                    </div>
                                `;
                                item.addEventListener('click', () => {
                                    window.location.href = product.url;
                                });
                                searchResults.appendChild(item);
                            });
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Hide search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });

            // Load products function
            function loadProducts() {
                loading.style.display = 'block';
                
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                
                fetch(`{{ route('products.ajax') }}?${params}`)
                    .then(response => response.json())
                    .then(data => {
                        productsGrid.innerHTML = data.html;
                        paginationContainer.innerHTML = data.pagination;
                        productsCount.textContent = data.count;
                        loading.style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        loading.style.display = 'none';
                    });
            }

            // Pagination links
            document.addEventListener('click', function(e) {
                if (e.target.closest('.pagination a')) {
                    e.preventDefault();
                    const url = e.target.closest('.pagination a').href;
                    const urlParams = new URLSearchParams(new URL(url).search);
                    
                    // Update form with pagination parameters
                    filterForm.elements.sort.value = urlParams.get('sort') || '';
                    filterForm.elements.price_min.value = urlParams.get('price_min') || '';
                    filterForm.elements.price_max.value = urlParams.get('price_max') || '';
                    filterForm.elements.manufacturer.value = urlParams.get('manufacturer') || '';
                    filterForm.elements.special_price.checked = urlParams.get('special_price') === '1';
                    
                    loadProducts();
                }
            });
        });
    </script>
</body>
</html>
