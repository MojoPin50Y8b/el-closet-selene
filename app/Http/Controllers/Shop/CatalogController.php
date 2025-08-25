<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CatalogController extends Controller
{
    public function category(string $slug)
    {
        // $category = Category::where('slug', $slug)->firstOrFail();
        // $products = $category->products()->latest()->paginate(12);
        // return view('shop.category', compact('category', 'products'));
        return view('shop.category', ['slug' => $slug]); // stub
    }
}
