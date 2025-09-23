<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(table: 'customers', callback: function (Blueprint $table): void {
            $table->id();
            $table->string(column: 'name');
            $table->string(column: 'gender')->nullable();
            $table->string(column: 'phone')->nullable();
            $table->string(column: 'email')->unique()->nullable();
            $table->text(column: 'address')->nullable();
            $table->date(column: 'birthday')->nullable();
            $table->decimal(column: 'balance', total: 15, places: 2)->default(value: 0);
            $table->decimal(column: 'credit_limit', total: 15, places: 2)->default(value: 0);
            $table->text(column: 'notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(table: 'customers');
    }
};