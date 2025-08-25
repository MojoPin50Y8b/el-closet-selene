<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = session('cart.items', []);
        $total = array_sum(array_map(fn($i) => $i['qty'] * $i['price'], $items));
        return view('shop.cart', compact('items', 'total'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required',  // usa variant_id o product_id según tu UI
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'url' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        $items = session('cart.items', []);
        $key = (string) $validated['id'];
        if (!isset($items[$key]))
            $items[$key] = $validated;
        else
            $items[$key]['qty'] += $validated['qty'];

        session(['cart.items' => $items, 'cart.count' => array_sum(array_column($items, 'qty'))]);
        return response()->json(['ok' => true, 'count' => session('cart.count', 0)]);
    }

    public function remove(Request $request)
    {
        $id = (string) $request->input('id');
        $items = session('cart.items', []);
        unset($items[$id]);
        session(['cart.items' => $items, 'cart.count' => array_sum(array_column($items, 'qty'))]);
        return response()->json(['ok' => true, 'count' => session('cart.count', 0)]);
    }

    public function mini()
    {
        $items = session('cart.items', []);
        $total = array_sum(array_map(fn($i) => $i['qty'] * $i['price'], $items));
        return view('shop.partials.mini-cart', compact('items', 'total'));
    }
}
