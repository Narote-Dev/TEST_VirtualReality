<?php
// database/migrations/2024_01_01_000001_create_currencies_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id('currency_id');
            $table->string('symbol', 10)->unique(); // BTC, ETH, THB, USD
            $table->string('name', 100); // Bitcoin, Ethereum, Thai Baht
            $table->enum('type', ['fiat', 'crypto']); // ประเภทสกุลเงิน
            $table->decimal('exchange_rate', 15, 8)->default(1); // อัตราแลกเปลี่ยน
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}

// database/migrations/2024_01_01_000002_create_users_table.php
class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username', 50)->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->enum('kyc_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}

// database/migrations/2024_01_01_000003_create_wallets_table.php
class CreateWalletsTable extends Migration
{
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id('wallet_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies', 'currency_id');
            $table->decimal('balance', 20, 8)->default(0); // ยอดเงินคงเหลือ
            $table->string('wallet_address')->nullable(); // สำหรับ crypto
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // ป้องกันการสร้าง wallet ซ้ำสำหรับ user + currency เดียวกัน
            $table->unique(['user_id', 'currency_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}

// database/migrations/2024_01_01_000004_create_payment_methods_table.php
class CreatePaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_method_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->enum('method_type', ['bank_transfer', 'promptpay', 'credit_card']);
            $table->string('account_number', 50);
            $table->string('bank_name', 100)->nullable();
            $table->string('account_name', 100);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
}

// database/migrations/2024_01_01_000005_create_orders_table.php
class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('currency_from_id')->constrained('currencies', 'currency_id');
            $table->foreignId('currency_to_id')->constrained('currencies', 'currency_id');
            $table->enum('order_type', ['buy', 'sell']);
            $table->decimal('amount', 20, 8); // จำนวนที่ต้องการซื้อ/ขาย
            $table->decimal('price', 20, 8); // ราคาต่อหน่วย
            $table->decimal('filled_amount', 20, 8)->default(0); // จำนวนที่เติมแล้ว
            $table->enum('status', ['pending', 'partial', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'order_type']);
            $table->index(['currency_from_id', 'currency_to_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

// database/migrations/2024_01_01_000006_create_trades_table.php
class CreateTradesTable extends Migration
{
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id('trade_id');
            $table->foreignId('buyer_order_id')->constrained('orders', 'order_id');
            $table->foreignId('seller_order_id')->constrained('orders', 'order_id');
            $table->foreignId('buyer_id')->constrained('users', 'user_id');
            $table->foreignId('seller_id')->constrained('users', 'user_id');
            $table->foreignId('currency_id')->constrained('currencies', 'currency_id');
            $table->decimal('amount', 20, 8); // จำนวนที่ซื้อขาย
            $table->decimal('price', 20, 8); // ราคาที่ตกลงกัน
            $table->decimal('total_value', 20, 8); // มูลค่ารวม
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();
            
            $table->index(['buyer_id', 'seller_id']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('trades');
    }
}

// database/migrations/2024_01_01_000007_create_transactions_table.php
class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->foreignId('from_user_id')->nullable()->constrained('users', 'user_id');
            $table->foreignId('to_user_id')->nullable()->constrained('users', 'user_id');
            $table->foreignId('currency_id')->constrained('currencies', 'currency_id');
            $table->decimal('amount', 20, 8);
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'transfer', 'trade', 'fee']);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('external_address')->nullable(); // สำหรับโอนภายนอก
            $table->string('transaction_hash')->nullable(); // hash ของ blockchain
            $table->decimal('fee', 20, 8)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['from_user_id', 'to_user_id']);
            $table->index(['transaction_type', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}

// database/migrations/2024_01_01_000008_create_fiat_deposits_table.php
class CreateFiatDepositsTable extends Migration
{
    public function up()
    {
        Schema::create('fiat_deposits', function (Blueprint $table) {
            $table->id('deposit_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('payment_method_id')->constrained('payment_methods', 'payment_method_id');
            $table->foreignId('currency_id')->constrained('currencies', 'currency_id');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('reference_number', 100)->nullable();
            $table->string('proof_image')->nullable(); // path ของรูปหลักฐาน
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fiat_deposits');
    }
}

// database/migrations/2024_01_01_000009_create_fiat_withdrawals_table.php
class CreateFiatWithdrawalsTable extends Migration
{
    public function up()
    {
        Schema::create('fiat_withdrawals', function (Blueprint $table) {
            $table->id('withdrawal_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('payment_method_id')->constrained('payment_methods', 'payment_method_id');
            $table->foreignId('currency_id')->constrained('currencies', 'currency_id');
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected'])->default('pending');
            $table->string('reference_number', 100)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fiat_withdrawals');
    }
}