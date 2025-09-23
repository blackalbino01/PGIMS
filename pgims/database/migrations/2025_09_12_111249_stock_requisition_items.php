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
        Schema::create(table: 'stock_requisition_items', callback: function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'stock_requisition_id')->constrained()->onDelete(action: 'cascade');
            $table->foreignId(column: 'product_id')->constrained()->onDelete(action: 'cascade');
            $table->integer(column: 'quantity');
            $table->timestamps();

            $table->unique(columns: ['stock_requisition_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'stock_requisition_items');
    }
};