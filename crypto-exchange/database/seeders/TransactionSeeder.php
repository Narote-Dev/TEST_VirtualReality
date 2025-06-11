<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Currency;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $buyer = User::first();
        $seller = User::skip(1)->first();
        $currency = Currency::first();

        Transaction::create([
            'buyer_id' => $buyer->user_id,
            'seller_id' => $seller->user_id,
            'currency_id' => $currency->currency_id,
            'amount' => 0.5,
            'price' => 1000000,
            'total_amount' => 500000,
            'fee_amount' => 500,
            'status' => 'completed',
        ]);
    }
}
