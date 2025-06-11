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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->nullable()->constrained(table: 'users', column: 'user_id')->onDelete('cascade');
            $table->foreignId('to_user_id')->nullable()->constrained(table: 'users', column: 'user_id')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained(table: 'currencies', column: 'currency_id')->onDelete('cascade');
            $table->decimal('amount', 20, 8);
            $table->decimal('fee_amount', 20, 8);
            $table->string('type');  // หรือ enum ตามที่ออกแบบ
            $table->string('external_address')->nullable();
            $table->string('transaction_hash')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
