<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\{Banner, Category, Product};
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Banners activos (hasta 3)
        $banners = Banner::query()
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderBy('position')
            ->limit(3)
            ->get();

        // 4 categorías raíz activas
        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        // Novedades (con relaciones necesarias para la card)
        $newProducts = Product::with([
            'images' => fn($q) => $q->orderBy('sort_order'),
            'variants' => fn($q) => $q->select(
                'id',
                'product_id',
                'price',
                'sale_price',
                'sale_starts_at',
                'sale_ends_at'
            ),
        ])
            ->latest()
            ->limit(8)
            ->get();

        // En oferta (hay al menos una variante con sale_price vigente)
        $saleProducts = Product::with([
            'images' => fn($q) => $q->orderBy('sort_order'),
            'variants' => fn($q) => $q->select(
                'id',
                'product_id',
                'price',
                'sale_price',
                'sale_starts_at',
                'sale_ends_at'
            ),
        ])
            ->whereHas('variants', function ($q) {
                $q->whereNotNull('sale_price')
                    ->where(function ($qq) {
                        $qq->whereNull('sale_starts_at')
                            ->orWhere('sale_starts_at', '<=', now());
                    })
                    ->where(function ($qq) {
                        $qq->whereNull('sale_ends_at')
                            ->orWhere('sale_ends_at', '>=', now());
                    });
            })
            ->limit(8)
            ->get();

        // OJO: estamos usando el alias de vistas "shop::" que mapea a resources/views/landing
        return view('shop::home', compact('banners', 'categories', 'newProducts', 'saleProducts'));
    }
}
