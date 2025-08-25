<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // $q = $request->get('q');
        // $products = Product::where('name', 'like', "%{$q}%")->paginate(12);
        // return view('shop.search', compact('products','q'));
        return view('shop.search');
    }

    public function suggest(Request $request)
    {
        // retornar sugerencias (stub)
        return response()->json(['suggestions' => []]);
    }
}
