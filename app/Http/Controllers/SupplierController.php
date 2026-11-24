<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Liste paginée des fournisseurs du magasin courant.
     */
    public function index(Request $request): View
    {
        Gate::authorize('purchases.manage');

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();

        $escapedSearch = str_replace(['%', '_'], ['\\%', '\\_'], $search);

        $suppliers = Supplier::query()
            ->when($search, fn ($query) => $query->where('name', 'like', '%'.$escapedSearch.'%'))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search', 'status'));
    }

    /**
     * Formulaire de création d'un fournisseur.
     */
    public function create(): View
    {
        Gate::authorize('purchases.manage');

        return view('suppliers.create');
    }

    /**
     * Enregistre un nouveau fournisseur.
     */
    public function store(SupplierRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tenant_id'] = $this->targetTenantId($request);

        $supplier = Supplier::create($data);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Fournisseur créé avec succès.');
    }

    /**
     * Fiche détaillée d'un fournisseur.
     */
    public function show(Supplier $supplier): View
    {
        Gate::authorize('purchases.manage');

        $supplier->load(['purchaseOrders' => fn ($q) => $q->latest()->take(10)]);

        $stats = [
            'orders_count' => $supplier->purchaseOrders()->count(),
            'orders_pending' => $supplier->purchaseOrders()->where('status', '!=', 'received')->count(),
            'debt' => 0, // TODO: calculer la dette si nécessaire
        ];

        return view('suppliers.show', compact('supplier', 'stats'));
    }

    /**
     * Formulaire de modification d'un fournisseur.
     */
    public function edit(Supplier $supplier): View
    {
        Gate::authorize('purchases.manage');

        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Met à jour un fournisseur existant.
     */
    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validated();
        unset($data['tenant_id']);
        $supplier->update($data);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Fournisseur mis à jour.');
    }

    /**
     * Supprime (désactive) un fournisseur.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        Gate::authorize('purchases.manage');

        if ($supplier->purchaseOrders()->exists()) {
            return redirect()
                ->route('suppliers.index')
                ->with('error', 'Ce fournisseur a des commandes : désactivez-le au lieu de le supprimer.');
        }

        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fournisseur supprimé.');
    }

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
