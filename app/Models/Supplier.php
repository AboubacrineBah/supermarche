<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tenant_id',
    'name',
    'nif',
    'phone',
    'whatsapp',
    'email',
    'address',
    'payment_terms',
    'debt_limit',
    'notes',
    'is_active',
])]
class Supplier extends Model
{
    /** @use HasFactory<SupplierFactory> */
    use BelongsToTenant, HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'debt_limit' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function paymentTermsLabel(): string
    {
        return match ($this->payment_terms) {
            'cash' => 'Comptant',
            '15_days' => '15 jours',
            '30_days' => '30 jours',
            '45_days' => '45 jours',
            '60_days' => '60 jours',
            default => $this->payment_terms,
        };
    }
}
