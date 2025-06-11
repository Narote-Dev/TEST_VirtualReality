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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('buyer_id')->constrained(table: 'users', column: 'user_id')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained(table: 'users', column: 'user_id')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained(table: 'currencies', column: 'currency_id')->onDelete('cascade');
            $table->decimal('amount', 20, 8);
            $table->decimal('price', 20, 8);
            $table->decimal('total_amount', 20, 8);
            $table->decimal('fee_amount', 20, 8);
            $table->enum('status', ['pending', 'completed', 'cancelled']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
