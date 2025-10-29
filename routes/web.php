<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Web\WishlistController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SaleBannerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\ManufacturerAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\CustomerAdminController;

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
Route::get('/', [ProductController::class, 'index'])->name('home');

// Product routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
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

// Vue products page
Route::get('/products-vue', [ProductController::class, 'index'])->name('products.vue');

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
