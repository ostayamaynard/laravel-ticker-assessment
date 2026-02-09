<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Events\TickerDataFetched;

class FetchTickerData extends Command
{
   
    protected $signature = 'ticker:fetch';
    protected $description = 'Fetch BTC-USDT ticker data from Coinbase API';

    public function handle()
    {
        try {
            $this->info('Fetching ticker data from Coinbase API...');
            
            // Disable SSL verification for development
            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(30)->get('https://api.exchange.coinbase.com/products/BTC-USDT/ticker');
            
            // Debug: Show response status
            $this->line('Response Status: ' . $response->status());
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Debug: Check if data is null or empty
                if ($data === null || empty($data)) {
                    $this->error('✗ API returned null or empty data');
                    $this->line('Raw Response: ' . $response->body());
                    return Command::FAILURE;
                }
                
                // Debug: Show what we received
                $this->line('Data received: ' . json_encode($data, JSON_PRETTY_PRINT));
                
                // Check if required fields exist
                $requiredFields = ['ask', 'bid', 'price', 'volume', 'trade_id', 'size', 'time'];
                $missingFields = [];
                
                foreach ($requiredFields as $field) {
                    if (!isset($data[$field])) {
                        $missingFields[] = $field;
                    }
                }
                
                if (!empty($missingFields)) {
                    $this->error('✗ Missing required fields: ' . implode(', ', $missingFields));
                    return Command::FAILURE;
                }
                
                // Dispatch event with raw data
                event(new TickerDataFetched($data));
                
                $this->info('✓ Ticker data fetched successfully at ' . now());
                $this->line('  Price: $' . number_format($data['price'], 2));
                $this->line('  Ask: $' . number_format($data['ask'], 2));
                $this->line('  Bid: $' . number_format($data['bid'], 2));
                
                return Command::SUCCESS;
            } else {
                $this->error('✗ API request failed with status: ' . $response->status());
                $this->line('Response Body: ' . $response->body());
                return Command::FAILURE;
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $this->error('✗ HTTP Request Error: ' . $e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('✗ Error fetching ticker data: ' . $e->getMessage());
            $this->line('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
