<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        $currencies = [
            // Fiat Currencies
            [
                'code' => 'THB',
                'name' => 'Thai Baht',
                'type' => 'fiat',
                'symbol' => '฿',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'type' => 'fiat',
                'symbol' => '$',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            // Cryptocurrencies
            [
                'code' => 'BTC',
                'name' => 'Bitcoin',
                'type' => 'crypto',
                'symbol' => '₿',
                'decimal_places' => 8,
                'is_active' => true,
            ],
            [
                'code' => 'ETH',
                'name' => 'Ethereum',
                'type' => 'crypto',
                'symbol' => 'Ξ',
                'decimal_places' => 8,
                'is_active' => true,
            ],
            [
                'code' => 'XRP',
                'name' => 'Ripple',
                'type' => 'crypto',
                'symbol' => 'XRP',
                'decimal_places' => 6,
                'is_active' => true,
            ],
            [
                'code' => 'DOGE',
                'name' => 'Dogecoin',
                'type' => 'crypto',
                'symbol' => 'Ð',
                'decimal_places' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }

        $this->command->info('Currencies seeded successfully!');
    }
}