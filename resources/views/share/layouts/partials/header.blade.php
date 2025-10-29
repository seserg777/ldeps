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
                <!-- Auth Button (Vue) -->
                <li class="nav-item">
                    <div class="nav-link p-0">
                        <auth-button
                            :is-authenticated="{{ auth('custom')->check() ? 'true' : 'false' }}"
                            username="{{ auth('custom')->check() ? auth('custom')->user()->username : '' }}"
                            login-url="{{ route('auth.login.submit') }}"
                            logout-url="{{ route('auth.logout') }}"
                            csrf-token="{{ csrf_token() }}"
                        ></auth-button>
                    </div>
                </li>

                <!-- Cart -->
                <li class="nav-item">
                    <div class="nav-link p-0">
                        <mini-cart
                            cart-index-url="{{ route('cart.index') }}"
                            cart-modal-url="{{ route('cart.modal') }}"
                            cart-remove-url="{{ route('cart.remove') }}"
                            csrf-token="{{ csrf_token() }}"
                            count-url="{{ route('cart.count') }}"
                            use-float="false"
                        ></mini-cart>
                    </div>
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
