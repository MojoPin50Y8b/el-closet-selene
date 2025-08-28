<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    /**
     * Listado por categoría con filtros (precio, talla, color) y orden simple.
     */
    public function category(Request $request, string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // --- Filtros desde query ---
        $min = (int) $request->query('min', 0);
        $max = (int) $request->query('max', 0);
        $size = trim((string) $request->query('size', ''));   // ej. "m"
        $color = trim((string) $request->query('color', ''));  // ej. "rojo" o "red"
        $sort = (string) $request->query('sort', '');         // ej. "new"

        // Base: productos de la categoría principal (sin usar is_active)
        $query = Product::query()
            ->with(['images', 'variants']) // para la card (precio "desde" por variantes)
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

        // Normalizamos para comparar case-insensitive / slugs derivados
        $sizeNeedle = mb_strtolower($size, 'UTF-8');
        $colorNeedle = mb_strtolower($color, 'UTF-8');

        /** ---------------- TALLA (atributo size/talla) ----------------
         * Coincidimos por:
         *  - attribute_values.code (si lo usas para tallas)
         *  - "slug" derivado de value (lower + espacios->guiones)
         *  - value en minúsculas
         */
        if ($size !== '') {
            $query->whereExists(function ($sub) use ($sizeNeedle) {
                $sub->from('product_variants as pv')
                    ->join('variant_values as vv', 'vv.variant_id', '=', 'pv.id')
                    ->join('attributes as a', 'a.id', '=', 'vv.attribute_id')
                    ->join('attribute_values as av', 'av.id', '=', 'vv.attribute_value_id')
                    ->whereColumn('pv.product_id', 'products.id')
                    ->whereIn('a.slug', ['size', 'talla'])
                    ->where(function ($w) use ($sizeNeedle) {
                        $w->whereRaw('LOWER(av.code) = ?', [$sizeNeedle])
                            ->orWhereRaw('LOWER(REPLACE(av.value, " ", "-")) = ?', [$sizeNeedle])
                            ->orWhereRaw('LOWER(av.value) = ?', [$sizeNeedle]);
                    });
            });
        }

        /** ---------------- COLOR (atributo color) ----------------
         * Coincidimos por:
         *  - attribute_values.code (p.ej. "rojo" o "red")
         *  - "slug" derivado de value
         *  - value en minúsculas
         */
        if ($color !== '') {
            $query->whereExists(function ($sub) use ($colorNeedle) {
                $sub->from('product_variants as pv')
                    ->join('variant_values as vv', 'vv.variant_id', '=', 'pv.id')
                    ->join('attributes as a', 'a.id', '=', 'vv.attribute_id')
                    ->join('attribute_values as av', 'av.id', '=', 'vv.attribute_value_id')
                    ->whereColumn('pv.product_id', 'products.id')
                    ->where('a.slug', 'color')
                    ->where(function ($w) use ($colorNeedle) {
                        $w->whereRaw('LOWER(av.code) = ?', [$colorNeedle])
                            ->orWhereRaw('LOWER(REPLACE(av.value, " ", "-")) = ?', [$colorNeedle])
                            ->orWhereRaw('LOWER(av.value) = ?', [$colorNeedle]);
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

        // Rango sugerido de precios en la categoría (placeholders del filtro de precio)
        $range = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('products.main_category_id', $category->id)
            ->selectRaw('
                MIN(COALESCE(product_variants.sale_price, product_variants.price)) as min_price,
                MAX(COALESCE(product_variants.sale_price, product_variants.price)) as max_price
            ')
            ->first();

        // ---------------- Facets (tallas / colores) ----------------
        // TALLAS (attributes.slug IN size,talla)
        $sizes = DB::table('variant_values as vv')
            ->join('attributes as a', 'vv.attribute_id', '=', 'a.id')
            ->join('attribute_values as av', 'vv.attribute_value_id', '=', 'av.id')
            ->join('product_variants as pv', 'vv.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.main_category_id', $category->id)
            ->whereIn('a.slug', ['size', 'talla'])
            ->selectRaw('DISTINCT av.value AS name,
                COALESCE(av.code, LOWER(REPLACE(av.value, " ", "-"))) AS slug')
            ->orderBy('name')
            ->get();

        // COLORES (attributes.slug = color)
        $colors = DB::table('variant_values as vv')
            ->join('attributes as a', 'vv.attribute_id', '=', 'a.id')
            ->join('attribute_values as av', 'vv.attribute_value_id', '=', 'av.id')
            ->join('product_variants as pv', 'vv.variant_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->where('p.main_category_id', $category->id)
            ->where('a.slug', 'color')
            ->selectRaw('DISTINCT av.value AS name,
                COALESCE(av.code, LOWER(REPLACE(av.value, " ", "-"))) AS slug')
            ->orderBy('name')
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
