<?php

namespace App\Http\Requests;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur peut créer ou modifier un fournisseur.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user instanceof User) {
            return false;
        }

        $supplier = $this->route('supplier');

        if ($supplier instanceof Supplier) {
            return $user->can('purchases.manage');
        }

        return $user->can('purchases.manage');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $supplier = $this->route('supplier');
        $supplier = $supplier instanceof Supplier ? $supplier : null;
        $tenantId = $supplier !== null ? $supplier->tenant_id : $this->targetTenantId();

        $user = $this->user();
        $isSuperAdmin = $user instanceof User && $user->hasRole('super-admin');

        $tenantRules = ['nullable', 'integer'];

        if ($supplier === null && $isSuperAdmin) {
            $tenantRules = [
                'required',
                'integer',
                Rule::exists('tenants', 'id')->where('is_active', true),
            ];
        }

        return [
            'tenant_id' => $tenantRules,
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('suppliers', 'name')
                    ->where('tenant_id', $tenantId)
                    ->ignore($supplier?->id),
            ],
            'nif' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'payment_terms' => [
                'required',
                Rule::in(['cash', '15_days', '30_days', '45_days', '60_days']),
            ],
            'debt_limit' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
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
