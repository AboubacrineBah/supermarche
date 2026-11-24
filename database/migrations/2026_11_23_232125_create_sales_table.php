<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cash_session_id')->nullable()->constrained('cash_sessions')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable();
            $table->string('reference', 40)->unique();
            $table->integer('subtotal')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('tax')->default(0);
            $table->integer('total')->default(0);
            $table->string('payment_method', 30)->default('cash');
            $table->string('payment_status', 20)->default('paid');
            $table->integer('paid_amount')->default(0);
            $table->integer('change_amount')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('sold_at');
            $table->timestamps();

            $table->index(['tenant_id', 'sold_at']);
            $table->index(['tenant_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
