<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

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
        $q = trim((string) $request->query('q', ''));
        $sort = (string) $request->query('sort', '');
        $tag = (string) $request->query('tag', '');

        $query = Product::query()
            ->with(['images', 'variants']);

        // Texto libre
        if ($q !== '') {
            $query->where(function (Builder $w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Tag: sale  -> sale_price en VARIANTES (y opcionalmente ventana de fechas)
        if ($tag === 'sale') {
            $now = now();
            $query->whereHas('variants', function (Builder $v) use ($now) {
                $v->whereNotNull('sale_price')
                    ->where(function (Builder $d) use ($now) {
                        // Activa si sin fechas o dentro de ventana
                        $d->whereNull('sale_starts_at')->orWhere('sale_starts_at', '<=', $now);
                    })
                    ->where(function (Builder $d) use ($now) {
                        $d->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>=', $now);
                    });
            });
        }

        // Orden
        if ($sort === 'new') {
            $query->latest('products.created_at');
        } else {
            $query->orderBy('products.id', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        // Título amigable para la vista genérica
        $title =
            $tag === 'sale' ? 'Ofertas' :
            ($q !== '' ? "Resultados para “{$q}”" : 'Resultados');

        // Usa tu vista de listado genérico
        return view('landing.catalog.index', [
            'title' => $title,
            'products' => $products,
            'filters' => compact('q', 'sort', 'tag'),
            // Para que la blade no truene si intenta usar $category:
            'category' => null,
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
                'slug' => $p->slug,
                'name' => $p->name,
                'thumb' => $img ?: null,
            ];
        });

        return response()->json(['data' => $data]);
    }
}
