<?php

namespace App\Http\Requests;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PurchaseOrderRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur peut créer ou modifier un bon de commande.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user instanceof User) {
            return false;
        }

        $purchaseOrder = $this->route('purchase_order');

        if ($purchaseOrder instanceof PurchaseOrder) {
            return $user->can('purchases.manage');
        }

        return $user->can('purchases.manage');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $purchaseOrder = $this->route('purchase_order');
        $purchaseOrder = $purchaseOrder instanceof PurchaseOrder ? $purchaseOrder : null;
        $tenantId = $purchaseOrder !== null ? $purchaseOrder->tenant_id : $this->targetTenantId();

        $user = $this->user();
        $isSuperAdmin = $user instanceof User && $user->hasRole('super-admin');

        $tenantRules = ['nullable', 'integer'];

        if ($purchaseOrder === null && $isSuperAdmin) {
            $tenantRules = [
                'required',
                'integer',
                Rule::exists('tenants', 'id')->where('is_active', true),
            ];
        }

        return [
            'tenant_id' => $tenantRules,
            'reference' => [
                'required',
                'string',
                'max:30',
                Rule::unique('purchase_orders', 'reference')
                    ->where('tenant_id', $tenantId)
                    ->ignore($purchaseOrder?->id),
            ],
            'supplier_id' => [
                'required',
                'integer',
                Rule::exists('suppliers', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],
            'ordered_at' => ['required', 'date'],
            'expected_at' => ['nullable', 'date', 'after_or_equal:ordered_at'],
            'status' => ['required', Rule::enum(PurchaseOrderStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function validatedItems(): array
    {
        $items = $this->input('items', []);

        if (! is_array($items) || count($items) === 0) {
            throw ValidationException::withMessages([
                'items' => 'Au moins une ligne d\'article est requise.',
            ]);
        }

        $validated = [];
        $tenantId = $this->targetTenantId();

        foreach (array_values($items) as $index => $item) {
            if (! is_array($item)) {
                throw ValidationException::withMessages([
                    'items' => 'Ligne d\'article invalide (ligne '.($index + 1).').',
                ]);
            }

            $productId = $item['product_id'] ?? null;
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitCost = (float) ($item['unit_cost'] ?? 0);

            $validator = validator()->make(
                ['product_id' => $productId],
                ['product_id' => [
                    'required',
                    'integer',
                    Rule::exists('products', 'id')->where('tenant_id', $tenantId),
                ]]
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if ($quantity <= 0 || $unitCost < 0) {
                throw ValidationException::withMessages([
                    'items' => 'Quantité et prix unitaire doivent être valides (ligne '.($index + 1).').',
                ]);
            }

            $validated[] = [
                'product_id' => (int) $productId,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'line_total' => (int) round($quantity * $unitCost, 0),
            ];
        }

        return $validated;
    }

    private function targetTenantId(): ?int
    {
        $user = $this->user();

        if (! $user instanceof User) {
            return null;
        }

        if ($user->hasRole('super-admin')) {
            return $this->filled('tenant_id') ? (int) $this->input('tenant_id') : null;
        }

        return $user->tenant_id;
    }
}
