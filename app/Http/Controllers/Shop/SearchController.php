<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    /**
     * Resultados de búsqueda / listados especiales.
     * Soporta:
     *   - q        : texto a buscar
     *   - tag=sale : solo ofertas (price/sale_price a nivel producto o variante)
     *   - sort=new : orden por más nuevos
     */
    public function index(Request $request)
    {
        $q    = trim((string) $request->query('q', ''));
        $tag  = (string) $request->query('tag', '');
        $sort = (string) $request->query('sort', '');

        $query = Product::query()
            ->with(['images', 'variants']);

        // texto
        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // ofertas
        if ($tag === 'sale') {
            $query->where(function ($qq) {
                $qq->whereNotNull('sale_price')
                   ->orWhereHas('variants', fn($v) => $v->whereNotNull('sale_price'));
            });
        }

        // orden
        if ($sort === 'new') {
            $query->latest('products.created_at');
        } else {
            $query->orderBy('products.id', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        $title = $q !== ''
            ? "Resultados: \"{$q}\""
            : ($tag === 'sale' ? 'Ofertas'
                : ($sort === 'new' ? 'Novedades' : 'Búsqueda'));

        return view('landing.catalog.index', [
            'title'    => $title,
            'products' => $products,
            // por si quieres reutilizar en la vista
            'q'        => $q,
            'tag'      => $tag,
            'sort'     => $sort,
        ]);
    }

    /**
     * Sugerencias de búsqueda para el autocompletado.
     * Devuelve: [{ slug, name, thumb }]
     */
    public function suggest(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['data' => []]);
        }

        $items = Product::query()
            ->with('images')
            ->where('name', 'like', "%{$q}%")
            ->orderBy('name')
            ->take(8)
            ->get();

        $data = $items->map(function ($p) {
            $img = optional($p->images->first())->url;
            return [
                'slug'  => $p->slug,
                'name'  => $p->name,
                'thumb' => $img ?: null,
            ];
        });

        return response()->json(['data' => $data]);
    }
}
