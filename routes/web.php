<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ContentController as WebContentController;
use App\Http\Controllers\Web\BannerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Web\WishlistController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SaleBannerController;
use App\Http\Controllers\Web\MenuController as WebMenuController;
use App\Http\Controllers\Web\HomeController as WebHomeController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\JshoppingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\ManufacturerAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\CustomerAdminController;
use App\Http\Controllers\Admin\MenuTypeController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Main product catalog page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Content routes
Route::get('/articles', [WebContentController::class, 'index'])->name('content.index');
Route::get('/articles/{id}', [WebContentController::class, 'show'])->name('content.show');

// Banner routes
Route::get('/promo', [BannerController::class, 'index'])->name('banners.index');
Route::get('/banner/{id}', [BannerController::class, 'show'])->name('banners.show');

// Route for individual banners: /promo/449.html (must be before generic routes)
Route::get('/{category}/{id}.html', [HomeController::class, 'showBanner'])->name('banner.show')->where('category', '[a-zA-Z0-9\-_]+')->where('id', '[0-9]+');

// SEO-friendly URLs for menu items
Route::get('/{path}.html', [HomeController::class, 'showPage'])->name('page.show')->where('path', '[a-zA-Z0-9\-_]+');

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
// SSR products module must be before /products/{id}
Route::get('/products/html', [ProductController::class, 'moduleHtml'])->name('products.module.html');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Category routes with hierarchical alias
Route::get('/categories', [ProductController::class, 'categories'])->name('categories.index');
Route::get('/category/{path}', [ProductController::class, 'category'])->name('category.show')->where('path', '.*');

// Language switching
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

// AJAX routes for dynamic loading
Route::get('/api/products', [ProductController::class, 'getProducts'])->name('products.api');
Route::get('/api/filters', [ProductController::class, 'getFilters'])->name('filters.api');
Route::get('/api/search', [ProductController::class, 'search'])->name('search.api');
Route::get('/api/jshopping/category/{id}', [JshoppingController::class, 'category'])->name('api.jshopping.category');

// Vue products page
Route::get('/products-vue', [ProductController::class, 'index'])->name('products.vue');

// Server-rendered menu HTML for hydration/SEO
Route::get('/menu/html/{menutype}', [WebMenuController::class, 'html'])->name('menu.html');

// Page meta for Vue components (PHP-SSR + hydration; JSON only)
Route::get('/api/page-meta/{path}', [WebHomeController::class, 'pageMeta'])
    ->where('path', '[a-zA-Z0-9\-_]+')
    ->name('page.meta');

// (moved above product {id} route)

// Wishlist routes
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::get('/cart/modal', [CartController::class, 'modal'])->name('cart.modal');

// Static files routes
Route::get('/images/{filename}', function ($filename) {
    $path = public_path('images/' . $filename);
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
})->where('filename', '.*');

Route::get('/favicon.ico', function () {
    $path = public_path('favicon.ico');
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
});

Route::get('/sw.js', function () {
    $path = public_path('sw.js');
    if (file_exists($path)) {
        return response()->file($path);
    }
    abort(404);
});

// Custom Authentication routes
Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    
    // Protected routes for users
    Route::middleware(['custom.auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('auth.dashboard');
        })->name('auth.dashboard');
    });
});

// Short route for dashboard (redirects to auth/dashboard)
Route::get('/dashboard', function () {
    return redirect()->route('auth.dashboard');
})->middleware(['custom.auth'])->name('dashboard');

// Profile routes
Route::middleware(['custom.auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/editaccount', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Sale Banner routes
Route::get('/sale-banners', [SaleBannerController::class, 'index'])->name('sale-banners.index');
Route::get('/sale-banners/{alias}', [SaleBannerController::class, 'show'])->name('sale-banner.show');
Route::get('/sale-banners/tag/{tagAlias}', [SaleBannerController::class, 'tag'])->name('sale-banner.tag');
Route::get('/api/sale-banners', [SaleBannerController::class, 'getBanners'])->name('sale-banners.api');

// Admin routes
// Admin auth (no site header/layout)
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});

