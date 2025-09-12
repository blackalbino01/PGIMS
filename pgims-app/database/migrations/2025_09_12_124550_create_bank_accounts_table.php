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
        Schema::create(table: 'bank_accounts', callback: function (Blueprint $table): void {
            $table->id();
            $table->string(column: 'bank_name');
            $table->string(column: 'account_number')->unique();
            $table->string(column: 'account_name');
            $table->string(column: 'branch')->nullable();
            $table->string(column: 'account_type')->nullable();
            $table->decimal(column: 'balance', total: 15, places: 2)->default(value: 0);
            $table->text(column: 'description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'bank_accounts');
    }
};
