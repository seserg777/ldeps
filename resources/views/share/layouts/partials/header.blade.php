<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand" href="{{ route('products.index') }}">
            <i class="fas fa-shopping-bag me-2"></i>Каталог товарів
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left Navigation -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}">
                        <i class="fas fa-home me-1"></i>Головна
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-th-large me-1"></i>Категорії
                    </a>
                    <ul class="dropdown-menu">
                        @foreach(\App\Models\Category\Category::where('category_publish', 1)->take(10)->get() as $category)
                            <li>
                                <a class="dropdown-item" href="{{ route('category.show', $category->category_id) }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('categories.index') }}">Всі категорії</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sale-banners.index') }}">
                        <i class="fas fa-percent me-1"></i>Акції
                    </a>
                </li>
            </ul>

            <!-- Search -->
            <form class="d-flex me-3" method="GET" action="{{ route('products.index') }}">
                <div class="input-group">
                    <input class="form-control" type="search" name="search" placeholder="Пошук товарів..." value="{{ request('search') }}">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Right Navigation -->
            <ul class="navbar-nav">
                @auth('custom')
                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ auth('custom')->user()->username }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}">
                                <i class="fas fa-user me-2"></i>Профіль
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('wishlist.index') }}">
                                <i class="fas fa-heart me-2"></i>Список бажань
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
                    </li>
                @else
                    <!-- Guest Menu -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>Увійти
                        </a>
                    </li>
                @endauth
                
                <!-- Cart -->
                <li class="nav-item">
                    <button 
                        class="nav-link text-light border-0 bg-transparent" 
                        title="Кошик"
                        onclick="openCartModal()"
                    >
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge bg-danger ms-1" id="cart-count">0</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Top Bar (Optional) -->
@if(config('app.show_top_bar', false))
    <div class="bg-dark text-light py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>
                        <i class="fas fa-phone me-1"></i>
                        +38 (067) 123-45-67
                        <span class="ms-3">
                            <i class="fas fa-envelope me-1"></i>
                            info@example.com
                        </span>
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small>
                        <i class="fas fa-truck me-1"></i>
                        Безкоштовна доставка від 1000₴
                    </small>
                </div>
            </div>
        </div>
    </div>
@endif