Route::prefix('admin')
    ->middleware(['web', 'custom.auth', 'admin'])
    ->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        // Catalog
        Route::get('/products', [ProductAdminController::class, 'index'])->name('admin.products');
        Route::get('/products/create', [ProductAdminController::class, 'create'])->name('admin.products.create');
        Route::post('/products', [ProductAdminController::class, 'store'])->name('admin.products.store');
        Route::get('/products/{id}/edit', [ProductAdminController::class, 'edit'])->name('admin.products.edit');
        Route::put('/products/{id}', [ProductAdminController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{id}', [ProductAdminController::class, 'destroy'])->name('admin.products.destroy');
        Route::get('/categories', [CategoryAdminController::class, 'index'])->name('admin.categories');
        Route::get('/categories/create', [CategoryAdminController::class, 'create'])->name('admin.categories.create');
        Route::post('/categories', [CategoryAdminController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{id}/edit', [CategoryAdminController::class, 'edit'])->name('admin.categories.edit');
        Route::put('/categories/{id}', [CategoryAdminController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{id}', [CategoryAdminController::class, 'destroy'])->name('admin.categories.destroy');
        
        Route::get('/manufacturers', [ManufacturerAdminController::class, 'index'])->name('admin.manufacturers');
        Route::get('/manufacturers/create', [ManufacturerAdminController::class, 'create'])->name('admin.manufacturers.create');
        Route::post('/manufacturers', [ManufacturerAdminController::class, 'store'])->name('admin.manufacturers.store');
        Route::get('/manufacturers/{id}/edit', [ManufacturerAdminController::class, 'edit'])->name('admin.manufacturers.edit');
        Route::put('/manufacturers/{id}', [ManufacturerAdminController::class, 'update'])->name('admin.manufacturers.update');
        Route::delete('/manufacturers/{id}', [ManufacturerAdminController::class, 'destroy'])->name('admin.manufacturers.destroy');

        // Sales
        Route::get('/orders', [OrderAdminController::class, 'index'])->name('admin.orders');
        Route::get('/customers', [CustomerAdminController::class, 'index'])->name('admin.customers');

        // Menu Management
        Route::prefix('menu')->group(function () {
            // Menu Types
            Route::get('/types', [MenuTypeController::class, 'index'])->name('admin.menu.types.index');
            Route::get('/types/create', [MenuTypeController::class, 'create'])->name('admin.menu.types.create');
            Route::post('/types', [MenuTypeController::class, 'store'])->name('admin.menu.types.store');
            Route::get('/types/{menuType}', [MenuTypeController::class, 'show'])->name('admin.menu.types.show');
            Route::get('/types/{menuType}/edit', [MenuTypeController::class, 'edit'])->name('admin.menu.types.edit');
            Route::put('/types/{menuType}', [MenuTypeController::class, 'update'])->name('admin.menu.types.update');
            Route::delete('/types/{menuType}', [MenuTypeController::class, 'destroy'])->name('admin.menu.types.destroy');
            Route::post('/types/update-ordering', [MenuTypeController::class, 'updateOrdering'])->name('admin.menu.types.update-ordering');

            // Menu Items
            Route::get('/{menutype}/items', [MenuController::class, 'index'])->name('admin.menu.items.index');
            Route::get('/{menutype}/items/create', [MenuController::class, 'create'])->name('admin.menu.items.create');
            Route::post('/{menutype}/items', [MenuController::class, 'store'])->name('admin.menu.items.store');
            Route::get('/{menutype}/items/{menu}', [MenuController::class, 'show'])->name('admin.menu.items.show');
            Route::get('/{menutype}/items/{menu}/edit', [MenuController::class, 'edit'])->name('admin.menu.items.edit');
            Route::put('/{menutype}/items/{menu}', [MenuController::class, 'update'])->name('admin.menu.items.update');
            Route::delete('/{menutype}/items/{menu}', [MenuController::class, 'destroy'])->name('admin.menu.items.destroy');
            Route::post('/{menutype}/items/{menu}/toggle-published', [MenuController::class, 'togglePublished'])->name('admin.menu.items.toggle-published');
            Route::post('/{menutype}/items/{menu}/trash', [MenuController::class, 'trash'])->name('admin.menu.items.trash');
            Route::post('/{menutype}/items/{menu}/restore', [MenuController::class, 'restore'])->name('admin.menu.items.restore');
        });

        // Content Management
        Route::prefix('content')->group(function () {
            // Categories
            Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
            Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
            Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
            Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('admin.categories.show');
            Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
            Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
            Route::post('/categories/{category}/toggle-published', [CategoryController::class, 'togglePublished'])->name('admin.categories.toggle-published');

            // Content
            Route::get('/', [ContentController::class, 'index'])->name('admin.content.index');
            Route::get('/create', [ContentController::class, 'create'])->name('admin.content.create');
            Route::post('/', [ContentController::class, 'store'])->name('admin.content.store');
            Route::get('/{content}', [ContentController::class, 'show'])->name('admin.content.show');
            Route::get('/{content}/edit', [ContentController::class, 'edit'])->name('admin.content.edit');
            Route::put('/{content}', [ContentController::class, 'update'])->name('admin.content.update');
            Route::delete('/{content}', [ContentController::class, 'destroy'])->name('admin.content.destroy');
            Route::post('/{content}/toggle-published', [ContentController::class, 'togglePublished'])->name('admin.content.toggle-published');
            Route::post('/{content}/archive', [ContentController::class, 'archive'])->name('admin.content.archive');
            Route::post('/{content}/trash', [ContentController::class, 'trash'])->name('admin.content.trash');
            Route::post('/{content}/restore', [ContentController::class, 'restore'])->name('admin.content.restore');
            Route::post('/{content}/toggle-featured', [ContentController::class, 'toggleFeatured'])->name('admin.content.toggle-featured');
        });
    });

// Static files
Route::get('/css/{filename}', function ($filename) {
    $path = public_path("css/{$filename}");
    if (file_exists($path)) {
        return response()->file($path, ['Content-Type' => 'text/css']);
    }
    abort(404);
})->name('css.file');

Route::get('/js/{filename}', function ($filename) {
    $path = public_path("js/{$filename}");
    if (file_exists($path)) {
        return response()->file($path, ['Content-Type' => 'application/javascript']);
    }
    abort(404);
})->name('js.file');

// Product routes with hierarchical paths (must be last)
Route::get('/{path}', [ProductController::class, 'showByPath'])->name('products.show-by-path')->where('path', '.*');
