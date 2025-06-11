<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Currency;
use App\Models\Wallet;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Get all currencies
        $currencies = Currency::all();

        // Create test users
        $users = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '+66812345678',
                'password' => Hash::make('password123'),
                'kyc_status' => 'verified',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@example.com',
                'phone' => '+66823456789',
                'password' => Hash::make('password123'),
                'kyc_status' => 'verified',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'email' => 'bob@example.com',
                'phone' => '+66834567890',
                'password' => Hash::make('password123'),
                'kyc_status' => 'pending',
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Wilson',
                'email' => 'alice@example.com',
                'phone' => '+66845678901',
                'password' => Hash::make('password123'),
                'kyc_status' => 'verified',
                'is_verified' => true,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Create wallets for each currency
            foreach ($currencies as $currency) {
                $balance = 0;

                // Set some initial balances for testing
                if ($currency->type === 'fiat') {
                    switch ($currency->code) {
                        case 'THB':
                            $balance = rand(50000, 500000); // 50k - 500k THB
                            break;
                        case 'USD':
                            $balance = rand(1000, 10000); // 1k - 10k USD
                            break;
                    }
                } else {
                    // Crypto balances
                    switch ($currency->code) {
                        case 'BTC':
                            $balance = rand(1, 100) / 100; // 0.01 - 1 BTC
                            break;
                        case 'ETH':
                            $balance = rand(10, 1000) / 100; // 0.1 - 10 ETH
                            break;
                        case 'XRP':
                            $balance = rand(100, 10000); // 100 - 10k XRP
                            break;
                        case 'DOGE':
                            $balance = rand(1000, 100000); // 1k - 100k DOGE
                            break;
                    }
                }

                Wallet::updateOrCreate(
                    [
                        'user_id' => $user->user_id,
                        'currency_id' => $currency->currency_id,
                    ],
                    [
                        'balance' => $balance,
                        'frozen_balance' => 0,
                        'wallet_address' => $currency->type === 'crypto' 
                            ? $this->generateWalletAddress($currency->code) 
                            : null,
                    ]
                );
            }
        }

        $this->command->info('Users and wallets seeded successfully!');
    }

    private function generateWalletAddress($currencyCode)
    {
        $prefixes = [
            'BTC' => ['1', '3', 'bc1'],
            'ETH' => ['0x'],
            'XRP' => ['r'],
            'DOGE' => ['D'],
        ];

        $prefix = $prefixes[$currencyCode][0] ?? '';
        $randomString = substr(md5(uniqid()), 0, 30);
        
        return $prefix . $randomString;
    }
}