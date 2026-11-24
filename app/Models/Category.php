<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

#[Fillable(['tenant_id', 'parent_id', 'name', 'slug', 'position'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use BelongsToTenant, HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Category $category): void {
            if (blank($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            if ($category->parent_id !== null) {
                $parent = static::query()->withoutGlobalScopes()->find($category->parent_id);

                if ($parent === null || $parent->tenant_id !== $category->tenant_id) {
                    throw ValidationException::withMessages([
                        'parent_id' => 'La catégorie parente doit appartenir au même magasin.',
                    ]);
                }
            }
        });

        static::updating(function (Category $category): void {
            if ($category->isDirty('parent_id') && $category->parent_id !== null) {
                $parent = static::query()->withoutGlobalScopes()->find($category->parent_id);

                if ($parent === null || $parent->tenant_id !== $category->tenant_id) {
                    throw ValidationException::withMessages([
                        'parent_id' => 'La catégorie parente doit appartenir au même magasin.',
                    ]);
                }
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
