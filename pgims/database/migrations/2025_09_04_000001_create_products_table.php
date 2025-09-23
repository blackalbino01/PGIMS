<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'products', callback: function (Blueprint $table): void {
            $table->id();
            $table->string(column: 'sku')->nullable()->index();
            $table->string(column: 'name');
            $table->text(column: 'description')->nullable();
            $table->decimal(column: 'price', total: 12, places: 2)->default(value: 0);
            $table->integer(column: 'stock')->default(value: 0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'products');
    }
};
