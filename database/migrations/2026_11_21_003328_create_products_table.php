<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku', 50);
            $table->string('barcode', 50)->nullable();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();

            // Montants en francs CFA : entiers, sans décimale.
            $table->decimal('cost_price', 15, 0)->default(0);
            $table->decimal('retail_price', 15, 0)->default(0);
            $table->decimal('wholesale_price', 15, 0)->nullable();
            $table->decimal('wholesale_min_qty', 12, 2)->nullable();

            $table->decimal('stock_min', 12, 2)->default(0);
            $table->decimal('stock_max', 12, 2)->nullable();
            $table->boolean('track_lots')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'sku']);
            $table->unique(['tenant_id', 'barcode']);
            $table->index(['tenant_id', 'category_id']);
            $table->index(['tenant_id', 'is_active', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
