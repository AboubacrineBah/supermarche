<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tenant_id',
    'name',
    'phone',
    'whatsapp',
    'email',
    'address',
    'credit_limit',
    'credit_balance',
    'is_active',
])]
class Customer extends Model
{
    use BelongsToTenant, HasFactory;

    protected function casts(): array
    {
        return [
            'credit_limit' => 'integer',
            'credit_balance' => 'integer',
            'loyalty_points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function creditPayments(): HasMany
    {
        return $this->hasMany(CreditPayment::class);
    }

    public function hasCreditAvailable(int $amount): bool
    {
        return ($this->credit_balance + $amount) <= $this->credit_limit;
    }
}
