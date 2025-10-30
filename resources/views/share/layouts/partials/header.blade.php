<!-- Top Menu (Vue) -->
<div class="bg-light border-bottom">
    <?php
        use App\Models\Menu\Menu;
        $__menutype = 'main-menu-add';
        $extractParams = function($item) {
            if (is_array($item->params)) {
                return $item->params;
            }
            if (is_string($item->params) && $item->params !== '') {
                $decoded = json_decode($item->params, true);
                return is_array($decoded) ? $decoded : [];
            }
            return [];
        };
        $shouldShow = function($item) use ($extractParams) {
            $params = $extractParams($item);
            // default: show if not specified
            return (int)($params['menu_show'] ?? 1) !== 0;
        };
        $buildTree = function ($parentId) use (&$buildTree, $__menutype, $shouldShow, $extractParams) {
            $children = Menu::ofType($__menutype)
                ->published()
                ->where('parent_id', $parentId)
                ->orderBy('ordering')
                ->get()
                ->filter($shouldShow);
            return $children->map(function ($item) use (&$buildTree, $shouldShow, $extractParams) {
                $params = $extractParams($item);
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'alias' => $item->alias,
                    'path' => $item->full_path,
                    'link' => $item->link,
                    'type' => $item->type,
                    'level' => $item->level,
                    'img' => $item->img,
                    'language' => $item->language,
                    'menu_show' => (int)($params['menu_show'] ?? 1),
                    'children' => $buildTree($item->id),
                ];
            })->values()->all();
        };
        $menuTreeTop = Menu::ofType($__menutype)->published()->root()->orderBy('ordering')->get()
            ->filter($shouldShow)
            ->map(function ($item) use (&$buildTree, $shouldShow, $extractParams) {
                $params = $extractParams($item);
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'alias' => $item->alias,
                    'path' => $item->full_path,
                    'link' => $item->link,
                    'type' => $item->type,
                    'level' => $item->level,
                    'img' => $item->img,
                    'language' => $item->language,
                    'menu_show' => (int)($params['menu_show'] ?? 1),
                    'children' => $buildTree($item->id),
                ];
            })->values()->all();
        $menuTreeJson = htmlspecialchars(json_encode($menuTreeTop, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
    ?>
    <div class="container py-2">
                <site-menu menutype="main-menu-add" layout="default">
            <script type="application/json" class="menu-data">{!! json_encode($menuTreeTop, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
        </site-menu>
    </div>
    
    <style>
        /* keep top menu subtle */
        .site-menu .menu-link{ color:#212529; }
        .site-menu .menu-link:hover{ color:#0d6efd; }
    </style>
</div>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left: mainmenu-rus via Vue site-menu -->
            <div class="me-auto">
                <?php
                    // Build menu tree for mainmenu-rus to feed Vue site-menu
                    use App\Models\Menu\Menu as __HeaderMenuModel;
                    $__headerMenutype = 'mainmenu-rus';
                    $__extractParams = function($item) {
                        if (is_array($item->params)) { return $item->params; }
                        if (is_string($item->params) && $item->params !== '') {
                            $decoded = json_decode($item->params, true);
                            return is_array($decoded) ? $decoded : [];
                        }
                        return [];
                    };
                    $__shouldShow = function($item) use ($__extractParams) {
                        $params = $__extractParams($item);
                        return (int)($params['menu_show'] ?? 1) !== 0;
                    };
                    $__buildTree = function ($parentId) use (&$__buildTree, $__headerMenutype, $__shouldShow, $__extractParams) {
                        $children = __HeaderMenuModel::ofType($__headerMenutype)
                            ->published()
                            ->where('parent_id', $parentId)
                            ->orderBy('ordering')
                            ->get()
                            ->filter($__shouldShow);
                        return $children->map(function ($item) use (&$__buildTree, $__extractParams) {
                            $params = $__extractParams($item);
                            return [
                                'id' => $item->id,
                                'title' => $item->title,
                                'alias' => $item->alias,
                                // Use alias as SEO-friendly path for our Vue menu generator
                                'path' => $item->alias,
                                'link' => $item->link,
                                'type' => $item->type,
                                'level' => $item->level,
                                'img' => $item->img,
                                'language' => $item->language,
                                'menu_show' => (int)($params['menu_show'] ?? 1),
                                'children' => $__buildTree($item->id),
                            ];
                        })->values()->all();
                    };
                    $__menuTreeHeader = __HeaderMenuModel::ofType($__headerMenutype)
                        ->published()->root()->orderBy('ordering')->get()
                        ->filter($__shouldShow)
                        ->map(function ($item) use (&$__buildTree, $__extractParams) {
                            $params = $__extractParams($item);
                            return [
                                'id' => $item->id,
                                'title' => $item->title,
                                'alias' => $item->alias,
                                'path' => $item->alias,
                                'link' => $item->link,
                                'type' => $item->type,
                                'level' => $item->level,
                                'img' => $item->img,
                                'language' => $item->language,
                                'menu_show' => (int)($params['menu_show'] ?? 1),
                                'children' => $__buildTree($item->id),
                            ];
                        })->values()->all();
                ?>
                <site-menu menutype="mainmenu-rus" layout="default">
                    <script type="application/json" class="menu-data">{!! json_encode($__menuTreeHeader, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
                </site-menu>
            </div>

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

<style>
    /* Ensure Vue site-menu is readable inside dark navbar */
    .navbar.bg-primary .site-menu { background: transparent !important; border: 0; }
    .navbar.bg-primary .site-menu .menu-link { color: #ffffff !important; padding: 12px 16px; }
    .navbar.bg-primary .site-menu .menu-link.active { color: #ffffff !important; border-bottom-color: rgba(255,255,255,.85); }
    .navbar.bg-primary .site-menu .menu-link:hover { color: #ffffff !important; background-color: rgba(255,255,255,0.12); border-bottom-color: rgba(255,255,255,.85); }
    .navbar.bg-primary .site-menu .menu-submenu { color: #212529; }
    .navbar.bg-primary .site-menu .menu-submenu .menu-link { color: #495057 !important; }
    .navbar.bg-primary .site-menu .menu-submenu .menu-link:hover { color: #0d6efd !important; }
    .navbar.bg-primary .site-menu .menu-root { gap: 0; }
    /* Fix hidden menu due to generic .collapse { visibility: collapse } */
    #navbarNav.collapse { visibility: visible !important; }
    .navbar .navbar-collapse.collapse { visibility: visible !important; }
    .collapse.show { visibility: visible !important; }
</style>

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
