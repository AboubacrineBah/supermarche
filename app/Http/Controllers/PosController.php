<?php

namespace App\Http\Controllers;

use App\Enums\StockMovementType;
use App\Models\CashSession;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Tenant;
use App\Models\User;
use App\Services\StockService;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('pos.sell');

        $session = CashSession::query()
            ->where('status', 'open')
            ->where('user_id', $request->user()->id)
            ->latest('opened_at')
            ->first();

        $products = Product::query()
            ->where('is_active', true)
            ->with(['stock', 'unit'])
            ->orderBy('name')
            ->get();

        $customers = Customer::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pos.index', compact('session', 'products', 'customers'));
    }

    public function openForm(): View
    {
        Gate::authorize('cash.open');

        $tenants = auth()->user()?->hasRole('super-admin')
            ? Tenant::query()->orderBy('name')->get()
            : collect();

        return view('pos.open', compact('tenants'));
    }

    public function open(Request $request): RedirectResponse
    {
        Gate::authorize('cash.open');

        $user = $request->user();
        $isSuperAdmin = $user instanceof User && $user->hasRole('super-admin');

        $rules = [
            'opening_amount' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        if ($isSuperAdmin) {
            $rules['tenant_id'] = ['required', 'integer', Rule::exists('tenants', 'id')->where('is_active', true)];
        }

        $data = $request->validate($rules);

        $tenantId = $isSuperAdmin
            ? (int) $data['tenant_id']
            : $user->tenant_id;

        $existing = CashSession::query()
            ->where('status', 'open')
            ->where('user_id', $user->id)
            ->exists();

        if ($existing) {
            return redirect()->route('pos.index')->with('error', 'Une caisse est déjà ouverte.');
        }

        $session = CashSession::create([
            'tenant_id' => $tenantId,
            'user_id' => $request->user()->id,
            'opened_at' => now(),
            'opening_amount' => (int) $data['opening_amount'],
            'status' => 'open',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('pos.index')->with('success', 'Caisse ouverte.');
    }

    public function closeForm(CashSession $cashSession): View
    {
        Gate::authorize('cash.close');

        $expected = (int) $cashSession->sales()->sum('total') + (int) $cashSession->opening_amount;

        return view('pos.close', compact('cashSession', 'expected'));
    }

    public function close(Request $request, CashSession $cashSession, StockService $stockService): RedirectResponse
    {
        Gate::authorize('cash.close');

        if (! $cashSession->isOpen()) {
            return redirect()->route('pos.index')->with('error', 'Cette caisse est déjà fermée.');
        }

        $data = $request->validate([
            'closing_amount' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $expected = (int) $cashSession->sales()->sum('total') + (int) $cashSession->opening_amount;

        $cashSession->update([
            'closed_at' => now(),
            'closing_amount' => (int) $data['closing_amount'],
            'expected_amount' => $expected,
            'difference' => (int) $data['closing_amount'] - $expected,
            'status' => 'closed',
            'notes' => $data['notes'] ?? $cashSession->notes,
        ]);

        return redirect()->route('pos.index')->with('success', 'Caisse fermée. Écart : '.format_fcfa($cashSession->fresh()->difference));
    }

    public function storeSale(Request $request, StockService $stockService): RedirectResponse
    {
        Gate::authorize('pos.sell');

        $rawItems = $request->input('items', []);
        $filtered = [];

        foreach (is_array($rawItems) ? $rawItems : [] as $item) {
            if (is_array($item) && isset($item['quantity']) && (float) $item['quantity'] > 0) {
                $filtered[] = [
                    'product_id' => (int) ($item['product_id'] ?? 0),
                    'quantity' => $item['quantity'],
                ];
            }
        }

        $request->merge(['items' => $filtered]);

        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,orange_money,wave,moov_money,card,credit'],
            'paid_amount' => ['required', 'integer', 'min:0'],
            'discount' => ['nullable', 'integer', 'min:0'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
        ]);

        $session = CashSession::query()
            ->where('status', 'open')
            ->where('user_id', $request->user()->id)
            ->latest('opened_at')
            ->first();

        if ($session === null) {
            return redirect()->route('pos.index')->with('error', 'Aucune caisse ouverte. Ouvrez une caisse avant de vendre.');
        }

        $discount = (int) ($data['discount'] ?? 0);

        $sale = DB::transaction(function () use ($request, $data, $session, $discount, $stockService): Sale {
            $subtotal = 0;
            $lineItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::query()->findOrFail($item['product_id']);
                $quantity = (float) $item['quantity'];
                $unitPrice = (int) $product->retail_price;
                $lineTotal = (int) round($quantity * $unitPrice);

                $stockService->record(
                    $product,
                    StockMovementType::Sale,
                    $quantity,
                    $request->user(),
                    'Vente',
                );

                $lineItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];

                $subtotal += $lineTotal;
            }

            $total = max(0, $subtotal - $discount);

            $customerId = isset($data['customer_id']) ? (int) $data['customer_id'] : null;
            $isCredit = $data['payment_method'] === 'credit';

            $sale = Sale::create([
                'cash_session_id' => $session->id,
                'user_id' => $request->user()->id,
                'customer_id' => $customerId,
                'reference' => Sale::generateReference(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => 0,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'payment_status' => $isCredit ? 'credit' : 'paid',
                'paid_amount' => (int) $data['paid_amount'],
                'change_amount' => max(0, (int) $data['paid_amount'] - $total),
                'sold_at' => now(),
            ]);

            if ($customerId !== null) {
                $customer = Customer::query()->find($customerId);
                if ($customer !== null) {
                    if ($isCredit) {
                        $customer->increment('credit_balance', $total - (int) $data['paid_amount']);
                    }
                    // Fidélité : 1 point pour 1000 FCFA dépensés
                    $customer->increment('loyalty_points', (int) floor($total / 1000));
                }
            }

            foreach ($lineItems as $line) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount' => 0,
                    'line_total' => $line['line_total'],
                ]);
            }

            return $sale;
        });

        return redirect()->route('pos.receipt', $sale)->with('success', 'Vente enregistrée.');
    }

    public function receipt(Sale $sale): View
    {
        Gate::authorize('pos.sell');

        $sale->load(['items.product', 'user', 'cashSession', 'customer']);

        return view('pos.receipt', compact('sale'));
    }

    public function receiptPdf(Sale $sale)
    {
        Gate::authorize('pos.sell');

        $sale->load(['items.product', 'user', 'cashSession', 'customer']);

        $dompdf = new Dompdf;
        $html = view('pos.receipt-thermal', compact('sale'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 226.77, 600], 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="ticket-'.$sale->reference.'.pdf"',
        ]);
    }
}
