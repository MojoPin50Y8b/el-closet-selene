<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('shop.cart');
    }

    public function add(Request $request)
    {
        // lógica de agregar (stub)
        return response()->json(['ok' => true]);
    }

    public function remove(Request $request)
    {
        // lógica de eliminar (stub)
        return response()->json(['ok' => true]);
    }

    public function mini()
    {
        return view('shop.partials.mini-cart');
    }
}
