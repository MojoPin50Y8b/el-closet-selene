<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Category};
use App\Models\Product;

class CatalogController extends Controller
{
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::with('images')
            ->where('main_category_id', $category->id)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('shop::catalog.category', compact('category', 'products'));
    }
}
