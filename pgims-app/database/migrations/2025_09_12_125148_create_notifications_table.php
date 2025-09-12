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
        Schema::create(table: 'notifications', callback: function (Blueprint $table): void {
            $table->id();
            $table->string(column: 'type');
            $table->string(column: 'title');
            $table->text(column: 'message');
            $table->boolean(column: 'is_read')->default(value: false);
            $table->morphs(name: 'notifiable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(table: 'notifications');
    }
};