<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Liste des produits avec leur stock courant.
     */
    public function index(Request $request): View
    {
        Gate::authorize('products.view');

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();

        $query = Product::query()
            ->with(['stock', 'category', 'unit'])
            ->search($search);

        if ($status === 'low') {
            $query->lowStock();
        } elseif ($status === 'out') {
            $query->outOfStock();
        } elseif ($status === 'ok') {
            $query->inStock();
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();

        $stats = [
            'products' => Product::query()->count(),
            'low_stock' => Product::query()->where('is_active', true)->lowStock()->count(),
            'out_of_stock' => Product::query()->where('is_active', true)->outOfStock()->count(),
        ];

        return view('stocks.index', compact('products', 'stats', 'search', 'status'));
    }

    /**
     * Formulaire d'ajustement du stock d'un produit.
     */
    public function adjustForm(Product $product): View
    {
        Gate::authorize('stock.adjust');

        $product->load(['stock', 'category', 'unit']);

        return view('stocks.adjust', compact('product'));
    }

    /**
     * Enregistre un ajustement de stock.
     */
    public function adjust(StockAdjustmentRequest $request, Product $product, StockService $stockService): RedirectResponse
    {
        $movement = $stockService->adjust(
            $product,
            (float) $request->validated('quantity'),
            $request->user(),
            (string) $request->validated('reason'),
        );

        if ($movement === null) {
            return redirect()
                ->route('stocks.index')
                ->with('success', 'Aucun changement : la quantité saisie est identique au stock actuel.');
        }

        return redirect()
            ->route('stocks.movements', $product)
            ->with('success', 'Stock ajusté avec succès.');
    }

    /**
     * Historique des mouvements de stock d'un produit.
     */
    public function movements(Product $product): View
    {
        Gate::authorize('products.view');

        $product->load(['stock', 'category', 'unit']);

        $movements = $product->stockMovements()
            ->with('user')
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('stocks.movements', compact('product', 'movements'));
    }
}
