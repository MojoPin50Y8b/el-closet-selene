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
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['data' => []]);
        }

        $products = Product::query()
            ->where('status', 'published')
            ->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            })
            ->with(['images' => fn($img) => $img->orderBy('sort_order')->limit(1)])
            ->take(8)
            ->get();

        $items = $products->map(function ($p) {
            $thumb = optional($p->images->first())->url;
            return [
                'name' => $p->name,
                'slug' => $p->slug,
                'thumb' => $thumb,
            ];
        });

        return response()->json(['data' => $items]);
    }
}
