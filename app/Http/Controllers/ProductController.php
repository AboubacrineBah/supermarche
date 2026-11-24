<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Liste paginée des produits du magasin courant.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Product::class);

        $user = $request->user();
        $isSuperAdmin = $user instanceof User && $user->hasRole('super-admin');

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();
        $categoryId = $request->integer('category_id');

        $products = Product::query()
            ->with(['category', 'unit', 'tenant'])
            ->search($search)
            ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();

        return view('products.index', compact(
            'products',
            'categories',
            'isSuperAdmin',
            'search',
            'status',
            'categoryId',
        ));
    }

    /**
     * Formulaire de création d'un produit.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', Product::class);

        return view('products.create', $this->formData($request));
    }

    /**
     * Enregistre un nouveau produit.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = $this->targetTenantId($request);

        $product = DB::transaction(function () use ($data): Product {
            $product = Product::create($data);
            $product->stock()->create([
                'tenant_id' => $product->tenant_id,
                'quantity' => 0,
            ]);

            return $product;
        });

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Fiche détaillée d'un produit.
     */
    public function show(Product $product): View
    {
        Gate::authorize('view', $product);

        $product->load(['category', 'unit', 'tenant']);

        return view('products.show', compact('product'));
    }

    /**
     * Formulaire de modification d'un produit.
     */
    public function edit(Request $request, Product $product): View
    {
        Gate::authorize('update', $product);

        return view('products.edit', $this->formData($request, $product));
    }

    /**
     * Met à jour un produit existant.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        unset($data['tenant_id']);

        $product->update($data);

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Produit mis à jour.');
    }

    /**
     * Supprime un produit.
     */
    public function destroy(Product $product): RedirectResponse
    {
        Gate::authorize('delete', $product);

        $stockQuantity = (float) ($product->stock()->value('quantity') ?? 0);

        if ($product->stockMovements()->exists() || $stockQuantity !== 0.0) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Ce produit possède un stock ou des mouvements : désactivez-le au lieu de le supprimer.');
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produit supprimé.');
    }

    /**
     * Données partagées par les formulaires de création et de modification.
     *
     * @return array<string, mixed>
     */
    private function formData(Request $request, ?Product $product = null): array
    {
        $user = $request->user();
        $isSuperAdmin = $user instanceof User && $user->hasRole('super-admin');

        $tenantId = $product?->tenant_id;

        if ($tenantId === null && ! $isSuperAdmin && $user instanceof User) {
            $tenantId = $user->tenant_id;
        }

        $categories = Category::query()
            ->with('tenant')
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->orderBy('name')
            ->get();

        $units = Unit::query()
            ->with('tenant')
            ->when($tenantId !== null, fn ($query) => $query->where('tenant_id', $tenantId))
            ->orderBy('name')
            ->get();

        $tenants = $isSuperAdmin
            ? Tenant::query()->orderBy('name')->get()
            : collect();

        return [
            'product' => $product,
            'categories' => $categories,
            'units' => $units,
            'tenants' => $tenants,
            'isSuperAdmin' => $isSuperAdmin,
        ];
    }

    /**
     * Magasin ciblé par la création : choisi par un super-admin, imposé sinon.
     */
    private function targetTenantId(Request $request): ?int
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return null;
        }

        if ($user->hasRole('super-admin')) {
            return $request->filled('tenant_id') ? $request->integer('tenant_id') : null;
        }

        return $user->tenant_id;
    }
}
