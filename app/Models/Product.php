<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable([
    'tenant_id',
    'category_id',
    'unit_id',
    'sku',
    'barcode',
    'name',
    'description',
    'image_path',
    'cost_price',
    'retail_price',
    'wholesale_price',
    'wholesale_min_qty',
    'stock_min',
    'stock_max',
    'track_lots',
    'is_active',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use BelongsToTenant, HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost_price' => 'integer',
            'retail_price' => 'integer',
            'wholesale_price' => 'integer',
            'wholesale_min_qty' => 'float',
            'stock_min' => 'float',
            'stock_max' => 'float',
            'track_lots' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (blank($product->sku)) {
                $product->sku = self::generateSku();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    #[Scope]
    protected function lowStock(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereHas('stock', fn (Builder $query) => $query->whereColumn('stocks.quantity', '<=', 'products.stock_min'))
                ->orWhereDoesntHave('stock');
        });
    }

    #[Scope]
    protected function outOfStock(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->whereHas('stock', fn (Builder $query) => $query->where('stocks.quantity', '<=', 0))
                ->orWhereDoesntHave('stock');
        });
    }

    #[Scope]
    protected function inStock(Builder $query): Builder
    {
        return $query->whereHas('stock', fn (Builder $query) => $query->whereColumn('stocks.quantity', '>', 'products.stock_min'));
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], trim($term)).'%';

        return $query->where(function (Builder $query) use ($like): void {
            $query->where('name', 'like', $like)
                ->orWhere('sku', 'like', $like)
                ->orWhere('barcode', 'like', $like);
        });
    }

    private static function generateSku(): string
    {
        do {
            $sku = 'P-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (static::query()->where('sku', $sku)->exists());

        return $sku;
    }
}
