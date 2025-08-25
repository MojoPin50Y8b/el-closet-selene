<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('shop.checkout');
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
}
