<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Affiche le tableau de bord du magasin courant.
     */
    public function index(): View
    {
        $stats = [
            'products' => Product::query()->count(),
            'active_products' => Product::query()->where('is_active', true)->count(),
            'categories' => Category::query()->count(),
            'units' => Unit::query()->count(),
            'low_stock' => Product::query()->where('is_active', true)->lowStock()->count(),
            'out_of_stock' => Product::query()->where('is_active', true)->outOfStock()->count(),
        ];

        $recentProducts = Product::query()
            ->with(['category', 'stock'])
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact('stats', 'recentProducts'));
    }
}
