<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur peut créer ou modifier le produit.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $product = $this->route('product');

        if (! $user instanceof User) {
            return false;
        }

        if ($product instanceof Product) {
            return $user->can('update', $product);
        }

        return $user->can('create', Product::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $product = $product instanceof Product ? $product : null;

        $tenantId = $product->tenant_id ?? $this->targetTenantId();

        $user = $this->user();
        $isSuperAdmin = $user instanceof User && $user->hasRole('super-admin');

        $tenantRules = ['nullable', 'integer'];

        if ($product === null && $isSuperAdmin) {
            $tenantRules = [
                'required',
                'integer',
                Rule::exists('tenants', 'id')->where('is_active', true),
            ];
        }

        return [
            'tenant_id' => $tenantRules,
            'name' => ['required', 'string', 'max:150'],
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'sku')
                    ->where('tenant_id', $tenantId)
                    ->ignore($product?->id),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'barcode')
                    ->where('tenant_id', $tenantId)
                    ->ignore($product?->id),
            ],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('tenant_id', $tenantId),
            ],
            'unit_id' => [
                'nullable',
                'integer',
                Rule::exists('units', 'id')->where('tenant_id', $tenantId),
            ],
            'description' => ['nullable', 'string'],
            'cost_price' => ['required', 'integer', 'min:0'],
            'retail_price' => ['required', 'integer', 'min:0'],
            'wholesale_price' => ['nullable', 'integer', 'min:0'],
            'wholesale_min_qty' => ['nullable', 'numeric', 'min:0'],
            'stock_min' => ['required', 'numeric', 'min:0'],
            'stock_max' => ['nullable', 'numeric', 'min:0', 'gte:stock_min'],
            'track_lots' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'track_lots' => $this->boolean('track_lots'),
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
