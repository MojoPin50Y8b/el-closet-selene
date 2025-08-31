<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        // Trae los ítems del carrito igual que en CartController@index
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

        // 👇 usa tu vista en landing/
        return view('landing.checkout.index', compact('items', 'total'));
    }

    public function applyCoupon(Request $request)
    {
        // validar cupón (stub)
        return back()->with('status', 'Cupón aplicado (demo)');
    }

    public function place(Request $request)
    {
        // crear pedido (stub)
        return redirect()->route('shop.home')->with('status', 'Pedido confirmado (demo)');
    }

    // Reutilizamos helper mínimo para obtener/crear carrito activo
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
}
