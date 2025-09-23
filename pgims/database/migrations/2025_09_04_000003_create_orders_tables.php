<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'orders', callback: function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'customer_id')->constrained()->onDelete(action: 'cascade');
            $table->enum(column: 'status', allowed: ['pending', 'processing', 'completed', 'cancelled'])->default(value: 'pending');
            $table->decimal(column: 'total_amount', total: 12, places: 2)->default(value: 0);
            $table->text(column: 'notes')->nullable();
            $table->timestamps();
        });

        Schema::create(table: 'order_items', callback: function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'order_id')->constrained()->cascadeOnDelete();
            $table->foreignId(column: 'product_id')->constrained()->cascadeOnDelete();
            $table->integer(column: 'quantity')->default(value: 1);
            $table->decimal(column: 'unit_price', total: 12, places: 2)->default(value: 0);
            $table->decimal(column: 'line_total', total: 12, places: 2)->default(value: 0);
            $table->timestamps();
            $table->unique(columns: ['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'order_items');
        Schema::dropIfExists(table: 'orders');
    }
};