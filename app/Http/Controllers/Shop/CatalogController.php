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

        // --- Filtros desde query ---
        $min = (int) $request->query('min', 0);
        $max = (int) $request->query('max', 0);
        $size = trim((string) $request->query('size', ''));   // ej. "M"
        $color = trim((string) $request->query('color', ''));  // ej. "rojo" o "red"
        $sort = (string) $request->query('sort', '');

        // Base: productos de la categoría principal (sin is_active)
        $query = Product::query()
            ->with(['images', 'variants'])
            ->where('main_category_id', $category->id);

        // --- Filtro por precio sobre variantes: COALESCE(sale_price, price) ---
        if ($min || $max) {
            $query->whereHas('variants', function (Builder $q) use ($min, $max) {
                if ($min) {
                    $q->whereRaw('COALESCE(sale_price, price) >= ?', [$min]);
                }
                if ($max) {
                    $q->whereRaw('COALESCE(sale_price, price) <= ?', [$max]);
                }
            });
        }

        // Normalizamos para comparar case-insensitive
        $sizeNeedle = mb_strtolower($size, 'UTF-8');
        $colorNeedle = mb_strtolower($color, 'UTF-8');

        // --- Filtro por talla (attribute_values.value) ---
        if ($size !== '') {
            $query->whereHas('variants.values', function (Builder $q) use ($sizeNeedle) {
                $q->whereHas('attribute', fn(Builder $a) => $a->whereIn('slug', ['size', 'talla']))
                    ->whereHas('value', function (Builder $v) use ($sizeNeedle) {
                        // attribute_values.value
                        $v->whereRaw('LOWER(`value`) = ?', [$sizeNeedle]);
                    });
            });
        }

        // --- Filtro por color (value o code) ---
        if ($color !== '') {
            $query->whereHas('variants.values', function (Builder $q) use ($colorNeedle) {
                $q->whereHas('attribute', fn(Builder $a) => $a->where('slug', 'color'))
                    ->whereHas('value', function (Builder $v) use ($colorNeedle) {
                        // attribute_values.value o attribute_values.code
                        $v->whereRaw('LOWER(`value`) = ?', [$colorNeedle])
                            ->orWhereRaw('LOWER(`code`) = ?', [$colorNeedle]);
                    });
            });
        }

        // --- Orden simple ---
        if ($sort === 'new') {
            $query->latest('products.created_at');
        } else {
            $query->orderBy('products.id', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        // Rango sugerido de precios (sin is_active)
        $range = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('products.main_category_id', $category->id)
            ->selectRaw('MIN(COALESCE(product_variants.sale_price, product_variants.price)) as min_price,
                     MAX(COALESCE(product_variants.sale_price, product_variants.price)) as max_price')
            ->first();

        // ----- Facets (tallas / colores) con tu esquema -----

        // TALLAS: variant_values.attribute_value_id -> attribute_values.id
        $sizes = DB::table('variant_values as vv')
            ->join('attributes as a', 'vv.attribute_id', '=', 'a.id')
            ->join('attribute_values as av', 'vv.attribute_value_id', '=', 'av.id')
            ->join('product_variants as pv', 'vv.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.main_category_id', $category->id)
            ->whereIn('a.slug', ['size', 'talla'])
            // usamos av.value como label y como "slug" del filtro
            ->selectRaw('DISTINCT av.value as label, av.value as slug')
            ->orderBy('label')
            ->get();

        // COLORES: mostramos label (value), y por si tienes code/hex, los exponemos
        $colors = DB::table('variant_values as vv')
            ->join('attributes as a', 'vv.attribute_id', '=', 'a.id')
            ->join('attribute_values as av', 'vv.attribute_value_id', '=', 'av.id')
            ->join('product_variants as pv', 'vv.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.main_category_id', $category->id)
            ->where('a.slug', 'color')
            ->selectRaw('DISTINCT av.value as label, COALESCE(av.code, av.value) as slug, av.hex')
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
