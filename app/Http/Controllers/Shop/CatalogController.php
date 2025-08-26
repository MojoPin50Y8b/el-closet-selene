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
        $min   = (int) $request->query('min', 0);
        $max   = (int) $request->query('max', 0);
        $size  = trim((string) $request->query('size', ''));   // ej. "m", "l"
        $color = trim((string) $request->query('color', ''));  // ej. "rojo", "red"
        $sort  = (string) $request->query('sort', '');         // ej. "new"

        // Base: productos de la categoría principal (¡sin is_active!)
        $query = Product::query()
            ->with(['images'])
            ->where('main_category_id', $category->id);

        // --- Filtro por precio (principal) sobre variantes: COALESCE(sale_price, price) ---
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

        // --- (Opcional) talla y color si existen atributos ---
        if ($size !== '') {
            $query->whereHas('variants.values', function (Builder $q) use ($size) {
                $q->whereHas('attribute', function (Builder $a) {
                    $a->whereIn('slug', ['size', 'talla']);
                })->whereHas('value', function (Builder $v) use ($size) {
                    $v->where('slug', $size)->orWhere('name', $size);
                });
            });
        }

        if ($color !== '') {
            $query->whereHas('variants.values', function (Builder $q) use ($color) {
                $q->whereHas('attribute', function (Builder $a) {
                    $a->where('slug', 'color');
                })->whereHas('value', function (Builder $v) use ($color) {
                    $v->where('slug', $color)->orWhere('name', $color);
                });
            });
        }

        // --- Orden simple (opcional) ---
        if ($sort === 'new') {
            $query->latest('products.created_at');
        } else {
            $query->orderBy('products.id', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        // Rango sugerido de precios en la categoría (para placeholders) — sin is_active
        $range = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('products.main_category_id', $category->id)
            ->selectRaw('
                MIN(COALESCE(product_variants.sale_price, product_variants.price)) as min_price,
                MAX(COALESCE(product_variants.sale_price, product_variants.price)) as max_price
            ')
            ->first();

        // (Opcional) valores disponibles de talla y color (si existen)
        $sizes = DB::table('variant_values as vv')
            ->join('attributes as a', 'vv.attribute_id', '=', 'a.id')
            ->join('attribute_values as av', 'vv.value_id', '=', 'av.id')
            ->join('product_variants as pv', 'vv.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.main_category_id', $category->id)
            ->whereIn('a.slug', ['size', 'talla'])
            ->select('av.name', 'av.slug')
            ->distinct()
            ->orderBy('av.name')
            ->get();

        $colors = DB::table('variant_values as vv')
            ->join('attributes as a', 'vv.attribute_id', '=', 'a.id')
            ->join('attribute_values as av', 'vv.value_id', '=', 'av.id')
            ->join('product_variants as pv', 'vv.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.main_category_id', $category->id)
            ->where('a.slug', 'color')
            ->select('av.name', 'av.slug')
            ->distinct()
            ->orderBy('av.name')
            ->get();

        return view('landing.catalog.category', [
            'category' => $category,
            'products' => $products,
            'range'    => $range,
            'filters'  => [
                'min'   => $min,
                'max'   => $max,
                'size'  => $size,
                'color' => $color,
                'sort'  => $sort,
            ],
            'sizes'  => $sizes,
            'colors' => $colors,
        ]);
    }
}
