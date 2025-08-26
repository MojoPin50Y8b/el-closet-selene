<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Página del carrito (si no tienes la Blade, este método no es crítico
     * para el flujo de “Añadir al carrito” y el contador).
     */
    public function index()
    {
        $cartId = $this->activeCartId();

        $items = DB::table('cart_items')
            ->select(
                'cart_items.id',
                'cart_items.qty',
                'cart_items.unit_price as price',
                'cart_items.product_id',
                'cart_items.variant_id',
                'products.name as product_name',
                'products.slug as product_slug'
            )
            ->join('products', 'products.id', '=', 'cart_items.product_id')
            ->leftJoin('product_variants', 'product_variants.id', '=', 'cart_items.variant_id')
            ->where('cart_items.cart_id', $cartId)
            ->get();

        $total = $items->reduce(fn ($c, $i) => $c + ($i->qty * (float) $i->price), 0.0);

        // Ajusta la vista si es necesario; no afecta al add/count
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
            'qty'        => ['required', 'integer', 'min:1'],
        ]);

        $cartId  = $this->activeCartId();
        $product = Product::findOrFail($data['product_id']);

        // Obtén precio unitario (sale_price > price) de la variante o del producto
        $variant = null;
        if (!empty($data['variant_id'])) {
            $variant = DB::table('product_variants')
                ->select('price', 'sale_price')
                ->where('id', $data['variant_id'])
                ->first();
        }

        $unitPrice = $variant->sale_price
            ?? $variant->price
            ?? $product->sale_price
            ?? $product->price
            ?? 0;

        // upsert simple (suma cantidad si ya existe la misma combinación)
        DB::transaction(function () use ($cartId, $data, $unitPrice) {
            $row = DB::table('cart_items')->where([
                'cart_id'    => $cartId,
                'product_id' => $data['product_id'],
                'variant_id' => $data['variant_id'] ?? null,
            ])->first();

            if ($row) {
                DB::table('cart_items')->where('id', $row->id)->update([
                    'qty'        => (int) $row->qty + (int) $data['qty'],
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('cart_items')->insert([
                    'cart_id'    => $cartId,
                    'product_id' => $data['product_id'],
                    'variant_id' => $data['variant_id'] ?? null,
                    'qty'        => (int) $data['qty'],
                    'unit_price' => $unitPrice,            // << clave para evitar el 1364
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json([
            'ok'    => true,
            'count' => $this->itemsCount($cartId),
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
            'ok'    => true,
            'count' => $this->itemsCount($cartId),
        ]);
    }

    /**
     * Mini-carrito (HTML parcial).
     */
    public function mini()
    {
        $cartId = $this->activeCartId();

        $items = DB::table('cart_items')
            ->select(
                'cart_items.id',
                'cart_items.qty',
                'cart_items.unit_price as price',
                'products.name as product_name',
                'products.slug as product_slug'
            )
            ->join('products', 'products.id', '=', 'cart_items.product_id')
            ->leftJoin('product_variants', 'product_variants.id', '=', 'cart_items.variant_id')
            ->where('cart_items.cart_id', $cartId)
            ->get();

        $total = $items->reduce(fn ($c, $i) => $c + ($i->qty * (float) $i->price), 0.0);

        return view('shop.partials.mini-cart', compact('items', 'total'));
    }

    /**
     * Contador del carrito (JSON). Lo usas en app.js para #cart-count.
     */
    public function count(): JsonResponse
    {
        return response()->json(['count' => $this->itemsCount($this->activeCartId())]);
    }

    // ======================= helpers privados =======================

    /** Obtiene (o crea) el carrito "active" para el usuario o la sesión. */
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
            $id = (int) DB::table('carts')->insertGetId([
                'user_id'    => auth()->id(),
                'session_id' => $sessionId,
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $id;
    }

    /** Suma de cantidades del carrito. */
    private function itemsCount(int $cartId): int
    {
        return (int) DB::table('cart_items')
            ->where('cart_id', $cartId)
            ->sum('qty');
    }
}
