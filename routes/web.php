<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\{
    HomeController,
    CatalogController,
    ProductController as ShopProductController,
    CartController,
    CheckoutController,
    SearchController,
    NewsletterController
};

// ---------- PÚBLICO ----------
Route::name('shop.')->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    // catálogo / producto
    Route::get('/categoria/{slug}', [CatalogController::class, 'category'])->name('category');
    Route::get('/producto/{slug}', [ShopProductController::class, 'show'])->name('product');

    // búsqueda
    Route::get('/buscar', [SearchController::class, 'index'])->name('search');
    Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

    // atajos "Nuevos" y "Ofertas" (crean shop.new y shop.sale)
    Route::get('/nuevos', [ShopProductController::class, 'new'])->name('new');
    Route::get('/ofertas', [ShopProductController::class, 'sale'])->name('sale');

    // newsletter
    Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

    // carrito
    Route::get('/carrito', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/mini', [CartController::class, 'mini'])->name('cart.mini');
    // contador del carrito (JSON)
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');


    // checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
    Route::post('/checkout/place', [CheckoutController::class, 'place'])->name('checkout.place');
});

// (No más alias landing.* con las mismas URIs — evitar duplicados)

require __DIR__ . '/auth.php';
