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
        Schema::create(table: 'stores', callback: function (Blueprint $table): void {
            $table->id();
            $table->string(column: 'name')->unique();
            $table->string(column: 'address')->nullable();
            $table->string(column: 'phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'stores');
    }
};
