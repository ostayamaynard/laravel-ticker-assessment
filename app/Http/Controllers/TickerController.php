<?php

namespace App\Http\Controllers;

use App\Models\TickerData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TickerController extends Controller
{
    public function index()
    {
        return view('ticker.search');
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => 'End date must be equal to or after start date.',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        // Check if we have any data in this range
        $hasData = TickerData::whereBetween('time', [$startDate, $endDate])->exists();

        if (!$hasData) {
            return view('ticker.results', [
                'lowestBid' => null,
                'lowestPrice' => null,
                'highestPrice' => null,
                'highestAsk' => null,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);
        }

        // Get statistics
        $lowestBid = TickerData::whereBetween('time', [$startDate, $endDate])
            ->orderBy('bid', 'asc')
            ->first();
            
        $lowestPrice = TickerData::whereBetween('time', [$startDate, $endDate])
            ->orderBy('price', 'asc')
            ->first();
            
        $highestPrice = TickerData::whereBetween('time', [$startDate, $endDate])
            ->orderBy('price', 'desc')
            ->first();
            
        $highestAsk = TickerData::whereBetween('time', [$startDate, $endDate])
            ->orderBy('ask', 'desc')
            ->first();

        return view('ticker.results', compact(
            'lowestBid',
            'lowestPrice',
            'highestPrice',
            'highestAsk',
            'startDate',
            'endDate'
        ));
    }
}
