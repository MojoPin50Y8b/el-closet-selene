<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('shop.home'); // vista simple por ahora
    }
}
