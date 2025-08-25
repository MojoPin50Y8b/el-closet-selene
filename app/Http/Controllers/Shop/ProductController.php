<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with([
            'images' => fn($q) => $q->orderBy('sort_order'),
            'variants' => fn($q) => $q->with(['values.attribute', 'values.value']),
        ])->where('slug', $slug)->firstOrFail();

        $related = Product::with('images')
            ->where('id', '<>', $product->id)
            ->where('main_category_id', $product->main_category_id)
            ->take(8)->get();
            
        return view('shop.product', compact('product', 'related'));   
    }
}
