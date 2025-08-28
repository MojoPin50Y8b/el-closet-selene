<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with([
            'images' => fn($q) => $q->orderBy('sort_order'),
            'variants.values.attribute',
            'variants.values.value',
        ])->where('slug', $slug)->firstOrFail();

        $related = Product::with('images')
            ->where('id', '<>', $product->id)
            ->where('main_category_id', $product->main_category_id)
            ->take(8)->get();

        // Nota: usamos el namespace 'shop::' que aliaste a resources/views/landing
        return view('shop::product.show', compact('product', 'related'));
    }

    // /ofertas
    public function sale(Request $request)
    {
        $now = now();

        $products = Product::query()
            ->with(['images', 'variants'])
            ->whereHas('variants', function (Builder $v) use ($now) {
                $v->whereNotNull('sale_price')
                    ->where(function (Builder $d) use ($now) {
                        $d->whereNull('sale_starts_at')->orWhere('sale_starts_at', '<=', $now);
                    })
                    ->where(function (Builder $d) use ($now) {
                        $d->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>=', $now);
                    });
            })
            ->latest('products.created_at')
            ->paginate(12)
            ->withQueryString();

        return view('landing.catalog.index', [
            'title' => 'Ofertas',
            'products' => $products,
            'filters' => ['tag' => 'sale'],
            'category' => null,
        ]);
    }

    // /nuevos
    public function new(Request $request)
    {
        $products = Product::query()
            ->with(['images', 'variants'])
            ->latest('products.created_at')
            ->paginate(12)
            ->withQueryString();

        return view('landing.catalog.index', [
            'title' => 'Nuevos ingresos',
            'products' => $products,
            'filters' => ['sort' => 'new'],
            'category' => null,
        ]);
    }
}
