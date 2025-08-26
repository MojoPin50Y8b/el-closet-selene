<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    /**
     * Carrito (página). Si aún no tienes la blade, puedes dejarlo así;
     * el flujo de "añadir" y contador funciona con JSON.
     */
    public function index()
    {
        $cartId = $this->activeCartId();

        $items = DB::table('cart_items')
            ->select(
                'cart_items.id',
                'cart_items.qty',
                'cart_items.product_id',
                'cart_items.variant_id',
                'products.name as product_name',
                'products.slug as product_slug',
                DB::raw('COALESCE(product_variants.sale_price, product_variants.price, products.sale_price, products.price) as price')
            )
            ->join('products', 'products.id', '=', 'cart_items.product_id')
            ->leftJoin('product_variants', 'product_variants.id', '=', 'cart_items.variant_id')
            ->where('cart_items.cart_id', $cartId)
            ->get();

        $total = $items->reduce(fn($c, $i) => $c + ($i->qty * (float) $i->price), 0.0);

        return view('shop.cart', compact('items', 'total'));
    }

    /**
     * Añadir al carrito (JSON).
     * Espera: { product_id, variant_id|null, qty }
     */
    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $cartId = $this->activeCartId();

        // upsert simple (suma cantidad si ya existe la misma combinación)
        $row = DB::table('cart_items')->where([
            'cart_id' => $cartId,
            'product_id' => $data['product_id'],
            'variant_id' => $data['variant_id'] ?? null,
        ])->first();

        if ($row) {
            DB::table('cart_items')->where('id', $row->id)->update([
                'qty' => (int) $row->qty + (int) $data['qty'],
                'updated_at' => now(),
            ]);
        } else {
            DB::table('cart_items')->insert([
                'cart_id' => $cartId,
                'product_id' => $data['product_id'],
                'variant_id' => $data['variant_id'] ?? null,
                'qty' => (int) $data['qty'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $count = $this->itemsCount($cartId);

        return response()->json([
            'ok' => true,
            'count' => (int) $count,
        ]);
    }

    /**
     * Quitar una línea del carrito (JSON).
     * Espera: { id } donde "id" es el ID de la fila en cart_items.
     */
    public function remove(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $cartId = $this->activeCartId();

        DB::table('cart_items')
            ->where('cart_id', $cartId)
            ->where('id', $payload['id'])
            ->delete();

        return response()->json([
            'ok' => true,
            'count' => (int) $this->itemsCount($cartId),
        ]);
    }

    /**
     * Mini-carrito (HTML parcial).
     * Asegúrate de tener la vista: resources/views/shop/partials/mini-cart.blade.php
     * (o ajusta el path si usas otro namespace de vistas).
     */
    public function mini()
    {
        $cartId = $this->activeCartId();

        $items = DB::table('cart_items')
            ->select(
                'cart_items.id',
                'cart_items.qty',
                'products.name as product_name',
                'products.slug as product_slug',
                DB::raw('COALESCE(product_variants.sale_price, product_variants.price, products.sale_price, products.price) as price')
            )
            ->join('products', 'products.id', '=', 'cart_items.product_id')
            ->leftJoin('product_variants', 'product_variants.id', '=', 'cart_items.variant_id')
            ->where('cart_items.cart_id', $cartId)
            ->get();

        $total = $items->reduce(fn($c, $i) => $c + ($i->qty * (float) $i->price), 0.0);

        return view('shop.partials.mini-cart', compact('items', 'total'));
    }

    /**
     * Contador del carrito (JSON). Lo usas en app.js para #cart-count.
     */
    public function count(): JsonResponse
    {
        $count = $this->itemsCount($this->activeCartId());

        return response()->json(['count' => (int) $count]);
    }

    // ======================= helpers privados =======================

    /**
     * Obtiene (o crea) el carrito "active" para el usuario o la sesión.
     */
    private function activeCartId(): int
    {
        $sessionId = session()->getId();

        $query = DB::table('carts')->where('status', 'active');

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $query->whereNull('user_id')->where('session_id', $sessionId);
        }

        $id = (int) $query->value('id');

        if (!$id) {
            $id = DB::table('carts')->insertGetId([
                'user_id' => auth()->id(),      // null si guest
                'session_id' => $sessionId,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $id;
    }

    /**
     * Suma de cantidades del carrito.
     */
    private function itemsCount(int $cartId): int
    {
        return (int) DB::table('cart_items')
            ->where('cart_id', $cartId)
            ->sum('qty');
    }
}
