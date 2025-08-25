<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Category};

class CatalogController extends Controller
{
    public function category(string $slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = $category->products()->with(['images', 'variants']);

        // Filtros mínimos
        if ($request->filled('min')) {
            $min = (float) $request->min;
            $query->whereHas('variants', fn($q) => $q->where('price', '>=', $min));
        }
        if ($request->filled('max')) {
            $max = (float) $request->max;
            $query->whereHas('variants', fn($q) => $q->where('price', '<=', $max));
        }

        $products = $query->paginate(12)->withQueryString();
        return view('shop.category', compact('category', 'products'));
    }
}
