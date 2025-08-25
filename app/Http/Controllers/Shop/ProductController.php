<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
// use App\Models\Product;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        // $product = Product::where('slug', $slug)->with(['images','variants'])->firstOrFail();
        // return view('shop.product', compact('product'));
        return view('shop.product', ['slug' => $slug]); // stub
    }
}
