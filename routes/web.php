<?php

use Illuminate\Support\Facades\Route;

// ==================== PUBLICO (SHOP) ====================
use App\Http\Controllers\Shop\{
    HomeController,
    CatalogController,
    ProductController as ShopProductController,
    CartController,
    CheckoutController,
    SearchController,
    NewsletterController
};

// ==================== ADMIN ====================
use App\Http\Controllers\Admin\{
    DashboardController,
    ProductController as AdminProductController,
    CategoryController as AdminCategoryController,
    OrderController,
    CouponController,
    BannerController
};

// ---------- PÚBLICO ----------
Route::name('shop.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/categoria/{slug}', [CatalogController::class, 'category'])->name('category');
    Route::get('/producto/{slug}', [ShopProductController::class, 'show'])->name('product');

    Route::get('/buscar', [SearchController::class, 'index'])->name('search');
    Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

    Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

    Route::get('/carrito', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/mini', [CartController::class, 'mini'])->name('cart.mini');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
    Route::post('/checkout/place', [CheckoutController::class, 'place'])->name('checkout.place');
});

// ---------- ADMIN ----------
Route::middleware(['auth','role:admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', AdminProductController::class);
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::resource('orders', OrderController::class)->only(['index','show','update']);
        Route::resource('coupons', CouponController::class)->except(['show']);
        Route::resource('banners', BannerController::class)->except(['show']);
    });

require __DIR__.'/auth.php';
