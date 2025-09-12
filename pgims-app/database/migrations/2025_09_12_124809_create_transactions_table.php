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
        Schema::create(table: 'transactions', callback: function (Blueprint $table): void {
            $table->id();
            $table->foreignId(column: 'bank_account_id')->constrained()->onDelete(action: 'cascade');
            $table->enum(column: 'type', allowed: ['credit', 'debit']);
            $table->decimal(column: 'amount', total: 15, places: 2);
            $table->string(column: 'reference')->nullable();
            $table->text(column: 'description')->nullable();
            $table->date(column: 'transaction_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'transactions');
    }
};
