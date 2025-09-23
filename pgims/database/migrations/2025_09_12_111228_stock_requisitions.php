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
        Schema::create(table: 'stock_requisitions', callback: function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'from_store_id')->constrained(table: 'stores')->onDelete(action: 'cascade');
            $table->foreignId(column: 'to_store_id')->constrained(table: 'stores')->onDelete(action: 'cascade');
            $table->enum(column: 'status', allowed: ['pending', 'approved', 'rejected', 'completed'])->default(value: 'pending');
            $table->foreignId(column: 'approved_by')->nullable()->constrained(table: 'users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'stock_requisitions');
    }
};