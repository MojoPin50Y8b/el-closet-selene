<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);
        NewsletterSubscriber::firstOrCreate(['email' => $data['email']]);
        return back()->with('status', '¡Gracias por suscribirte!');
    }
}
