<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $monthStart = now()->startOfMonth();
        $soldThisMonth = Product::where('status', Product::SOLD)->where('sold_at', '>=', $monthStart);

        return view('admin.dashboard', [
            'counts' => [
                'available' => Product::where('status', Product::AVAILABLE)->count(),
                'reserved' => Product::where('status', Product::RESERVED)->count(),
                'sold' => Product::where('status', Product::SOLD)->count(),
            ],
            'stockValue' => (int) Product::listed()->sum('price_sell'),
            'stockCost' => (int) Product::listed()->sum('price_cost'),
            'soldMonthCount' => (clone $soldThisMonth)->count(),
            'soldMonthRevenue' => (int) (clone $soldThisMonth)->sum('price_sell'),
            'soldMonthProfit' => (int) (clone $soldThisMonth)->get(['price_sell', 'price_cost'])->sum(fn ($p) => $p->price_sell - $p->price_cost),
            'recent' => Product::with('brand')->latest()->take(6)->get(),
        ]);
    }
}
