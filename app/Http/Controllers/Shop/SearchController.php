<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $products = Product::with('images')
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->paginate(12)->withQueryString();
            
        return view('shop.search', compact('products', 'q'));
    }

    public function suggest(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '')
            return response()->json(['suggestions' => []]);

        $items = Product::select('name', 'slug')
            ->where('name', 'like', "%{$q}%")
            ->limit(5)->get();

        return response()->json([
            'suggestions' => $items->map(fn($p) => [
                'text' => $p->name,
                'url' => route('shop.product', $p->slug),
            ])
        ]);
    }
}
