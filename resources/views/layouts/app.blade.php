<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Performance meta tags -->
    <meta name="theme-color" content="#0d6efd">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- DNS prefetch for external resources -->
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//code.jquery.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    
    <title>@yield('title', 'Каталог товарів') - {{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="@yield('description', 'Каталог товарів')">
    
    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"></noscript>
    
    <!-- Font Awesome - load only needed icons -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></noscript>
    
    <!-- Google Fonts - Rubik with display=swap for better performance -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&display=swap"></noscript>
    
    @stack('styles')
    
    <!-- Critical CSS inline -->
    <style>
        /* Critical above-the-fold styles with Rubik font */
        body { 
            margin: 0; 
            font-family: 'Rubik', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-weight: 400;
            line-height: 1.6;
        }
        .navbar { 
            background-color: #0d6efd; 
            min-height: 56px; 
            font-family: 'Rubik', sans-serif;
        }
        .navbar-brand { 
            color: white; 
            font-weight: 600; 
            font-family: 'Rubik', sans-serif;
        }
        .nav-link { 
            color: white; 
            font-family: 'Rubik', sans-serif;
            font-weight: 400;
        }
        .btn-outline-light { 
            border-color: white; 
            color: white; 
            font-family: 'Rubik', sans-serif;
            font-weight: 500;
        }
        .search-box { position: relative; }
        .search-results { 
            position: absolute; 
            top: 100%; 
            left: 0; 
            right: 0; 
            background: white; 
            border: 1px solid #ddd; 
            z-index: 1000; 
            display: none; 
            font-family: 'Rubik', sans-serif;
        }
    </style>
    
    <style>
        /* Global font family */
        * {
            font-family: 'Rubik', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
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
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            max-height: 500px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }
        
        .search-results .search-section {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .search-results .search-section:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .search-results .search-result-item:hover {
            background-color: #f8f9fa !important;
        }
        
        /* Typography with Rubik font weights */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Rubik', sans-serif;
            font-weight: 600;
        }
        
        .card-title {
            font-family: 'Rubik', sans-serif;
            font-weight: 500;
        }
        
        .btn {
            font-family: 'Rubik', sans-serif;
            font-weight: 500;
        }
        
        .form-control {
            font-family: 'Rubik', sans-serif;
            font-weight: 400;
        }
        
        .breadcrumb {
            font-family: 'Rubik', sans-serif;
            font-weight: 400;
        }
        
        .badge {
            font-family: 'Rubik', sans-serif;
            font-weight: 500;
        }
        
        /* Navigation menu styles */
        .navbar-light .navbar-nav .nav-link {
            color: #333 !important;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        
        .navbar-light .navbar-nav .nav-link:hover {
            color: #0d6efd !important;
        }
        
        .navbar-light .navbar-nav .nav-link i {
            color: #6c757d;
            transition: color 0.2s ease;
        }
        
        .navbar-light .navbar-nav .nav-link:hover i {
            color: #0d6efd;
        }
        
        .navbar-light {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
        }
        
        /* Ensure text visibility in navigation */
        .navbar-light .navbar-nav .nav-link {
            text-decoration: none !important;
        }
        
        .navbar-light .navbar-nav .nav-link:focus {
            color: #0d6efd !important;
            outline: none;
        }
        
        /* Dropdown styles */
        .navbar-light .dropdown-toggle::after {
            color: #6c757d;
        }
        
        .navbar-light .dropdown-toggle:hover::after {
            color: #0d6efd;
        }
        
        /* Critical CSS for above-the-fold content */
        .navbar {
            min-height: 56px;
        }
        
        /* Lazy loading styles */
        .lazy {
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .lazy.loaded {
            opacity: 1;
        }
        
        .search-result-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .search-result-item:hover {
            background-color: #f8f9fa;
        }
        
        .search-result-item:last-child {
            border-bottom: none;
        }
        
        .filter-sidebar {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .filter-sidebar h5 {
            color: #495057;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
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
        }
        
        .loading {
            display: none;
        }
        
        .footer {
            background: #343a40;
            color: white;
            margin-top: 50px;
        }
        
        .category-dropdown .dropdown-menu {
            min-width: 900px;
            max-height: 600px;
            overflow: hidden;
            padding: 0;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        /* Two-column category menu */
        .category-menu-container {
            display: flex;
            min-height: 500px;
        }
        
        .category-main-column {
            flex: 1;
            background: white;
            border-right: 1px solid #e9ecef;
        }
        
        .category-sub-column {
            flex: 1;
            background: #f8f9fa;
            padding: 20px;
        }
        
        .category-main-item {
            display: block;
            padding: 12px 20px;
            color: #495057;
            text-decoration: none;
            border-bottom: 1px solid #f8f9fa;
            transition: all 0.2s ease;
        }
        
        .category-main-item:hover {
            background: #e9ecef;
            color: #007bff;
        }
        
        .category-main-item.active {
            background: #007bff;
            color: white;
        }
        
        .category-sub-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .category-sub-item {
            display: block;
            padding: 6px 0;
            color: #6c757d;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s ease;
            font-weight: 500;
        }
        
        .category-sub-item:hover {
            color: #007bff;
        }

        .category-sub-sub-item {
            display: block;
            padding: 4px 0 4px 16px;
            color: #8a8a8a;
            text-decoration: none;
            font-size: 12px;
            transition: color 0.2s ease;
            border-left: 2px solid #e9ecef;
            margin-left: 8px;
        }

        .category-sub-sub-item:hover {
            color: #007bff;
            border-left-color: #007bff;
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
        
        .category-sub-section {
            margin-bottom: 20px;
        }
        
        .main-content {
            min-height: calc(100vh - 200px);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('products.index') }}">
                <i class="fas fa-shopping-bag me-2"></i>Каталог товарів
            </a>
            
            <!-- Search Box -->
            <div class="search-box flex-grow-1 mx-4 position-relative">
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
                <!-- Authentication -->
                @if(Auth::guard('custom')->check())
                    <!-- User is logged in -->
                    <div class="nav-item dropdown me-3">
                        <a class="nav-link text-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" title="Профіль користувача">
                            <i class="fas fa-user me-1"></i>{{ Auth::guard('custom')->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}">
                                <i class="fas fa-user me-2"></i>Профіль
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Панель управління
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('auth.logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Вийти
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <!-- User is not logged in -->
                    <a class="nav-link text-light me-3" href="{{ route('auth.login') }}" title="Увійти">
                        <i class="fas fa-sign-in-alt me-1"></i>Увійти
                    </a>
                @endif
                
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

    <!-- Main Navigation Menu -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container">
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('products.index') }}">
                    <i class="fas fa-home me-1"></i>Головна
                </a>
                
                <!-- Categories Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-th-large me-1"></i>Категорії
                    </a>
                    <div class="dropdown-menu category-dropdown">
                        <div class="category-menu-container">
                            <!-- Main Categories Column -->
                            <div class="category-main-column">
                                @if(isset($categories) && $categories->count() > 0)
                                    @foreach($categories as $category)
                                        <a class="category-main-item" href="{{ route('category.show', $category->full_path) }}" 
                                           data-category="{{ $category->category_id }}"
                                           title="Category ID: {{ $category->category_id }}">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                            
                            <!-- Subcategories Column -->
                            <div class="category-sub-column">
                                <div id="category-subcontent">
                                    <!-- Default content when no category is hovered -->
                                    <div class="category-sub-section">
                                        <div class="category-sub-title">Оберіть категорію</div>
                                        <div class="category-sub-item">Наведіть курсор на категорію зліва, щоб побачити підкатегорії</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sale Banners Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-percent me-1"></i>Акції
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('sale-banners.index') }}">
                            <i class="fas fa-list me-2"></i>Всі акції
                        </a></li>
                        @php
                            $saleTags = \App\Models\ExussalebannerTag::published()->ordered()->get();
                        @endphp
                        @if($saleTags->count() > 0)
                            <li><hr class="dropdown-divider"></li>
                            @foreach($saleTags as $tag)
                                <li><a class="dropdown-item" href="{{ route('sale-banner.tag', $tag->alias) }}">
                                    <i class="fas fa-tag me-2"></i>{{ $tag->name }}
                                </a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                
                <a class="nav-link" href="{{ route('products.index', ['sort' => 'popularity']) }}">
                    <i class="fas fa-fire me-1"></i>Популярні
                </a>
                
                <a class="nav-link" href="{{ route('products.index', ['sort' => 'rating']) }}">
                    <i class="fas fa-star me-1"></i>Топ рейтинг
                </a>
            </div>
        </div>
    </nav>

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
                    @yield('breadcrumbs')
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container main-content">
        <div class="row">
            @if(!View::hasSection('sidebar') || trim(View::yieldContent('sidebar')) !== '')
                <!-- Left Sidebar -->
                <div class="col-lg-3">
                    @hasSection('sidebar')
                        @yield('sidebar')
                    @else
                        <!-- Default sidebar content -->
                        <div class="filter-sidebar">
                            <h5><i class="fas fa-filter me-2"></i>Фільтри</h5>
                            <p class="text-muted">Модулі фільтрації будуть тут</p>
                        </div>
                    @endif
                </div>

                <!-- Main Content Area -->
                <div class="col-lg-9">
                    @yield('content')
                </div>
            @else
                <!-- Full width content when sidebar is empty -->
                <div class="col-12">
                    @yield('content')
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-4">
                    <h5>Каталог товарів</h5>
                    <p class="text-muted">Інтернет-магазин з широким асортиментом товарів</p>
                    <div class="mt-3">
                        <a href="{{ route('sale-banners.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-percent me-1"></i>Акції та розпродажі
                        </a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5>Контакти</h5>
                    <p class="text-muted">
                        <i class="fas fa-envelope me-2"></i>info@example.com<br>
                        <i class="fas fa-phone me-2"></i>+380 (XX) XXX-XX-XX
                    </p>
                </div>
                <div class="col-md-4">
                    <h5>Соціальні мережі</h5>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-telegram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted mb-0">&copy; {{ date('Y') }} Каталог товарів. Всі права захищені.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS - defer loading -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    
    <!-- jQuery - load without defer for compatibility -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
    
    <script>
        // Lazy loading functionality
        function initLazyLoading() {
            const lazyImages = document.querySelectorAll('img.lazy');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    });
                });
                
                lazyImages.forEach(img => imageObserver.observe(img));
            } else {
                // Fallback for older browsers
                lazyImages.forEach(img => {
                    img.classList.add('loaded');
                });
            }
        }
        
        // Initialize lazy loading when DOM is ready
        document.addEventListener('DOMContentLoaded', initLazyLoading);
        
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW registered: ', registration);
                    })
                    .catch(function(registrationError) {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
        
        // Initialize all functionality
        function initializeApp() {
            // Simple jQuery check
            if (typeof $ === 'undefined') {
                console.error('jQuery not loaded!');
                return;
            }
            
            console.log('jQuery loaded successfully');
            
            // Search functionality
            $(document).ready(function() {
                console.log('Document ready fired');
                let searchTimeout;
                
                $('#search-input').on('input', function() {
                    clearTimeout(searchTimeout);
                    const query = $(this).val();
                    
                    if (query.length < 2) {
                        $('#search-results').hide();
                        return;
                    }
                    
                    // Debounce search requests
                    searchTimeout = setTimeout(function() {
                        // Cancel previous request if still pending
                        if (window.searchXHR) {
                            window.searchXHR.abort();
                        }
                        
                        window.searchXHR = $.ajax({
                            url: '{{ route("search.api") }}',
                            method: 'GET',
                            data: { q: query },
                            timeout: 5000, // 5 second timeout
                            success: function(data) {
                                displaySearchResults(data);
                            },
                            error: function(xhr, status, error) {
                                if (status !== 'abort') {
                                    console.log('Search error:', error);
                                }
                            }
                        });
                    }, 300);
                });
            
                function displaySearchResults(data) {
                    const container = $('#search-results');
                    container.empty();
                    
                    let hasResults = false;
                    let resultsHtml = '';
                    
                    // Products section
                    if (data.products && data.products.length > 0) {
                        hasResults = true;
                        resultsHtml += '<div class="search-section mb-3"><h6 class="fw-bold mb-2 text-dark"><i class="fas fa-box me-2"></i>Товари</h6>';
                        
                        data.products.forEach(function(product) {
                            let additionalInfo = '';
                            if (product.ean) {
                                additionalInfo += `<small class="text-muted d-block">Артикул: ${product.ean}</small>`;
                            }
                            if (product.manufacturer_code) {
                                additionalInfo += `<small class="text-muted d-block">Код: ${product.manufacturer_code}</small>`;
                            }
                            
                            resultsHtml += `
                                <div class="search-result-item d-flex align-items-center p-2 border rounded mb-2 bg-white" style="cursor: pointer;">
                                    <img src="${product.image}" class="me-3" style="width: 40px; height: 40px; object-fit: cover;" alt="${product.name}">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark">${product.name}</div>
                                        <div class="text-primary fw-bold">${product.price}</div>
                                        ${additionalInfo}
                                    </div>
                                </div>
                            `;
                    });
                    
                    resultsHtml += '</div>';
                }
                
                // Categories section
                if (data.categories && data.categories.length > 0) {
                    hasResults = true;
                    resultsHtml += '<div class="search-section mb-3"><h6 class="fw-bold mb-2 text-dark"><i class="fas fa-th-large me-2"></i>Категорії</h6><ul class="list-unstyled">';
                    
                    data.categories.forEach(function(category) {
                        resultsHtml += `<li class="mb-1"><a href="${category.url}" class="text-decoration-none text-dark">• ${category.name}</a></li>`;
                    });
                    
                    resultsHtml += '</ul></div>';
                }
                
                // Manufacturers section
                if (data.manufacturers && data.manufacturers.length > 0) {
                    hasResults = true;
                    resultsHtml += '<div class="search-section mb-3"><h6 class="fw-bold mb-2 text-dark"><i class="fas fa-industry me-2"></i>Виробники</h6><ul class="list-unstyled">';
                    
                    data.manufacturers.forEach(function(manufacturer) {
                        resultsHtml += `<li class="mb-1"><a href="${manufacturer.url}" class="text-decoration-none text-dark">• ${manufacturer.name}</a></li>`;
                    });
                    
                    resultsHtml += '</ul></div>';
                }
                
                if (!hasResults) {
                    resultsHtml = '<div class="text-center text-muted py-3"><i class="fas fa-search fa-lg mb-2"></i><p class="mb-0">Нічого не знайдено</p></div>';
                }
                
                container.html(resultsHtml);
                container.show();
                
                // Add click handlers for products
                container.find('.search-result-item').on('click', function() {
                    const index = container.find('.search-result-item').index(this);
                    window.location.href = data.products[index].url;
                });
            }
                
                // Hide search results when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.search-box').length) {
                        $('#search-results').hide();
                    }
                });
            
                // Category menu hover functionality
                console.log('Setting up category hover events');
                console.log('Found category-main-item elements:', $('.category-main-item').length);
                
                $(document).on('mouseenter', '.category-main-item', function() {
                    const categoryId = $(this).data('category');
                    console.log('Hover on category:', categoryId, 'Element:', this);
                    
                    if (categoryId) {
                        loadSubcategories(categoryId);
                    } else {
                        console.log('No category ID found on element');
                    }
                });
                    
                // Reset subcategories when leaving the menu
                $('.category-dropdown').on('mouseleave', function() {
                    resetSubcategories();
                });
                
                // Function to load subcategories from HTML data
                function loadSubcategories(categoryId) {
                    console.log('Loading subcategories for category:', categoryId);
                    const subcontent = $('#category-subcontent');
                    
                    // Find subcategories data in hidden SEO section
                    const seoSubcategories = $(`.seo-subcategories[data-category="${categoryId}"]`);
                    console.log('Found SEO subcategories:', seoSubcategories.length);
                    console.log('All SEO sections:', $('.seo-subcategories').length);
                    console.log('Looking for category ID:', categoryId);
                    console.log('SEO subcategories HTML:', seoSubcategories.html());
                    
                    if (seoSubcategories.length > 0) {
                        let html = '<div class="category-sub-section">';
                        html += '<div class="category-sub-title">Підкатегорії</div>';
                        
                        seoSubcategories.find('ul > li').each(function() {
                            const mainLink = $(this).find('> a');
                            const subSubcategories = $(this).find('.sub-subcategories');
                            
                            // Add main subcategory
                            html += '<a class="category-sub-item" href="' + mainLink.attr('href') + '">' + mainLink.text() + '</a>';
                            
                            // Add sub-subcategories if they exist
                            if (subSubcategories.length > 0) {
                                subSubcategories.find('li').each(function() {
                                    const subLink = $(this).find('a');
                                    html += '<a class="category-sub-sub-item" href="' + subLink.attr('href') + '">' + subLink.text() + '</a>';
                                });
                            }
                        });
                        
                        html += '</div>';
                        subcontent.html(html);
                    } else {
                        subcontent.html('<div class="category-sub-section"><div class="category-sub-title">Немає підкатегорій</div></div>');
                    }
                }
                
                // Function to reset subcategories to default state
                function resetSubcategories() {
                    const subcontent = $('#category-subcontent');
                    const defaultHtml = `
                        <div class="category-sub-section">
                            <div class="category-sub-title">Оберіть категорію</div>
                            <div class="category-sub-item">Наведіть курсор на категорію зліва, щоб побачити підкатегорії</div>
                        </div>
                    `;
                    subcontent.html(defaultHtml);
                }
                
            }); // End of $(document).ready function
            
        } // Close initializeApp function
        
        // Initialize the app
        initializeApp();
        
    </script>
    
    {{-- Hidden subcategories for SEO and JavaScript --}}
    @if(isset($categories))
        <!-- Debug: Categories count: {{ $categories->count() }} -->
        <div style="display: none;">
            @foreach($categories as $category)
                @if($category->subcategories->count() > 0)
                    <!-- Debug: Category {{ $category->category_id }} has {{ $category->subcategories->count() }} subcategories -->
                    <div class="seo-subcategories" data-category="{{ $category->category_id }}">
                        <h3>{{ $category->name }}</h3>
                        <ul>
                            @foreach($category->subcategories as $subcategory)
                                <!-- Debug: Subcategory {{ $subcategory->category_id }}: {{ $subcategory->name }} -->
                                <li>
                                    <a href="{{ route('category.show', $subcategory->full_path) }}">
                                        {{ $subcategory->name }}
                                    </a>
                                    @if($subcategory->subcategories->count() > 0)
                                        <ul class="sub-subcategories">
                                            @foreach($subcategory->subcategories as $subSubcategory)
                                                <li>
                                                    <a href="{{ route('category.show', $subSubcategory->full_path) }}">
                                                        {{ $subSubcategory->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach
        </div>
    @endif


    <!-- Update counters script -->
    <script>
    $(document).ready(function() {
        // Update wishlist count
        function updateWishlistCount() {
            $.get('{{ route("wishlist.count") }}', function(response) {
                $('#wishlist-count').text(response.count);
            });
        }

        // Update cart count
        function updateCartCount() {
            $.get('{{ route("cart.count") }}', function(response) {
                $('#cart-count').text(response.count);
            });
        }

        // Update counts on page load
        updateWishlistCount();
        updateCartCount();

        // Add to wishlist
        $(document).on('click', '.add-to-wishlist', function() {
            const productId = $(this).data('product-id');
            const button = $(this);
            const originalHtml = button.html();
            
            button.html('<i class="fas fa-spinner fa-spin"></i>');
            button.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("wishlist.add") }}',
                method: 'POST',
                data: {
                    product_id: productId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        button.html('<i class="fas fa-check"></i>');
                        button.removeClass('btn-outline-danger').addClass('btn-success');
                        updateWishlistCount();
                        
                        setTimeout(() => {
                            button.html(originalHtml);
                            button.removeClass('btn-success').addClass('btn-outline-danger');
                            button.prop('disabled', false);
                        }, 2000);
                    }
                },
                error: function() {
                    button.html(originalHtml);
                    button.prop('disabled', false);
                }
            });
        });

        // Add to cart
        $(document).on('click', '.add-to-cart', function() {
            const productId = $(this).data('product-id');
            const button = $(this);
            const originalHtml = button.html();
            
            button.html('<i class="fas fa-spinner fa-spin"></i>');
            button.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("cart.add") }}',
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: 1,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        button.html('<i class="fas fa-check"></i>');
                        button.removeClass('btn-primary').addClass('btn-success');
                        updateCartCount();
                        
                        setTimeout(() => {
                            button.html(originalHtml);
                            button.removeClass('btn-success').addClass('btn-primary');
                            button.prop('disabled', false);
                        }, 2000);
                    }
                },
                error: function() {
                    button.html(originalHtml);
                    button.prop('disabled', false);
                }
            });
        });
    });
    </script>
</body>
</html>

