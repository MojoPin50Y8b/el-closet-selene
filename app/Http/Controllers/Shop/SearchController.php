<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $sort = (string) $request->query('sort', '');
        $tag = (string) $request->query('tag', '');

        $query = Product::query()->with(['images']);

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        if ($tag === 'sale') {
            $query->whereHas('variants', fn($qq) => $qq->whereNotNull('sale_price'));
        }

        if ($sort === 'new') {
            $query->latest('products.created_at');
        } else {
            $query->orderBy('products.id', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        return view('landing.catalog.index', [
            'products' => $products,
            'q' => $q,
            'sort' => $sort,
            'tag' => $tag,
        ]);
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
