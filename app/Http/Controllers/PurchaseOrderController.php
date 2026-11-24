<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Enums\StockMovementType;
use App\Http\Requests\PurchaseOrderRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReceipt;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    /**
     * Liste paginée des bons de commande.
     */
    public function index(Request $request): View
    {
        Gate::authorize('purchases.manage');

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();
        $supplierId = $request->integer('supplier_id');
        $dateFrom = $request->date('date_from');
        $dateTo = $request->date('date_to');

        $escapedSearch = str_replace(['%', '_'], ['\\%', '\\_'], $search);

        $purchaseOrders = PurchaseOrder::query()
            ->with(['supplier', 'user'])
            ->when($search, fn ($query) => $query->where('reference', 'like', '%'.$escapedSearch.'%'))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($supplierId > 0, fn ($query) => $query->where('supplier_id', $supplierId))
            ->when($dateFrom, fn ($query) => $query->whereDate('ordered_at', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('ordered_at', '<=', $dateTo))
            ->latest('ordered_at')
            ->paginate(20)
            ->withQueryString();

        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('purchase-orders.index', compact(
            'purchaseOrders',
            'suppliers',
            'search',
            'status',
            'supplierId',
            'dateFrom',
            'dateTo',
        ));
    }

    /**
     * Formulaire de création d'un bon de commande.
     */
    public function create(): View
    {
        Gate::authorize('purchases.manage');

        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->where('is_active', true)
            ->with('unit')
            ->orderBy('name')
            ->get();

        $tenants = auth()->user()?->hasRole('super-admin')
            ? Tenant::query()->orderBy('name')->get()
            : collect();

        return view('purchase-orders.create', compact('suppliers', 'products', 'tenants'));
    }

    /**
     * Enregistre un nouveau bon de commande.
     */
    public function store(PurchaseOrderRequest $request): RedirectResponse
    {
        Gate::authorize('purchases.manage');

        $data = $request->validated();
        $data['tenant_id'] = $this->targetTenantId($request);
        $data['user_id'] = $request->user()->id;
        $items = $request->validatedItems();

        $purchaseOrder = DB::transaction(function () use ($data, $items): PurchaseOrder {
            $data['total'] = array_sum(array_column($items, 'line_total'));

            $purchaseOrder = PurchaseOrder::create($data);

            foreach ($items as $item) {
                $item['purchase_order_id'] = $purchaseOrder->id;
                $item['received_quantity'] = 0;
                PurchaseOrderItem::create($item);
            }

            return $purchaseOrder;
        });

        return redirect()
            ->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Bon de commande créé avec succès.');
    }

    /**
     * Affiche un bon de commande.
     */
    public function show(PurchaseOrder $purchaseOrder): View
    {
        Gate::authorize('purchases.manage');

        $purchaseOrder->load(['supplier', 'user', 'items.product.unit']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Formulaire de modification d'un bon de commande (brouillon uniquement).
     */
    public function edit(PurchaseOrder $purchaseOrder): \Illuminate\Contracts\View\View|RedirectResponse
    {
        Gate::authorize('purchases.manage');

        $tenants = auth()->user()?->hasRole('super-admin')
            ? Tenant::query()->orderBy('name')->get()
            : collect();

        if ($purchaseOrder->status !== PurchaseOrderStatus::Draft) {
            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Seuls les brouillons peuvent être modifiés.');
        }

        $purchaseOrder->load(['items.product']);

        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->where('is_active', true)
            ->with('unit')
            ->orderBy('name')
            ->get();

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products', 'tenants'));
    }

    /**
     * Met à jour un bon de commande (brouillon uniquement).
     */
    public function update(PurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        Gate::authorize('purchases.manage');

        if ($purchaseOrder->status !== PurchaseOrderStatus::Draft) {
            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Seuls les brouillons peuvent être modifiés.');
        }

        $data = $request->validated();
        $items = $request->validatedItems();
        unset($data['tenant_id']);

        DB::transaction(function () use ($purchaseOrder, $data, $items): void {
            $data['total'] = array_sum(array_column($items, 'line_total'));
            $purchaseOrder->update($data);

            // Supprimer les anciennes lignes et recréer
            $purchaseOrder->items()->delete();

            foreach ($items as $item) {
                $item['purchase_order_id'] = $purchaseOrder->id;
                $item['received_quantity'] = 0;
                PurchaseOrderItem::create($item);
            }
        });

        return redirect()
            ->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Bon de commande mis à jour.');
    }

    /**
     * Supprime un bon de commande (brouillon uniquement).
     */
    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        Gate::authorize('purchases.manage');

        if ($purchaseOrder->status !== PurchaseOrderStatus::Draft) {
            return redirect()
                ->route('purchase-orders.index')
                ->with('error', 'Seuls les brouillons peuvent être supprimés.');
        }

        $purchaseOrder->delete();

        return redirect()
            ->route('purchase-orders.index')
            ->with('success', 'Bon de commande supprimé.');
    }

    /**
     * Ouvre la page de réception pour un bon de commande.
     */
    public function receive(PurchaseOrder $purchaseOrder): View
    {
        Gate::authorize('purchases.manage');

        $purchaseOrder->load(['supplier', 'items.product.unit']);

        return view('purchase-orders.receive', compact('purchaseOrder'));
    }

    /**
     * Enregistre une réception (stock initial ou partiel).
     */
    public function storeReceipt(Request $request, PurchaseOrder $purchaseOrder, StockService $stockService): RedirectResponse
    {
        Gate::authorize('purchases.manage');

        $request->validate([
            'received_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_order_item_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        $items = $request->input('items', []);

        DB::transaction(function () use ($request, $purchaseOrder, $items, $stockService): void {
            // Créer la réception
            $receipt = PurchaseReceipt::create([
                'tenant_id' => $purchaseOrder->tenant_id,
                'purchase_order_id' => $purchaseOrder->id,
                'user_id' => $request->user()->id,
                'reference' => 'REC-'.$purchaseOrder->reference.'-'.now()->format('YmdHis'),
                'received_at' => $request->date('received_at'),
                'notes' => $request->input('notes'),
            ]);

            foreach ($items as $itemData) {
                $purchaseOrderItem = PurchaseOrderItem::query()
                    ->where('purchase_order_id', $purchaseOrder->id)
                    ->findOrFail($itemData['purchase_order_item_id']);

                $quantity = (float) $itemData['quantity'];
                $newReceived = round((float) $purchaseOrderItem->received_quantity + $quantity, 2);

                if ($newReceived > (float) $purchaseOrderItem->quantity) {
                    throw ValidationException::withMessages([
                        'items' => 'Quantité reçue supérieure à la quantité commandée (ligne '.(Product::find($purchaseOrderItem->product_id) !== null ? Product::find($purchaseOrderItem->product_id)->name : '—').').',
                    ]);
                }

                // Créer la ligne de réception
                $receipt->items()->create([
                    'tenant_id' => $purchaseOrder->tenant_id,
                    'purchase_order_item_id' => $purchaseOrderItem->id,
                    'product_id' => $purchaseOrderItem->product_id,
                    'quantity' => $quantity,
                    'unit_cost' => $purchaseOrderItem->unit_cost,
                ]);

                // Mettre à jour la quantité reçue sur la ligne du bon de commande
                $purchaseOrderItem->update(['received_quantity' => $newReceived]);

                // Incrémenter le stock du produit réceptionné
                $product = Product::findOrFail($purchaseOrderItem->product_id);

                $stockService->record(
                    $product,
                    StockMovementType::Purchase,
                    $quantity,
                    $request->user(),
                    'Réception '.$receipt->reference,
                    (int) $purchaseOrderItem->unit_cost,
                );
            }

            // Mettre à jour le statut du bon de commande
            $allReceived = $purchaseOrder->items()
                ->whereColumn('received_quantity', '<', 'quantity')
                ->doesntExist();

            if ($allReceived) {
                $purchaseOrder->update(['status' => PurchaseOrderStatus::Received]);
            } elseif ($purchaseOrder->status === PurchaseOrderStatus::Draft || $purchaseOrder->status === PurchaseOrderStatus::Sent) {
                $purchaseOrder->update(['status' => PurchaseOrderStatus::PartiallyReceived]);
            }
        });

        return redirect()
            ->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Réception enregistrée avec succès.');
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
