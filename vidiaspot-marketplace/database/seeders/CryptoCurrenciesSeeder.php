<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CryptoCurrency;

class CryptoCurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'slug' => 'bitcoin',
                'description' => 'Bitcoin is a decentralized digital currency, without a central bank or single administrator.',
                'price' => 43250.00,
                'market_cap' => 847500000000.00,
                'logo_url' => 'https://cryptologos.cc/logos/bitcoin-btc-logo.png',
                'is_active' => true,
            ],
            [
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'slug' => 'ethereum',
                'description' => 'Ethereum is a decentralized, open-source blockchain with smart contract functionality.',
                'price' => 2650.00,
                'market_cap' => 318500000000.00,
                'logo_url' => 'https://cryptologos.cc/logos/ethereum-eth-logo.png',
                'is_active' => true,
            ],
            [
                'name' => 'Binance Coin',
                'symbol' => 'BNB',
                'slug' => 'binance-coin',
                'description' => 'BNB is an exchange-based token created and issued by the cryptocurrency exchange Binance.',
                'price' => 315.00,
                'market_cap' => 47200000000.00,
                'logo_url' => 'https://cryptologos.cc/logos/bnb-bnb-logo.png',
                'is_active' => true,
            ],
            [
                'name' => 'Solana',
                'symbol' => 'SOL',
                'slug' => 'solana',
                'description' => 'Solana is a highly functional open source project that pairs smart contracts with a proof-of-stake blockchain.',
                'price' => 98.50,
                'market_cap' => 43500000000.00,
                'logo_url' => 'https://cryptologos.cc/logos/solana-sol-logo.png',
                'is_active' => true,
            ],
            [
                'name' => 'Ripple',
                'symbol' => 'XRP',
                'slug' => 'ripple',
                'description' => 'Ripple is a digital payment protocol and cryptocurrency that enables fast, low-cost international money transfers.',
                'price' => 0.62,
                'market_cap' => 34500000000.00,
                'logo_url' => 'https://cryptologos.cc/logos/xrp-xrp-logo.png',
                'is_active' => true,
            ],
            [
                'name' => 'Cardano',
                'symbol' => 'ADA',
                'slug' => 'cardano',
                'description' => 'Cardano is a proof-of-stake blockchain platform that was developed to provide more advanced features than any protocol previously developed.',
                'price' => 0.52,
                'market_cap' => 18500000000.00,
                'logo_url' => 'https://cryptologos.cc/logos/cardano-ada-logo.png',
                'is_active' => true,
            ],
            [
                'name' => 'Dogecoin',
                'symbol' => 'DOGE',
                'slug' => 'dogecoin',
                'description' => 'Dogecoin is a cryptocurrency created by software engineers Billy Markus and Jackson Palmer as a joke.',
                'price' => 0.08,
                'market_cap' => 11300000000.00,
                'logo_url' => 'https://cryptologos.cc/logos/dogecoin-doge-logo.png',
                'is_active' => true,
            ],
            [
                'name' => 'Polkadot',
                'symbol' => 'DOT',
                'slug' => 'polkadot',
                'description' => 'Polkadot is a sharded scalable multi-chain framework that connects multiple specialized blockchains into one network.',
                'price' => 7.25,
                'market_cap' => 9500000000.00,
                'logo_url' => 'https://cryptologos.cc/logos/polkadot-new-dot-logo.png',
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            CryptoCurrency::updateOrCreate(
                ['symbol' => $currency['symbol']],
                $currency
            );
        }
    }
}