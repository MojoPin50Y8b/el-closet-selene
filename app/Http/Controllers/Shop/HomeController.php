<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\{Banner, Category, Product};

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::query()
            ->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()); })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()); })
            ->orderBy('position')
            ->take(3)->get();

        $categories = Category::query()
            ->whereNull('parent_id')->where('is_active', 1)
            ->orderBy('sort_order')->take(4)->get();

        $newProducts = Product::with(['images', 'variants'])
            ->latest()->take(8)->get();

        $saleProducts = Product::with(['images', 'variants'])
            ->whereHas('variants', function ($q) {
                $q->whereNotNull('sale_price')
                    ->where(function ($qq) {
                        $qq->whereNull('sale_starts_at')->orWhere('sale_starts_at', '<=', now()); })
                    ->where(function ($qq) {
                        $qq->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>=', now()); });
            })
            ->take(8)->get();

        return view('shop::home', compact('banners','categories','newProducts','saleProducts'));

    }
}
