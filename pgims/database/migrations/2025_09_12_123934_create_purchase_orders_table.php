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
        Schema::create(table: 'purchase_orders', callback: function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'supplier_id')->constrained()->onDelete(action: 'cascade');
            $table->string(column: 'order_number')->unique();
            $table->enum(column: 'status', allowed: ['pending', 'approved', 'received', 'cancelled'])->default(value: 'pending');
            $table->decimal(column: 'total_amount', total: 12, places: 2)->default(value: 0);
            $table->date(column: 'order_date')->nullable();
            $table->date(column: 'expected_date')->nullable();
            $table->text(column: 'notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'purchase_orders');
    }
};
