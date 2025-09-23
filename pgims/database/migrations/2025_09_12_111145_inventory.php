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
        Schema::create(table: 'inventory', callback: function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'store_id')->constrained()->onDelete(action: 'cascade');
            $table->foreignId(column: 'product_id')->constrained()->onDelete(action: 'cascade');
            $table->integer(column: 'quantity')->default(value: 0);
            $table->timestamps();

            $table->unique(columns: ['store_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'inventory');
    }
};
