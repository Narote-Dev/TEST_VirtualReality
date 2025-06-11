<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Currency;
use App\Models\Order;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $btc = Currency::where('code', 'BTC')->first();
        $eth = Currency::where('code', 'ETH')->first();
        $thb = Currency::where('code', 'THB')->first();
        $usd = Currency::where('code', 'USD')->first();

        if (!$btc || !$eth || !$thb || !$usd || $users->count() < 2) {
            $this->command->error('Required currencies or users not found!');
            return;
        }

        // Sample orders
        $orders = [
            // BTC Buy Orders
            [
                'buyer_id' => $users[0]->user_id,
                'seller_id' => null, // Market order
                'currency_id' => $btc->currency_id,
                'payment_currency_id' => $thb->id,
                'type' => 'buy',
                'amount' => 0.5,
                'price' => 1500000, // 1.5M THB per BTC
                'total_amount' => 750000,
                'filled_amount' => 0,
                'status' => 'active',
                'expires_at' => Carbon::now()->addDays(7),
            ],
            [
                'buyer_id' => $users[1]->user_id,
                'seller_id' => null,
                'currency_id' => $btc->currency_id,
                'payment_currency_id' => $usd->id,
                'type' => 'buy',
                'amount' => 0.25,
                'price' => 45000, // 45k USD per BTC
                'total_amount' => 11250,
                'filled_amount' => 0,
                'status' => 'active',
                'expires_at' => Carbon::now()->addDays(5),
            ],
            // BTC Sell Orders
            [
                'buyer_id' => null,
                'seller_id' => $users[2]->user_id,
                'currency_id' => $btc->currency_id,
                'payment_currency_id' => $thb->id,
                'type' => 'sell',
                'amount' => 0.3,
                'price' => 1520000, // 1.52M THB per BTC
                'total_amount' => 456000,
                'filled_amount' => 0,
                'status' => 'active',
                'expires_at' => Carbon::now()->addDays(10),
            ],
            // ETH Orders
            [
                'buyer_id' => $users[3]->user_id,
                'seller_id' => null,
                'currency_id' => $eth->currency_id,
                'payment_currency_id' => $thb->id,
                'type' => 'buy',
                'amount' => 2.0,
                'price' => 80000, // 80k THB per ETH
                'total_amount' => 160000,
                'filled_amount' => 0,
                'status' => 'active',
                'expires_at' => Carbon::now()->addDays(3),
            ],
            [
                'buyer_id' => null,
                'seller_id' => $users[0]->user_id,
                'currency_id' => $eth->currency_id,
                'payment_currency_id' => $usd->id,
                'type' => 'sell',
                'amount' => 1.5,
                'price' => 2500, // 2.5k USD per ETH
                'total_amount' => 3750,
                'filled_amount' => 0,
                'status' => 'active',
                'expires_at' => Carbon::now()->addDays(14),
            ],
            // Partially filled order
            [
                'buyer_id' => $users[1]->user_id,
                'seller_id' => null,
                'currency_id' => $btc->currency_id,
                'payment_currency_id' => $thb->id,
                'type' => 'buy',
                'amount' => 1.0,
                'price' => 1480000,
                'total_amount' => 1480000,
                'filled_amount' => 0.3, // 30% filled
                'status' => 'partial',
                'expires_at' => Carbon::now()->addDays(7),
            ],
            // Completed order
            [
                'buyer_id' => $users[2]->user_id,
                'seller_id' => $users[3]->user_id,
                'currency_id' => $eth->currency_id,
                'payment_currency_id' => $thb->id,
                'type' => 'buy',
                'amount' => 0.5,
                'price' => 82000,
                'total_amount' => 41000,
                'filled_amount' => 0.5, // 100% filled
                'status' => 'completed',
                'expires_at' => Carbon::now()->addDays(1),
            ]
        ];
    }
}