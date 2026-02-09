<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\TickerDataFetched;

class FetchTickerDataDemo extends Command
{
    // This is a demo version that simulates the Coinbase API response
    // The production version (main branch) uses the real API
    protected $signature = 'ticker:fetch-demo';
    protected $description = 'Fetch BTC-USDT ticker data (Demo version with mock data)';

    public function handle()
    {
        $this->info('==================================================');
        $this->info('DEMO MODE: Generating realistic BTC-USDT ticker data');
        $this->info('==================================================');
        $this->line('');
        
        // Generate realistic mock data that matches the exact Coinbase API structure
        $basePrice = rand(95000, 105000); // Current BTC price range
        $spreadPercent = rand(10, 30) / 10000; // 0.001% - 0.003% typical spread
        $spread = $basePrice * $spreadPercent;
        
        $price = $basePrice + rand(-500, 500); // Add market variance
        $ask = $price + ($spread / 2);
        $bid = $price - ($spread / 2);
        
        // Create data structure matching Coinbase API exactly
        // This matches the structure from: https://api.exchange.coinbase.com/products/BTC-USDT/ticker
        $data = [
            'ask' => (string) round($ask, 2),
            'bid' => (string) round($bid, 2),
            'price' => (string) round($price, 2), // This is lastPrice in the API
            'volume' => (string) rand(10000, 50000),
            'trade_id' => (string) (time() . rand(1000, 9999)),
            'size' => (string) (rand(1, 1000) / 100),
            'time' => now()->toIso8601String(),
        ];
        
        // Display what we generated
        $this->line('Generated Mock Data (matches real API structure):');
        $this->line('  Price (lastPrice): $' . number_format($data['price'], 2));
        $this->line('  Ask: $' . number_format($data['ask'], 2));
        $this->line('  Bid: $' . number_format($data['bid'], 2));
        $this->line('  Spread: $' . number_format($ask - $bid, 2));
        $this->line('  Volume: ' . number_format($data['volume'], 2));
        $this->line('  Trade ID: ' . $data['trade_id']);
        $this->line('  Time: ' . $data['time']);
        $this->line('');
        
        // Trigger the same event as the production version
        // This proves the queue/listener system works identically
        event(new TickerDataFetched($data));
        
        $this->info('✓ Demo ticker data generated and queued successfully!');
        $this->line('');
        $this->line('Next steps:');
        $this->line('  1. Check your queue:work terminal to see the listener processing');
        $this->line('  2. Verify data saved: php artisan tinker -> App\Models\TickerData::latest()->first()');
        $this->line('  3. View in browser: http://localhost:8000');
        $this->line('');
        
        return Command::SUCCESS;
    }
}