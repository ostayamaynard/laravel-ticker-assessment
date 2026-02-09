<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TickerData extends Model
{
    protected $fillable = [
        'ask',
        'bid',
        'volume',
        'trade_id',
        'price',
        'size',
        'time',
        'ask_bid_diff',
        'ask_last_diff',
        'bid_last_diff',
    ];

    protected $casts = [
        'time' => 'datetime',
        'ask' => 'decimal:8',
        'bid' => 'decimal:8',
        'price' => 'decimal:8',
        'volume' => 'decimal:8',
        'size' => 'decimal:8',
        'ask_bid_diff' => 'decimal:8',
        'ask_last_diff' => 'decimal:8',
        'bid_last_diff' => 'decimal:8',
    ];
}
