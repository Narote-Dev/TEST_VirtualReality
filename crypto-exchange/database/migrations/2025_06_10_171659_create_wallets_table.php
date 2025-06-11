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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(table: 'users', column: 'user_id')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained(table: 'currencies', column: 'currency_id')->onDelete('cascade');
            $table->decimal('balance', 20, 8)->default(0);
            $table->decimal('frozen_balance', 20, 8)->default(0);
            $table->string('wallet_address', 50)->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
