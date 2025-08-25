<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landing\{HomeController, CategoryController, ProductController, CartController, CheckoutController};
use App\Http\Controllers\Admin\{DashboardController, ProductController as AdminProductController, CategoryController as AdminCategoryController, OrderController, CouponController, BannerController};


// Landing (público)
Route::get('/', HomeController::class)->name('landing.home');
Route::get('/catalogo', [CategoryController::class, 'index'])->name('landing.catalog');
Route::get('/categoria/{slug}', [CategoryController::class, 'show'])->name('landing.category.show');
Route::get('/producto/{slug}', [ProductController::class, 'show'])->name('landing.product.show');
Route::get('/nuevos', [ProductController::class, 'new'])->name('landing.new');
Route::get('/ofertas', [ProductController::class, 'sale'])->name('landing.sale');
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::get('/checkout', [CheckoutController::class, 'index'])->middleware('auth')->name('checkout.index');

// Dashboard (admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
    Route::resource('coupons', CouponController::class)->except(['show']);
    Route::resource('banners', BannerController::class)->except(['show']);
});

require __DIR__ . '/auth.php';
