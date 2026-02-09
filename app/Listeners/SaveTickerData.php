<?php

namespace App\Listeners;

use App\Events\TickerDataFetched;
use App\Models\TickerData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SaveTickerData implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TickerDataFetched $event): void
    {
        try {
            $data = $event->tickerData;
            
            // Extract values
            $ask = (float) $data['ask'];
            $bid = (float) $data['bid'];
            $lastPrice = (float) $data['price'];
            
            // Perform calculations
            $askBidDiff = $ask - $bid;
            $askLastDiff = $ask - $lastPrice;
            $bidLastDiff = $bid - $lastPrice;
            
            // Save to database
            TickerData::create([
                'ask' => $ask,
                'bid' => $bid,
                'volume' => $data['volume'],
                'trade_id' => $data['trade_id'],
                'price' => $lastPrice,
                'size' => $data['size'],
                'time' => Carbon::parse($data['time']),
                'ask_bid_diff' => $askBidDiff,
                'ask_last_diff' => $askLastDiff,
                'bid_last_diff' => $bidLastDiff,
            ]);
            
            Log::info('Ticker data saved successfully', [
                'price' => $lastPrice,
                'time' => $data['time']
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save ticker data: ' . $e->getMessage());
            throw $e; // Re-throw to trigger queue retry
        }
    }
}