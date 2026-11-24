<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * Retourne la ligne de stock du produit, en la créant si nécessaire.
     */
    public function stockFor(Product $product): Stock
    {
        return Stock::query()->firstOrCreate(
            ['product_id' => $product->id],
            ['tenant_id' => $product->tenant_id, 'quantity' => 0],
        );
    }

    /**
     * Enregistre un mouvement de stock à partir d'une quantité positive.
     */
    public function record(
        Product $product,
        StockMovementType $type,
        float $quantity,
        ?User $user = null,
        ?string $reason = null,
        ?int $unitCost = null,
        ?CarbonInterface $occurredAt = null,
    ): StockMovement {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'La quantité du mouvement doit être supérieure à zéro.',
            ]);
        }

        $signedQuantity = $type->isInbound() ? $quantity : -$quantity;

        return DB::transaction(function () use ($product, $type, $signedQuantity, $user, $reason, $unitCost, $occurredAt): StockMovement {
            $stock = $this->lockedStock($product);
            $this->ensureAvailable($stock, $signedQuantity);

            return $this->persist($product, $stock, $type, $signedQuantity, $user, $reason, $unitCost, $occurredAt);
        });
    }

    /**
     * Ajuste le stock à une quantité réelle constatée.
     */
    public function adjust(
        Product $product,
        float $targetQuantity,
        ?User $user = null,
        ?string $reason = null,
        ?CarbonInterface $occurredAt = null,
    ): ?StockMovement {
        if ($targetQuantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'La quantité en stock ne peut pas être négative.',
            ]);
        }

        return DB::transaction(function () use ($product, $targetQuantity, $user, $reason, $occurredAt): ?StockMovement {
            $stock = $this->lockedStock($product);
            $delta = round($targetQuantity - (float) $stock->quantity, 2);

            if (abs($delta) < 0.005) {
                return null;
            }

            return $this->persist($product, $stock, StockMovementType::Adjustment, $delta, $user, $reason, null, $occurredAt);
        });
    }

    private function lockedStock(Product $product): Stock
    {
        $stock = Stock::query()
            ->where('product_id', $product->id)
            ->lockForUpdate()
            ->first();

        if ($stock !== null) {
            return $stock;
        }

        $stock = Stock::query()->create([
            'tenant_id' => $product->tenant_id,
            'product_id' => $product->id,
            'quantity' => 0,
        ]);

        return Stock::query()->whereKey($stock->getKey())->lockForUpdate()->firstOrFail();
    }

    private function ensureAvailable(Stock $stock, float $signedQuantity): void
    {
        if ((float) $stock->quantity + $signedQuantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Stock insuffisant : quantité disponible '.$this->formatQuantity((float) $stock->quantity).'.',
            ]);
        }
    }

    private function persist(
        Product $product,
        Stock $stock,
        StockMovementType $type,
        float $signedQuantity,
        ?User $user,
        ?string $reason,
        ?int $unitCost,
        ?CarbonInterface $occurredAt,
    ): StockMovement {
        $stock->update([
            'quantity' => round((float) $stock->quantity + $signedQuantity, 2),
        ]);

        return StockMovement::query()->create([
            'tenant_id' => $product->tenant_id,
            'product_id' => $product->id,
            'user_id' => $user?->id,
            'type' => $type,
            'quantity' => $signedQuantity,
            'unit_cost' => $unitCost,
            'reason' => $reason,
            'occurred_at' => $occurredAt ?? now(),
        ]);
    }

    private function formatQuantity(float $quantity): string
    {
        return number_format($quantity, 2, ',', ' ');
    }
}
