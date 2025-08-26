<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\{Category, Product};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    /**
     * Listado por categoría con filtros.
     */
    public function category(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // Filtros de query
        $min = (int) $request->query('min', 0);
        $max = (int) $request->query('max', 0);
        $size = trim((string) $request->query('size', ''));
        $color = trim((string) $request->query('color', ''));
        $sort = (string) $request->query('sort', '');

        // Base: productos de la categoría principal (¡sin is_active!)
        $query = Product::query()
            ->with(['images'])
            ->where('main_category_id', $category->id);

        // Precio (sobre variantes)
        if ($min || $max) {
            $query->whereHas('variants', function (Builder $q) use ($min, $max) {
                if ($min)
                    $q->whereRaw('COALESCE(sale_price, price) >= ?', [$min]);
                if ($max)
                    $q->whereRaw('COALESCE(sale_price, price) <= ?', [$max]);
            });
        }

        // Talla
        if ($size !== '') {
            $query->whereHas('variants.values', function (Builder $q) use ($size) {
                $q->whereHas('attribute', fn(Builder $a) => $a->whereIn('slug', ['size', 'talla']))
                    ->whereHas('value', fn(Builder $v) => $v->where('slug', $size)->orWhere('slug', $size));
            });
        }

        // Color
        if ($color !== '') {
            $query->whereHas('variants.values', function (Builder $q) use ($color) {
                $q->whereHas('attribute', fn(Builder $a) => $a->where('slug', 'color'))
                    ->whereHas('value', fn(Builder $v) => $v->where('slug', $color)->orWhere('slug', $color));
            });
        }

        // Orden
        if ($sort === 'new') {
            $query->latest('products.created_at');
        } else {
            $query->orderBy('products.id', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        // Rango sugerido de precios (sin products.is_active)
        $range = DB::table('product_variants as pv')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.main_category_id', $category->id)
            ->selectRaw('MIN(COALESCE(pv.sale_price, pv.price)) as min_price,
                         MAX(COALESCE(pv.sale_price, pv.price)) as max_price')
            ->first();

        // Facets (usa SOLO slug; tu tabla attribute_values no tiene 'name')
        $sizes = DB::table('variant_values as vv')
            ->join('attributes as a', 'vv.attribute_id', '=', 'a.id')
            ->join('attribute_values as av', 'vv.value_id', '=', 'av.id')
            ->join('product_variants as pv', 'vv.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.main_category_id', $category->id)
            ->whereIn('a.slug', ['size', 'talla'])
            ->selectRaw('DISTINCT av.slug as slug, av.slug as label')
            ->orderBy('label')
            ->get();

        $colors = DB::table('variant_values as vv')
            ->join('attributes as a', 'vv.attribute_id', '=', 'a.id')
            ->join('attribute_values as av', 'vv.value_id', '=', 'av.id')
            ->join('product_variants as pv', 'vv.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.main_category_id', $category->id)
            ->where('a.slug', 'color')
            ->selectRaw('DISTINCT av.slug as slug, av.slug as label')
            ->orderBy('label')
            ->get();

        return view('landing.catalog.category', [
            'category' => $category,
            'products' => $products,
            'range' => $range,
            'filters' => [
                'min' => $min,
                'max' => $max,
                'size' => $size,
                'color' => $color,
                'sort' => $sort,
            ],
            'sizes' => $sizes,
            'colors' => $colors,
        ]);
    }
}
