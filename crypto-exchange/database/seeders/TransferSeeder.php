<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Currency;
use App\Models\Transfer;

class TransferSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $btc = Currency::where('code', 'BTC')->first();
        $eth = Currency::where('code', 'ETH')->first();
        $thb = Currency::where('code', 'THB')->first();

        if ($users->count() < 2 || !$btc || !$eth || !$thb) {
            $this->command->error('Required data not found!');
            return;
        }

        $transfers = [
            // Internal transfers (between users in the system)
            [
                'from_user_id' => $users[0]->user_id,
                'to_user_id' => $users[1]->user_id,
                'currency_id' => $btc->currency_id,
                'amount' => 0.1,
                'fee_amount' => 0.0001,
                'type' => 'internal',
                'external_address' => null,
                'transaction_hash' => $this->generateTransactionHash(),
                'status' => 'completed',
                'notes' => 'Payment for services',
            ],
            [
                'from_user_id' => $users[1]->user_id,
                'to_user_id' => $users[2]->user_id,
                'currency_id' => $eth->currency_id,
                'amount' => 0.5,
                'fee_amount' => 0.002,
                'type' => 'internal',
                'external_address' => null,
                'transaction_hash' => $this->generateTransactionHash(),
                'status' => 'completed',
                'notes' => 'Loan repayment',
            ],
            [
                'from_user_id' => $users[2]->user_id,
                'to_user_id' => $users[3]->user_id,
                'currency_id' => $thb->currency_id,
                'amount' => 5000,
                'fee_amount' => 10,
                'type' => 'internal',
                'external_address' => null,
                'transaction_hash' => null, // Internal THB transfer
                'status' => 'completed',
                'notes' => 'Birthday gift',
            ],
            [
                'from_user_id' => $users[0]->user_id,
                'to_user_id' => $users[3]->user_id,
                'currency_id' => $btc->currency_id,
                'amount' => 0.02,
                'fee_amount' => 0.0001,
                'type' => 'internal',
                'external_address' => null,
                'transaction_hash' => $this->generateTransactionHash(),
                'status' => 'pending',
                'notes' => 'Pending internal transfer',
            ],
            
            // External transfers (to external wallet addresses)
            [
                'from_user_id' => $users[1]->user_id,
                'to_user_id' => null,
                'currency_id' => $btc->currency_id,
                'amount' => 0.25,
                'fee_amount' => 0.0005,
                'type' => 'external',
                'external_address' => '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa',
                'transaction_hash' => $this->generateTransactionHash(),
                'status' => 'completed',
                'notes' => 'Withdrawal to external wallet',
            ],
            [
                'from_user_id' => $users[2]->user_id,
                'to_user_id' => null,
                'currency_id' => $eth->currency_id,
                'amount' => 1.0,
                'fee_amount' => 0.01,
                'type' => 'external',
                'external_address' => '0x742d35Cc6634C0532925a3b8D6Ac6d3fa2d3248f',
                'transaction_hash' => $this->generateTransactionHash(),
                'status' => 'completed',
                'notes' => 'Transfer to DeFi protocol',
            ],
            [
                'from_user_id' => $users[3]->user_id,
                'to_user_id' => null,
                'currency_id' => $btc->currency_id,
                'amount' => 0.05,
                'fee_amount' => 0.0002,
                'type' => 'external',
                'external_address' => '3J98t1WpEZ73CNmQviecrnyiWrnqRhWNLy',
                'transaction_hash' => null,
                'status' => 'pending',
                'notes' => 'Pending external withdrawal',
            ],
            [
                'from_user_id' => $users[0]->user_id,
                'to_user_id' => null,
                'currency_id' => $eth->currency_id,
                'amount' => 0.3,
                'fee_amount' => 0.005,
                'type' => 'external',
                'external_address' => '0xdAC17F958D2ee523a2206206994597C13D831ec7',
                'transaction_hash' => null,
                'status' => 'cancelled',
                'notes' => 'Failed external transfer - insufficient gas',
            ],

            // Incoming transfers (deposits from external sources)
            [
                'from_user_id' => null,
                'to_user_id' => $users[1]->user_id,
                'currency_id' => $btc->currency_id,
                'amount' => 0.15,
                'fee_amount' => 0,
                'type' => 'external',
                'external_address' => '1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2',
                'transaction_hash' => $this->generateTransactionHash(),
                'status' => 'completed',
                'notes' => 'Deposit from external wallet',
            ],
            [
                'from_user_id' => null,
                'to_user_id' => $users[3]->user_id,
                'currency_id' => $eth->currency_id,
                'amount' => 2.0,
                'fee_amount' => 0,
                'type' => 'external',
                'external_address' => '0x95aD61b0a150d79219dCF64E1E6Cc01f0B64C4cE',
                'transaction_hash' => $this->generateTransactionHash(),
                'status' => 'completed',
                'notes' => 'Deposit from exchange',
            ],
        ];

        foreach ($transfers as $transferData) {
            Transfer::create($transferData);
        }

        $this->command->info('Transfers seeded successfully!');
    }

    private function generateTransactionHash()
    {
        return '0x' . bin2hex(random_bytes(32));
    }
}