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
        Schema::create(table: 'suppliers', callback: function (Blueprint $table): void {
            $table->id();
            $table->string(column: 'name')->unique();
            $table->string(column: 'email')->nullable()->unique();
            $table->string(column: 'phone')->nullable();
            $table->text(column: 'address')->nullable();
            $table->text(column: 'description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'suppliers');
    }
};