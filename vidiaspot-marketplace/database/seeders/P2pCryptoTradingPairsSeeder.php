<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CryptoCurrency;
use App\Models\P2pCryptoTradingPair;

class P2pCryptoTradingPairsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define base currencies
        $btc = CryptoCurrency::where('symbol', 'BTC')->first();
        $eth = CryptoCurrency::where('symbol', 'ETH')->first();
        $sol = CryptoCurrency::where('symbol', 'SOL')->first();
        $usdt = CryptoCurrency::where('symbol', 'USDT')->first();
        $usdc = CryptoCurrency::where('symbol', 'USDC')->first();
        
        if (!$btc || !$eth || !$sol || !$usdt || !$usdc) {
            $this->command->info('Please run CryptoCurrenciesSeeder first to create required currencies');
            return;
        }

        $tradingPairs = [
            // BTC pairs
            [
                'base_currency_id' => $btc->id,
                'quote_currency_id' => $usdt->id,
                'pair_name' => 'BTC/USDT',
                'symbol' => 'BTCUSDT',
                'min_price' => 0.01,
                'max_price' => 100000.00,
                'min_quantity' => 0.00001,
                'max_quantity' => 100.0,
                'price_tick_size' => 0.01,
                'quantity_step_size' => 0.00001,
                'status' => 'active',
                'is_active' => true,
            ],
            [
                'base_currency_id' => $btc->id,
                'quote_currency_id' => $usdc->id,
                'pair_name' => 'BTC/USDC',
                'symbol' => 'BTCUSDC',
                'min_price' => 0.01,
                'max_price' => 100000.00,
                'min_quantity' => 0.00001,
                'max_quantity' => 100.0,
                'price_tick_size' => 0.01,
                'quantity_step_size' => 0.00001,
                'status' => 'active',
                'is_active' => true,
            ],
            
            // ETH pairs
            [
                'base_currency_id' => $eth->id,
                'quote_currency_id' => $usdt->id,
                'pair_name' => 'ETH/USDT',
                'symbol' => 'ETHUSDT',
                'min_price' => 0.01,
                'max_price' => 10000.00,
                'min_quantity' => 0.001,
                'max_quantity' => 1000.0,
                'price_tick_size' => 0.01,
                'quantity_step_size' => 0.001,
                'status' => 'active',
                'is_active' => true,
            ],
            [
                'base_currency_id' => $eth->id,
                'quote_currency_id' => $usdc->id,
                'pair_name' => 'ETH/USDC',
                'symbol' => 'ETHUSDC',
                'min_price' => 0.01,
                'max_price' => 10000.00,
                'min_quantity' => 0.001,
                'max_quantity' => 1000.0,
                'price_tick_size' => 0.01,
                'quantity_step_size' => 0.001,
                'status' => 'active',
                'is_active' => true,
            ],
            
            // SOL pairs
            [
                'base_currency_id' => $sol->id,
                'quote_currency_id' => $usdt->id,
                'pair_name' => 'SOL/USDT',
                'symbol' => 'SOLUSDT',
                'min_price' => 0.001,
                'max_price' => 1000.00,
                'min_quantity' => 0.01,
                'max_quantity' => 10000.0,
                'price_tick_size' => 0.001,
                'quantity_step_size' => 0.01,
                'status' => 'active',
                'is_active' => true,
            ],
            [
                'base_currency_id' => $sol->id,
                'quote_currency_id' => $usdc->id,
                'pair_name' => 'SOL/USDC',
                'symbol' => 'SOLUSDC',
                'min_price' => 0.001,
                'max_price' => 1000.00,
                'min_quantity' => 0.01,
                'max_quantity' => 10000.0,
                'price_tick_size' => 0.001,
                'quantity_step_size' => 0.01,
                'status' => 'active',
                'is_active' => true,
            ],
        ];

        foreach ($tradingPairs as $pair) {
            P2pCryptoTradingPair::updateOrCreate(
                [
                    'pair_name' => $pair['pair_name'],
                ],
                $pair
            );
        }
    }
}