<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::with([
            'images' => fn($q) => $q->orderBy('sort_order'),
            'variants' => fn($q) => $q->with(['values.attribute', 'values.value']),
        ])->where('slug', $slug)->firstOrFail();

        $related = Product::with('images')
            ->where('id', '<>', $product->id)
            ->where('main_category_id', $product->main_category_id)
            ->take(8)
            ->get();

        // Gracias al alias de namespace 'shop' -> 'resources/views/landing',
        // la vista 'shop.product' resolverá a resources/views/landing/product.blade.php
        return view('shop.product', compact('product', 'related'));
    }

    /** Atajo: /nuevos -> /buscar?sort=new */
    public function new(): RedirectResponse
    {
        return redirect()->route('shop.search', ['sort' => 'new']);
    }

    /** Atajo: /ofertas -> /buscar?tag=sale */
    public function sale(): RedirectResponse
    {
        return redirect()->route('shop.search', ['tag' => 'sale']);
    }
}
