<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
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
                'products.slug as product_slug',
                DB::raw('(SELECT url FROM product_images WHERE product_images.product_id = cart_items.product_id LIMIT 1) AS image_url')
            )
            ->join('products', 'products.id', '=', 'cart_items.product_id')
            ->where('cart_items.cart_id', $cartId)
            ->get();

        $total = $items->reduce(fn($c, $i) => $c + ($i->qty * (float) $i->price), 0.0);

        // 👉 ahora apunta a tu vista en landing/
        return view('landing.cart.index', compact('items', 'total'));
    }

    /**
     * Espera JSON: { product_id, variant_id|null, qty }
     */
    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $cartId = $this->activeCartId();

        // Determinar precio unitario (COALESCE sale_price, price)
        if (!empty($data['variant_id'])) {
            $v = DB::table('product_variants')
                ->select('price', 'sale_price')
                ->where('id', $data['variant_id'])
                ->first();
            $unit = $v ? ($v->sale_price ?? $v->price ?? 0) : 0;
        } else {
            $v = DB::table('product_variants')
                ->select('price', 'sale_price')
                ->where('product_id', $data['product_id'])
                ->orderByRaw('COALESCE(sale_price, price) asc')
                ->first();

            if (!$v) {
                $p = DB::table('products')->select('price', 'sale_price')
                    ->where('id', $data['product_id'])->first();
            }
            $unit = $v ? ($v->sale_price ?? $v->price ?? 0) : (($p->sale_price ?? $p->price ?? 0) ?? 0);
        }

        // upsert por combinación (cart_id, product_id, variant_id)
        $row = DB::table('cart_items')->where([
            'cart_id' => $cartId,
            'product_id' => $data['product_id'],
            'variant_id' => $data['variant_id'] ?? null,
        ])->first();

        if ($row) {
            DB::table('cart_items')->where('id', $row->id)->update([
                'qty' => (int) $row->qty + (int) $data['qty'],
                'unit_price' => $row->unit_price ?: $unit,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('cart_items')->insert([
                'cart_id' => $cartId,
                'product_id' => $data['product_id'],
                'variant_id' => $data['variant_id'] ?? null,
                'qty' => (int) $data['qty'],
                'unit_price' => $unit,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'ok' => true,
            'count' => (int) $this->itemsCount($cartId),
        ]);
    }

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
            ->where('cart_items.cart_id', $cartId)
            ->get();

        $total = $items->reduce(fn($c, $i) => $c + ($i->qty * (float) $i->price), 0.0);

        // 👉 apunta a tu parcial real en landing/
        return view('landing.partials.mini-cart', compact('items', 'total'));
    }

    public function count(): JsonResponse
    {
        $count = $this->itemsCount($this->activeCartId());
        return response()->json(['count' => (int) $count]);
    }

    // -------------------- helpers --------------------

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
                'user_id' => auth()->id(),
                'session_id' => $sessionId,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $id;
    }

    private function itemsCount(int $cartId): int
    {
        return (int) DB::table('cart_items')->where('cart_id', $cartId)->sum('qty');
    }
}
